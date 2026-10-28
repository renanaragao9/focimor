<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PetMatch extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'is_active',
        'matched_at',
        'pet_one_id',
        'pet_two_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'matched_at' => 'datetime',
    ];

    public function petOne(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_one_id');
    }

    public function petTwo(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_two_id');
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
