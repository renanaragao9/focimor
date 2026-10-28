<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OngPublicTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private function names($response): array
    {
        return collect($response->json('data'))->pluck('name')->all();
    }

    public function test_is_public(): void
    {
        $ong = $this->makeOng();

        $this->getJson('/api/v1/ongs')->assertOk();
        $this->getJson("/api/v1/ongs/{$ong->uuid}")->assertOk();
        $this->getJson("/api/v1/ongs/{$ong->uuid}/pets")->assertOk();
        $this->getJson("/api/v1/ongs/{$ong->uuid}/posts")->assertOk();
        $this->getJson('/api/v1/ongs/'.fake()->uuid())->assertNotFound();
    }

    public function test_lists_only_verified_ongs_without_private_data(): void
    {
        $this->loginAsTutor();
        $this->makeOng(['name' => 'Patas', 'cnpj' => '12.345.678/0001-90', 'latitude' => -23.5, 'longitude' => -46.6, 'pix_key' => 'patas@pix.com']);
        $this->makeOng(['name' => 'Pendente', 'status' => 'pending']);
        $this->makeOng(['name' => 'Suspensa', 'status' => 'suspended']);

        $response = $this->getJson('/api/v1/ongs')->assertOk();

        $this->assertSame(['Patas'], $this->names($response));
        $item = $response->json('data.0');
        $this->assertSame('patas@pix.com', $item['pix_key']);
        $this->assertTrue($item['is_verified']);
        foreach (['id', 'cnpj', 'latitude', 'longitude', 'status'] as $key) {
            $this->assertArrayNotHasKey($key, $item);
        }
    }

    public function test_search_filters_sorting_and_adoptable_pets_count(): void
    {
        $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $zeta = $this->makeOng(['name' => 'Zeta', 'state' => 'SP', 'city' => 'Campinas']);
        $alfa = $this->makeOng(['name' => 'Alfa', 'state' => 'RJ', 'city' => 'Niterói']);
        $this->makeAdoptionPetFor($owner, ['ong_id' => $zeta->id]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Inativo', 'ong_id' => $zeta->id, 'is_active' => false]);
        $this->makePetFor($owner, ['name' => 'Pet de Match', 'ong_id' => $zeta->id]);

        $this->assertSame(['Alfa', 'Zeta'], $this->names($this->getJson('/api/v1/ongs')));
        $this->assertSame(['Zeta', 'Alfa'], $this->names($this->getJson('/api/v1/ongs?sort=name&direction=desc')));
        $this->assertSame(['Zeta'], $this->names($this->getJson('/api/v1/ongs?search=zet')));
        $this->assertSame(['Alfa'], $this->names($this->getJson('/api/v1/ongs?filters[state]=rj')));
        $this->assertSame(['Zeta'], $this->names($this->getJson('/api/v1/ongs?filters[city]=camp')));
        $this->assertSame(1, collect($this->getJson('/api/v1/ongs')->json('data'))->firstWhere('name', 'Zeta')['adoptable_pets_count']);
        $this->getJson('/api/v1/ongs?per_page=1')->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 2);
    }

    public function test_show_returns_verified_ong_and_hides_the_others(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng(['name' => 'Patas', 'donation_info' => 'Aceitamos ração']);
        $pending = $this->makeOng(['status' => 'pending']);
        $suspended = $this->makeOng(['status' => 'suspended']);

        $this->getJson("/api/v1/ongs/{$ong->uuid}")->assertOk()->assertJsonPath('data.name', 'Patas')->assertJsonPath('data.donation_info', 'Aceitamos ração');
        $this->getJson("/api/v1/ongs/{$pending->uuid}")->assertNotFound();
        $this->getJson("/api/v1/ongs/{$suspended->uuid}")->assertNotFound();
        $this->getJson("/api/v1/ongs/{$pending->uuid}/pets")->assertNotFound();
        $this->getJson("/api/v1/ongs/{$pending->uuid}/posts")->assertNotFound();
    }

    public function test_lists_only_active_adoption_pets_of_the_ong(): void
    {
        $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $ong = $this->makeOng();
        $other = $this->makeOng();
        $this->makeAdoptionPetFor($owner, ['name' => 'Bidu', 'ong_id' => $ong->id, 'latitude' => -23.5, 'longitude' => -46.6]);
        $this->makeAdoptionPetFor($owner, ['name' => 'Inativo', 'ong_id' => $ong->id, 'is_active' => false]);
        $this->makeAdoptionPetFor($owner, ['name' => 'De Outra', 'ong_id' => $other->id]);
        $this->makePetFor($owner, ['name' => 'Pet de Match', 'ong_id' => $ong->id]);

        $response = $this->getJson("/api/v1/ongs/{$ong->uuid}/pets")->assertOk();

        $this->assertSame(['Bidu'], $this->names($response));
        foreach (['id', 'user_id', 'latitude', 'longitude'] as $key) {
            $this->assertArrayNotHasKey($key, $response->json('data.0'));
        }
    }

    public function test_pet_list_filters_by_gender(): void
    {
        $this->loginAsTutor();
        $owner = $this->makeUser('dono@example.com');
        $ong = $this->makeOng();
        $this->makeAdoptionPetFor($owner, ['name' => 'Macho', 'ong_id' => $ong->id, 'gender' => 'male']);
        $this->makeAdoptionPetFor($owner, ['name' => 'Femea', 'ong_id' => $ong->id, 'gender' => 'female']);

        $this->assertSame(['Femea'], $this->names($this->getJson("/api/v1/ongs/{$ong->uuid}/pets?filters[gender]=female")));
    }

    public function test_lists_only_published_posts_of_the_ong(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();
        $this->makePost($ong, ['title' => 'Publicado']);
        $this->makePost($ong, ['title' => 'Rascunho', 'published_at' => null]);
        $this->makePost($ong, ['title' => 'Agendado', 'published_at' => now()->addDay()]);
        $this->makePost($this->makeOng(), ['title' => 'De outra']);
        $this->makePost(null, ['title' => 'Do sistema']);

        $titles = collect($this->getJson("/api/v1/ongs/{$ong->uuid}/posts")->assertOk()->json('data'))->pluck('title')->all();

        $this->assertSame(['Publicado'], $titles);
    }
}
