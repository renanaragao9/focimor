<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use App\Models\Ong;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OngMemberTest extends TestCase
{
    use HasOngFixtures, RefreshDatabase;

    private Ong $ong;

    private function setUpAs(string $role): void
    {
        $user = $this->loginAsTutor();
        $this->ong = $this->makeOng();
        $this->joinOng($this->ong, $user, $role);
    }

    private function url(string $suffix = ''): string
    {
        return "/api/v1/my-ongs/{$this->ong->uuid}/members{$suffix}";
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/my-ongs/'.fake()->uuid().'/members')->assertUnauthorized();
        $this->postJson('/api/v1/my-ongs/'.fake()->uuid().'/members', [])->assertUnauthorized();
    }

    public function test_any_member_lists_the_members(): void
    {
        $this->setUpAs('member');
        $this->joinOng($this->ong, $this->makeUser('admin@example.com'), 'admin');

        $response = $this->getJson($this->url())->assertOk()->assertJsonCount(2, 'data');

        $this->assertEqualsCanonicalizing(['member', 'admin'], collect($response->json('data'))->pluck('role')->all());
        $this->assertArrayNotHasKey('password', $response->json('data.0'));
    }

    public function test_non_members_cannot_list(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();

        $this->getJson("/api/v1/my-ongs/{$ong->uuid}/members")->assertNotFound();
    }

    public function test_admin_adds_an_existing_user_by_email(): void
    {
        $this->setUpAs('admin');
        $newcomer = $this->makeUser('novo@example.com');

        $this->postJson($this->url(), ['email' => 'novo@example.com', 'role' => 'member'])
            ->assertCreated()
            ->assertJsonPath('data.email', 'novo@example.com')
            ->assertJsonPath('data.role', 'member');

        $this->assertSame('member', $this->ong->roleOf($newcomer->id));
    }

    public function test_add_member_validations(): void
    {
        $this->setUpAs('admin');
        $this->makeUser('existente@example.com');

        $this->postJson($this->url(), ['email' => 'ninguem@example.com', 'role' => 'member'])->assertUnprocessable()->assertJsonPath('message', 'Nenhum usuário encontrado com este e-mail.');
        $this->postJson($this->url(), ['email' => $this->tutor->email, 'role' => 'member'])->assertUnprocessable()->assertJsonPath('message', 'Este usuário já é membro da ONG.');
        $this->postJson($this->url(), ['email' => 'existente@example.com', 'role' => 'dono'])->assertUnprocessable()->assertJsonValidationErrors('role', 'errors');
        $this->postJson($this->url(), [])->assertUnprocessable();
    }

    public function test_plain_member_cannot_add_update_or_remove_others(): void
    {
        $this->setUpAs('member');
        $other = $this->makeUser('outro@example.com');
        $newcomer = $this->makeUser('novo@example.com');
        $this->joinOng($this->ong, $other, 'admin');

        $this->postJson($this->url(), ['email' => $newcomer->email, 'role' => 'member'])->assertForbidden();
        $this->putJson($this->url("/{$other->id}"), ['role' => 'member'])->assertForbidden();
        $this->deleteJson($this->url("/{$other->id}"))->assertForbidden();

        $this->assertNull($this->ong->roleOf($newcomer->id));
        $this->assertSame('admin', $this->ong->roleOf($other->id));
    }

    public function test_admin_changes_roles_but_keeps_at_least_one_admin(): void
    {
        $this->setUpAs('admin');
        $other = $this->makeUser('outro@example.com');
        $this->joinOng($this->ong, $other, 'member');

        $this->putJson($this->url("/{$other->id}"), ['role' => 'admin'])->assertOk()->assertJsonPath('data.role', 'admin');
        $this->putJson($this->url("/{$other->id}"), ['role' => 'member'])->assertOk();
        $this->putJson($this->url("/{$this->tutor->id}"), ['role' => 'member'])->assertUnprocessable()->assertJsonPath('message', 'A ONG precisa de pelo menos um administrador.');

        $this->assertSame('admin', $this->ong->roleOf($this->tutor->id));
    }

    public function test_admin_removes_a_member(): void
    {
        $this->setUpAs('admin');
        $other = $this->makeUser('outro@example.com');
        $this->joinOng($this->ong, $other);

        $this->deleteJson($this->url("/{$other->id}"))->assertOk();

        $this->assertNull($this->ong->roleOf($other->id));
    }

    public function test_the_last_admin_cannot_be_removed(): void
    {
        $this->setUpAs('admin');

        $this->deleteJson($this->url("/{$this->tutor->id}"))->assertUnprocessable()->assertJsonPath('message', 'A ONG precisa de pelo menos um administrador.');

        $this->assertSame('admin', $this->ong->roleOf($this->tutor->id));
    }

    public function test_a_member_can_leave_the_ong_but_not_remove_others(): void
    {
        $this->setUpAs('member');
        $this->joinOng($this->ong, $this->makeUser('admin@example.com'), 'admin');

        $this->deleteJson($this->url("/{$this->tutor->id}"))->assertOk();

        $this->assertNull($this->ong->roleOf($this->tutor->id));
        $this->getJson($this->url())->assertNotFound();
    }

    public function test_cannot_target_a_user_that_is_not_a_member_of_this_ong(): void
    {
        $this->setUpAs('admin');
        $stranger = User::create(['name' => 'Estranho', 'email' => 'estranho@example.com', 'password' => 'Senha123']);

        $this->putJson($this->url("/{$stranger->id}"), ['role' => 'admin'])->assertNotFound();
        $this->deleteJson($this->url("/{$stranger->id}"))->assertNotFound();
    }
}
