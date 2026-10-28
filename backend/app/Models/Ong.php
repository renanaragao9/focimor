<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ong extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'cnpj',
        'description',
        'logo_path',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'latitude',
        'longitude',
        'pix_key',
        'donation_info',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'verified_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ong_user')->withPivot('role')->withTimestamps();
    }

    public function roleOf(int $userId): ?string
    {
        return DB::table('ong_user')->where('ong_id', $this->id)->where('user_id', $userId)->value('role');
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(OngPost::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(OngCampaign::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
