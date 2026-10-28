<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetDetail extends BaseModel
{
    protected $fillable = [
        'microchip',
        'pedigree_number',
        'coat',
        'blood_type',
        'temperament',
        'allergies',
        'chronic_conditions',
        'diet',
        'vet_name',
        'vet_clinic',
        'vet_phone',
        'notes',
        'pet_id',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
