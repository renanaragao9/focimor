<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthRecordType extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function healthRecords(): HasMany
    {
        return $this->hasMany(PetHealthRecord::class);
    }
}
