<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use App\Models\Goal;
use App\Models\Ong;
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OngPetTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private Ong $ong;

    private function setUpAs(string $role = 'member', string $status = 'verified'): void
    {
        $user = $this->loginAsTutor();
        $this->ong = $this->makeOng(['status' => $status]);
        $this->joinOng($this->ong, $user, $role);
    }

    private function url(string $suffix = ''): string
    {
        return "/api/v1/my-ongs/{$this->ong->uuid}/pets{$suffix}";
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/my-ongs/'.fake()->uuid().'/pets')->assertUnauthorized();
        $this->postJson('/api/v1/my-ongs/'.fake()->uuid().'/pets', [])->assertUnauthorized();
    }

    public function test_any_member_creates_a_pet_forced_to_adoption_for_the_ong(): void
    {
        $this->setUpAs('member');

        $response = $this->postJson($this->url(), $this->petPayload(['name' => 'Bidu', 'goal_id' => Goal::where('slug', 'breeding')->value('id'), 'ong_id' => 999, 'user_id' => 999, 'is_active' => false]))
            ->assertCreated()
            ->assertJsonPath('data.name', 'Bidu')
            ->assertJsonPath('data.goal.name', 'Adoção')
            ->assertJsonPath('data.is_active', true);

        $pet = Pet::where('uuid', $response->json('data.uuid'))->firstOrFail();
        $this->assertSame($this->ong->id, $pet->ong_id);
        $this->assertSame($this->tutor->id, $pet->user_id);
        $this->assertSame(Goal::where('slug', 'adoption')->value('id'), $pet->goal_id);
    }

    #[DataProvider('unverifiedStatuses')]
    public function test_cannot_create_when_the_ong_is_not_verified(string $status): void
    {
        $this->setUpAs('admin', $status);

        $this->postJson($this->url(), $this->petPayload())
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A ONG precisa estar verificada para publicar.');

        $this->assertSame(0, Pet::count());
    }

    public static function unverifiedStatuses(): array
    {
        return [['pending'], ['suspended']];
    }

    public function test_non_members_cannot_list_or_create(): void
    {
        $this->loginAsTutor();
        $this->ong = $this->makeOng();

        $this->getJson($this->url())->assertNotFound();
        $this->postJson($this->url(), $this->petPayload())->assertNotFound();
        $this->assertSame(0, Pet::count());
    }

    public function test_validates_pet_data_and_breed_species(): void
    {
        $this->setUpAs();

        $this->postJson($this->url(), [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'gender', 'species_id'], 'errors');
        $this->postJson($this->url(), $this->petPayload(['species_id' => 9999]))->assertUnprocessable();
    }

    public function test_lists_all_pets_of_the_ong_including_inactive_with_filters(): void
    {
        $this->setUpAs();
        $other = $this->makeUser('outro@example.com');
        $this->makeAdoptionPetFor($other, ['name' => 'Bidu', 'ong_id' => $this->ong->id]);
        $this->makeAdoptionPetFor($other, ['name' => 'Nina', 'ong_id' => $this->ong->id, 'is_active' => false]);
        $this->makeAdoptionPetFor($other, ['name' => 'De Outra', 'ong_id' => $this->makeOng()->id]);
        $this->makeAdoptionPetFor($this->tutor, ['name' => 'Particular']);

        $names = fn ($r) => collect($r->json('data'))->pluck('name')->all();

        $this->assertEqualsCanonicalizing(['Bidu', 'Nina'], $names($this->getJson($this->url())->assertOk()));
        $this->assertSame(['Nina'], $names($this->getJson($this->url('?filters[is_active]=0'))));
        $this->assertSame(['Bidu'], $names($this->getJson($this->url('?search=bid'))));
        $this->getJson($this->url('?per_page=1'))->assertJsonPath('meta.total', 2);
    }

    public function test_members_manage_ong_pets_through_the_pet_endpoints(): void
    {
        Storage::fake(config('filesystems.media'));
        $this->setUpAs('member');
        $creator = $this->makeUser('criador@example.com');
        $pet = $this->makeAdoptionPetFor($creator, ['name' => 'Bidu', 'ong_id' => $this->ong->id]);

        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertOk()->assertJsonPath('data.name', 'Bidu');
        $this->putJson("/api/v1/pets/{$pet->uuid}", ['bio' => 'Resgatado'])->assertOk()->assertJsonPath('data.bio', 'Resgatado');
        $this->post("/api/v1/pets/{$pet->uuid}/photos", ['photo' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json'])->assertCreated();
        $this->postJson("/api/v1/pets/{$pet->uuid}/vaccines", ['name' => 'V10', 'applied_at' => '2026-01-10'])->assertCreated();
        $this->deleteJson("/api/v1/pets/{$pet->uuid}")->assertOk();
    }

    public function test_non_members_cannot_manage_ong_pets_and_leaving_removes_access(): void
    {
        $this->setUpAs('member');
        $this->joinOng($this->ong, $this->makeUser('admin@example.com'), 'admin');
        $pet = $this->makeAdoptionPetFor($this->makeUser('criador@example.com'), ['ong_id' => $this->ong->id]);
        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertOk();

        $this->ong->users()->detach($this->tutor->id);

        $this->getJson("/api/v1/pets/{$pet->uuid}")->assertNotFound();
        $this->putJson("/api/v1/pets/{$pet->uuid}", ['bio' => 'x'])->assertNotFound();
        $this->deleteJson("/api/v1/pets/{$pet->uuid}")->assertNotFound();
    }

    public function test_personal_pet_list_does_not_include_ong_pets_of_other_members(): void
    {
        $this->setUpAs('member');
        $this->makeAdoptionPetFor($this->makeUser('criador@example.com'), ['name' => 'Da ONG', 'ong_id' => $this->ong->id]);
        $this->makePetFor($this->tutor, ['name' => 'Meu']);

        $names = collect($this->getJson('/api/v1/pets')->json('data'))->pluck('name')->all();

        $this->assertSame(['Meu'], $names);
    }
}
