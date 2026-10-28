<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OngCampaign extends BaseModel
{
    use HasUuidTrait;

    public const STATUS_LABELS = [
        'active' => 'Em andamento',
        'completed' => 'Meta batida',
        'cancelled' => 'Cancelada',
    ];

    protected $fillable = [
        'uuid',
        'ong_id',
        'title',
        'description',
        'goal_amount',
        'raised_amount',
        'status',
        'ends_at',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'ends_at' => 'date',
    ];

    public function progressPercent(): float
    {
        $goal = (float) $this->goal_amount;

        return $goal > 0 ? min(100.0, round((float) $this->raised_amount / $goal * 100, 1)) : 0.0;
    }

    public function remainingAmount(): float
    {
        return max(0.0, (float) $this->goal_amount - (float) $this->raised_amount);
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
