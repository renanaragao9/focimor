<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OngPost extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'title',
        'body',
        'cover_path',
        'published_at',
        'ong_id',
        'author_user_id',
        'pet_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn (Builder $q) => $q->whereNull('ong_id')->orWhereHas('ong', fn ($ong) => $ong->where('status', 'verified')));
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
