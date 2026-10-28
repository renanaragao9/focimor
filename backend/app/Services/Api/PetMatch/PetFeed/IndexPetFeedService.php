<?php

declare(strict_types=1);

namespace App\Services\Api\PetMatch\PetFeed;

use App\Models\Pet;
use App\Models\Swipe;
use App\Services\BaseService;
use App\Traits\PaginatesByDistance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class IndexPetFeedService extends BaseService
{
    use PaginatesByDistance;

    public function run(Pet $pet, array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $speciesId = $data['filters']['species_id'] ?? $pet->species_id;
        $goalId = $data['filters']['goal_id'] ?? $pet->goal_id;
        $breedId = $data['filters']['breed_id'] ?? null;
        $sizeId = $data['filters']['size_id'] ?? null;
        $gender = $data['filters']['gender'] ?? null;
        $maxDistanceKm = $data['filters']['max_distance_km'] ?? null;
        $maxDistance = $maxDistanceKm !== null ? (float) $maxDistanceKm : null;
        $hasLocation = $pet->latitude !== null && $pet->longitude !== null;
        $latitude = (float) $pet->latitude;
        $longitude = (float) $pet->longitude;

        if ($maxDistance !== null && ! $hasLocation) {
            throw ValidationException::withMessages([
                'filters.max_distance_km' => ['Informe a localização do seu pet para filtrar por distância.'],
            ]);
        }

        $query = Pet::query()
            ->where('is_active', true)
            ->where('user_id', '!=', $this->getUserId())
            ->whereNotIn('id', Swipe::where('swiper_pet_id', $pet->id)->select('target_pet_id'))
            ->whereDoesntHave('goal', fn ($q) => $q->where('slug', 'adoption'))
            ->where('species_id', $speciesId)
            ->where('goal_id', $goalId)
            ->when($breedId, fn ($q, $id) => $q->where('breed_id', $id))
            ->when($sizeId, fn ($q, $id) => $q->where('size_id', $id))
            ->when($gender, fn ($q, $value) => $q->where('gender', $value))
            ->with(['species', 'breed', 'size', 'goal', 'photos']);

        if ($maxDistance !== null) {
            return $this->paginateWithinRadius($query, $latitude, $longitude, $maxDistance, $perPage, $page);
        }

        $paginator = $query->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        if ($hasLocation) {
            $paginator->getCollection()->each(fn (Pet $candidate) => $this->attachDistance($candidate, $latitude, $longitude));
        }

        return $paginator;
    }
}
