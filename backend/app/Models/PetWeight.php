<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetWeight extends BaseModel
{
    protected $fillable = [
        'weight_kg',
        'height_cm',
        'width_cm',
        'measured_at',
        'pet_id',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'measured_at' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
