<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Pet extends BaseModel
{
    use HasUuidTrait;

    protected $fillable = [
        'uuid',
        'name',
        'gender',
        'color',
        'bio',
        'latitude',
        'longitude',
        'is_neutered',
        'is_vaccinated',
        'is_active',
        'birth_date',
        'user_id',
        'species_id',
        'breed_id',
        'size_id',
        'goal_id',
        'ong_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_neutered' => 'boolean',
        'is_vaccinated' => 'boolean',
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PetPhoto::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(PetDetail::class);
    }

    public function vaccines(): HasMany
    {
        return $this->hasMany(PetVaccine::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(PetHealthRecord::class);
    }

    public function weights(): HasMany
    {
        return $this->hasMany(PetWeight::class);
    }

    public function swipesGiven(): HasMany
    {
        return $this->hasMany(Swipe::class, 'swiper_pet_id');
    }

    public function swipesReceived(): HasMany
    {
        return $this->hasMany(Swipe::class, 'target_pet_id');
    }

    public function adoptionSwipes(): HasMany
    {
        return $this->hasMany(AdoptionSwipe::class);
    }

    public function adoptionMatches(): HasMany
    {
        return $this->hasMany(AdoptionMatch::class);
    }

    public function managers(): Builder
    {
        return User::query()->where(fn (Builder $q) => $q
            ->where('id', $this->user_id)
            ->orWhereIn('id', DB::table('ong_user')->select('user_id')->where('ong_id', $this->ong_id)));
    }

    public function scopeAdoptable(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereHas('goal', fn (Builder $q) => $q->where('slug', 'adoption'))
            ->whereHas('ong', fn (Builder $q) => $q->where('status', 'verified'));
    }

    public function scopeManagedBy(Builder $query, int $userId): void
    {
        $query->where(fn (Builder $q) => $q
            ->where('user_id', $userId)
            ->orWhereIn('ong_id', DB::table('ong_user')->select('ong_id')->where('user_id', $userId)));
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
