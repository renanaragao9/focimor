<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'body',
        'read_at',
        'pet_match_id',
        'adoption_match_id',
        'sender_user_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function petMatch(): BelongsTo
    {
        return $this->belongsTo(PetMatch::class);
    }

    public function adoptionMatch(): BelongsTo
    {
        return $this->belongsTo(AdoptionMatch::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
