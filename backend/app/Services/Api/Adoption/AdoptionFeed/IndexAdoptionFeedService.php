<?php

declare(strict_types=1);

namespace App\Services\Api\Adoption\AdoptionFeed;

use App\Models\AdoptionSwipe;
use App\Models\Pet;
use App\Services\BaseService;
use App\Traits\PaginatesByDistance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class IndexAdoptionFeedService extends BaseService
{
    use PaginatesByDistance;

    public function run(array $data): LengthAwarePaginator
    {
        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 15);
        $speciesId = $data['filters']['species_id'] ?? null;
        $breedId = $data['filters']['breed_id'] ?? null;
        $sizeId = $data['filters']['size_id'] ?? null;
        $gender = $data['filters']['gender'] ?? null;
        $maxDistanceKm = $data['filters']['max_distance_km'] ?? null;
        $requestLatitude = $data['filters']['latitude'] ?? null;
        $requestLongitude = $data['filters']['longitude'] ?? null;
        $maxDistance = $maxDistanceKm !== null ? (float) $maxDistanceKm : null;
        $origin = $this->resolveOrigin($requestLatitude, $requestLongitude);
        [$originLatitude, $originLongitude] = $origin ?? [null, null];

        if ($maxDistance !== null && $origin === null) {
            throw ValidationException::withMessages([
                'filters.max_distance_km' => ['Informe a localização no seu perfil ou envie latitude e longitude.'],
            ]);
        }

        $query = Pet::query()
            ->where('is_active', true)
            ->whereNot(fn ($q) => $q->managedBy($this->getUserId()))
            ->whereHas('goal', fn ($q) => $q->where('slug', 'adoption'))
            ->whereNotIn('id', AdoptionSwipe::withTrashed()->where('user_id', $this->getUserId())->select('pet_id'))
            ->when($speciesId, fn ($q, $id) => $q->where('species_id', $id))
            ->when($breedId, fn ($q, $id) => $q->where('breed_id', $id))
            ->when($sizeId, fn ($q, $id) => $q->where('size_id', $id))
            ->when($gender, fn ($q, $value) => $q->where('gender', $value))
            ->with(['species', 'breed', 'size', 'goal', 'photos', 'ong']);

        if ($origin !== null && $maxDistance !== null) {
            return $this->paginateWithinRadius($query, $originLatitude, $originLongitude, $maxDistance, $perPage, $page);
        }

        $paginator = $query->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        if ($origin !== null) {
            $paginator->getCollection()->each(fn (Pet $pet) => $this->attachDistance($pet, $originLatitude, $originLongitude));
        }

        return $paginator;
    }

    private function resolveOrigin(mixed $requestLatitude, mixed $requestLongitude): ?array
    {
        if ($requestLatitude !== null && $requestLongitude !== null) {
            return [(float) $requestLatitude, (float) $requestLongitude];
        }

        $user = $this->getUser();

        return $user->latitude !== null && $user->longitude !== null ? [(float) $user->latitude, (float) $user->longitude] : null;
    }
}
