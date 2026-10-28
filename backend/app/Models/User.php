<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'email_verified_at', 'avatar_path', 'latitude', 'longitude'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_master;
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function ongs(): BelongsToMany
    {
        return $this->belongsToMany(Ong::class, 'ong_user')->withPivot('role')->withTimestamps();
    }

    public function adoptionSwipes(): HasMany
    {
        return $this->hasMany(AdoptionSwipe::class);
    }

    public function adoptionMatches(): HasMany
    {
        return $this->hasMany(AdoptionMatch::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_master' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'password' => 'hashed',
        ];
    }
}
