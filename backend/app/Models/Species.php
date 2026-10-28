<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends BaseModel
{
    protected $table = 'species';

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

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
