<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Swipe extends BaseModel
{
    protected $fillable = [
        'action',
        'swiper_pet_id',
        'target_pet_id',
    ];

    public function swiperPet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'swiper_pet_id');
    }

    public function targetPet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'target_pet_id');
    }
}
