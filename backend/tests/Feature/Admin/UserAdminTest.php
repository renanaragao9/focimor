<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $master;

    private function actAsMaster(): void
    {
        $this->master = User::forceCreate(['name' => 'Master', 'email' => 'master@example.com', 'password' => 'Senha123', 'is_master' => true]);
        $this->actingAs($this->master);
    }

    public function test_master_opens_the_user_pages(): void
    {
        $this->actAsMaster();
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123']);

        $this->get('/admin/users')->assertOk();
        $this->get('/admin/users/create')->assertOk();
        $this->get("/admin/users/{$user->id}")->assertOk();
        $this->get("/admin/users/{$user->id}/edit")->assertOk();
    }

    public function test_master_creates_an_admin_user(): void
    {
        $this->actAsMaster();

        Livewire::test(CreateUser::class)
            ->fillForm(['name' => 'Bia', 'email' => 'bia@example.com', 'password' => 'Senha123', 'is_master' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(User::where('email', 'bia@example.com')->firstOrFail()->is_master);
    }

    public function test_edit_keeps_the_password_when_blank(): void
    {
        $this->actAsMaster();
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123']);
        $hash = $user->password;

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm(['name' => 'Ana Maria', 'password' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();
        $this->assertSame('Ana Maria', $user->name);
        $this->assertSame($hash, $user->password);
    }

    public function test_list_shows_users(): void
    {
        $this->actAsMaster();
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123']);

        Livewire::test(ListUsers::class)->assertCanSeeTableRecords([$user, $this->master]);
    }
}
