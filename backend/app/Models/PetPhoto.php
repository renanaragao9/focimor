<?php

namespace App\Models;

use App\Helpers\GenerateTemporaryUrlHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetPhoto extends BaseModel
{
    protected $fillable = [
        'path',
        'order',
        'is_primary',
        'pet_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'https://') || str_starts_with($this->path, 'http://')) {
            return $this->path;
        }

        return GenerateTemporaryUrlHelper::run($this->path);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
