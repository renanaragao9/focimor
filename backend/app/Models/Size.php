<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends BaseModel
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

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
