<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Filament\Resources\Ongs\Pages\EditOng;
use App\Filament\Resources\Ongs\RelationManagers\MembersRelationManager;
use App\Filament\Resources\PreOngRegistrations\Pages\ListPreOngRegistrations;
use App\Models\Ong;
use App\Models\OngCampaign;
use App\Models\PreOngRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OngAdminTest extends TestCase
{
    use RefreshDatabase;

    private Ong $ong;

    private function actAsMaster(): void
    {
        $master = User::forceCreate(['name' => 'Master', 'email' => 'master@example.com', 'password' => 'Senha123', 'is_master' => true]);
        $this->ong = Ong::create(['name' => 'Patas', 'slug' => 'patas', 'status' => 'verified']);
        $this->actingAs($master);
    }

    public function test_master_opens_the_ong_pages(): void
    {
        $this->actAsMaster();

        $this->get('/admin/ongs')->assertOk();
        $this->get('/admin/ongs/create')->assertOk();
        $this->get("/admin/ongs/{$this->ong->uuid}")->assertOk();
        $this->get("/admin/ongs/{$this->ong->uuid}/edit")->assertOk();
    }

    public function test_members_tab_lists_and_attaches_members_with_a_role(): void
    {
        $this->actAsMaster();
        $existing = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'Senha123']);
        $newcomer = User::create(['name' => 'Bia', 'email' => 'bia@example.com', 'password' => 'Senha123']);
        $this->ong->users()->attach($existing->id, ['role' => 'admin']);

        Livewire::test(MembersRelationManager::class, ['ownerRecord' => $this->ong, 'pageClass' => EditOng::class])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$existing])
            ->assertCanNotSeeTableRecords([$newcomer])
            ->callTableAction('attach', data: ['recordId' => $newcomer->id, 'role' => 'member'])
            ->assertHasNoTableActionErrors();

        $this->assertSame('member', $this->ong->roleOf($newcomer->id));
    }

    public function test_master_opens_the_campaign_pages(): void
    {
        $this->actAsMaster();
        $campaign = OngCampaign::create(['ong_id' => $this->ong->id, 'title' => 'Reforma', 'goal_amount' => 1000, 'raised_amount' => 250]);

        $this->get('/admin/ong-campaigns')->assertOk();
        $this->get('/admin/ong-campaigns/create')->assertOk();
        $this->get("/admin/ong-campaigns/{$campaign->uuid}")->assertOk();
        $this->get("/admin/ong-campaigns/{$campaign->uuid}/edit")->assertOk();
    }

    public function test_master_opens_the_pre_registration_pages(): void
    {
        $this->actAsMaster();
        $registration = PreOngRegistration::create(['name' => 'Patas do Bem', 'email' => 'a@example.com', 'phone' => '85999990000', 'city' => 'Fortaleza', 'state' => 'CE']);

        $this->get('/admin/pre-ong-registrations')->assertOk();
        $this->get("/admin/pre-ong-registrations/{$registration->id}")->assertOk();
        $this->get("/admin/pre-ong-registrations/{$registration->id}/edit")->assertOk();
    }

    public function test_approving_a_pre_registration_creates_a_verified_ong_with_the_admin(): void
    {
        $this->actAsMaster();
        $applicant = User::create(['name' => 'Bia', 'email' => 'bia@example.com', 'password' => 'Senha123']);
        $registration = PreOngRegistration::create(['name' => 'Patas do Bem', 'email' => 'Bia@Example.com', 'phone' => '85999990000', 'city' => 'Fortaleza', 'state' => 'CE', 'cnpj' => '11222333000181']);

        Livewire::test(ListPreOngRegistrations::class)->callTableAction('approve', $registration)->assertHasNoTableActionErrors();

        $registration->refresh();
        $ong = Ong::findOrFail($registration->ong_id);
        $this->assertSame('approved', $registration->status);
        $this->assertSame('verified', $ong->status);
        $this->assertSame('patas-do-bem', $ong->slug);
        $this->assertSame('admin', $ong->roleOf($applicant->id));
    }
}
