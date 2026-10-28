<?php

namespace Tests\Unit\Models;

use App\Models\Ong;
use App\Models\OngCampaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OngCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCampaign(array $attributes = []): OngCampaign
    {
        return OngCampaign::create(array_merge([
            'title' => 'Reforma do canil',
            'goal_amount' => 1000,
            'raised_amount' => 250,
            'ong_id' => Ong::create(['name' => 'ONG', 'slug' => 'ong-'.Str::random(5)])->id,
        ], $attributes));
    }

    public function test_progress_and_remaining_amount(): void
    {
        $campaign = $this->makeCampaign();

        $this->assertSame(25.0, $campaign->progressPercent());
        $this->assertSame(750.0, $campaign->remainingAmount());
    }

    public function test_progress_is_capped_at_one_hundred(): void
    {
        $campaign = $this->makeCampaign(['raised_amount' => 1500]);

        $this->assertSame(100.0, $campaign->progressPercent());
        $this->assertSame(0.0, $campaign->remainingAmount());
    }

    public function test_can_create(): void
    {
        $record = $this->makeCampaign();

        $this->assertDatabaseHas('ong_campaigns', ['id' => $record->id, 'title' => 'Reforma do canil', 'status' => 'active']);
    }

    public function test_can_read(): void
    {
        $this->assertNotNull(OngCampaign::find($this->makeCampaign()->id));
    }

    public function test_can_update(): void
    {
        $record = $this->makeCampaign();

        $record->update(['raised_amount' => 400]);

        $this->assertDatabaseHas('ong_campaigns', ['id' => $record->id, 'raised_amount' => 400]);
    }

    public function test_can_delete(): void
    {
        $record = $this->makeCampaign();

        $record->delete();

        $this->assertSoftDeleted('ong_campaigns', ['id' => $record->id]);
        $this->assertNull(OngCampaign::find($record->id));
    }

    public function test_relationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->makeCampaign()->ong());
    }
}
