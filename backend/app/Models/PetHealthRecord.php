<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetHealthRecord extends BaseModel
{
    protected $fillable = [
        'health_record_type_id',
        'title',
        'performed_at',
        'next_due_at',
        'veterinarian',
        'cost',
        'description',
        'pet_id',
    ];

    protected $casts = [
        'performed_at' => 'date',
        'next_due_at' => 'date',
        'cost' => 'decimal:2',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(HealthRecordType::class, 'health_record_type_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
