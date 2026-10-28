<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetVaccine extends BaseModel
{
    protected $fillable = [
        'name',
        'applied_at',
        'next_dose_at',
        'batch',
        'veterinarian',
        'notes',
        'pet_id',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'next_dose_at' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
