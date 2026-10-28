<?php

declare(strict_types=1);

namespace Tests\Feature\Ong;

use App\Models\Ong;
use App\Models\OngCampaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OngCampaignTest extends TestCase
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
        return "/api/v1/my-ongs/{$this->ong->uuid}/campaigns{$suffix}";
    }

    private function makeCampaign(array $overrides = []): OngCampaign
    {
        return OngCampaign::create(array_merge(['ong_id' => $this->ong->id, 'title' => 'Reforma', 'goal_amount' => 1000, 'raised_amount' => 0], $overrides));
    }

    public function test_requires_authentication_to_manage(): void
    {
        $this->getJson('/api/v1/my-ongs/'.fake()->uuid().'/campaigns')->assertUnauthorized();
        $this->postJson('/api/v1/my-ongs/'.fake()->uuid().'/campaigns', [])->assertUnauthorized();
    }

    public function test_any_member_creates_a_campaign_with_progress(): void
    {
        $this->setUpAs('member');

        $this->postJson($this->url(), ['title' => 'Castração', 'goal_amount' => 2000, 'raised_amount' => 500])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Castração')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.progress_percent', 25)
            ->assertJsonPath('data.remaining_amount', '1500.00')
            ->assertJsonPath('data.ong.uuid', $this->ong->uuid);
    }

    public function test_campaign_reaching_the_goal_is_completed(): void
    {
        $this->setUpAs();

        $this->postJson($this->url(), ['title' => 'Ração', 'goal_amount' => 100, 'raised_amount' => 100])->assertCreated()->assertJsonPath('data.status', 'completed');
    }

    public function test_updating_the_raised_amount_updates_progress_and_status(): void
    {
        $this->setUpAs();
        $campaign = $this->makeCampaign();

        $this->putJson($this->url("/{$campaign->uuid}"), ['raised_amount' => 400])->assertOk()->assertJsonPath('data.progress_percent', 40)->assertJsonPath('data.status', 'active');
        $this->putJson($this->url("/{$campaign->uuid}"), ['raised_amount' => 1000])->assertOk()->assertJsonPath('data.status', 'completed');
        $this->putJson($this->url("/{$campaign->uuid}"), ['raised_amount' => 800])->assertOk()->assertJsonPath('data.status', 'active');
    }

    public function test_a_cancelled_campaign_stays_cancelled_unless_the_status_changes(): void
    {
        $this->setUpAs();
        $campaign = $this->makeCampaign(['status' => 'cancelled']);

        $this->putJson($this->url("/{$campaign->uuid}"), ['raised_amount' => 2000])->assertOk()->assertJsonPath('data.status', 'cancelled');
        $this->putJson($this->url("/{$campaign->uuid}"), ['status' => 'active'])->assertOk()->assertJsonPath('data.status', 'active');
    }

    public function test_validates_the_amounts(): void
    {
        $this->setUpAs();

        $this->postJson($this->url(), ['title' => 'X'])->assertUnprocessable()->assertJsonValidationErrors('goal_amount', 'errors');
        $this->postJson($this->url(), ['title' => 'X', 'goal_amount' => 0])->assertUnprocessable();
        $this->postJson($this->url(), ['title' => 'X', 'goal_amount' => 10, 'raised_amount' => -1])->assertUnprocessable();
    }

    public function test_unverified_ong_cannot_create_campaigns(): void
    {
        $this->setUpAs('admin', 'pending');

        $this->postJson($this->url(), ['title' => 'X', 'goal_amount' => 10])->assertUnprocessable();
    }

    public function test_deletes_a_campaign_and_isolates_other_ongs(): void
    {
        $this->setUpAs();
        $campaign = $this->makeCampaign();
        $foreign = OngCampaign::create(['ong_id' => $this->makeOng()->id, 'title' => 'Outra', 'goal_amount' => 10]);

        $this->deleteJson($this->url("/{$foreign->uuid}"))->assertNotFound();
        $this->deleteJson($this->url("/{$campaign->uuid}"))->assertOk();

        $this->assertSoftDeleted('ong_campaigns', ['id' => $campaign->id]);
    }

    public function test_non_members_cannot_manage(): void
    {
        $this->loginAsTutor();
        $ong = $this->makeOng();

        $this->getJson("/api/v1/my-ongs/{$ong->uuid}/campaigns")->assertNotFound();
    }

    public function test_public_list_hides_cancelled_campaigns_and_needs_no_login(): void
    {
        $ong = $this->makeOng();
        $this->ong = $ong;
        $this->makeCampaign(['title' => 'Concluída', 'raised_amount' => 1000, 'status' => 'completed']);
        $this->makeCampaign(['title' => 'Cancelada', 'status' => 'cancelled']);
        $this->makeCampaign(['title' => 'Ativa', 'raised_amount' => 300]);

        $response = $this->getJson("/api/v1/ongs/{$ong->uuid}/campaigns")->assertOk();

        $this->assertSame(['Ativa', 'Concluída'], collect($response->json('data'))->pluck('title')->all());
        $this->assertSame(30, (int) $response->json('data.0.progress_percent'));
    }
}
