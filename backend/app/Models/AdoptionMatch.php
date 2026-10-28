<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdoptionMatch extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'status',
        'decided_at',
        'user_id',
        'pet_id',
        'decided_by_user_id',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public const TRANSITIONS = [
        'pending' => ['accepted', 'declined', 'cancelled'],
        'accepted' => ['adopted', 'declined', 'cancelled'],
        'declined' => [],
        'adopted' => [],
        'cancelled' => [],
    ];

    public const STATUS_LABELS = [
        'pending' => 'Pendente',
        'accepted' => 'Aceito',
        'declined' => 'Recusado',
        'adopted' => 'Adotado',
        'cancelled' => 'Cancelado',
    ];

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
