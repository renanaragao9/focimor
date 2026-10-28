<?php

namespace App\Traits;

use App\Models\Pet;
use App\Support\Geo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginatesByDistance
{
    protected function attachDistance(Pet $pet, float $lat, float $lon): void
    {
        if ($pet->latitude === null || $pet->longitude === null) {
            return;
        }

        $pet->setAttribute('distance_km', Geo::distanceKm($lat, $lon, (float) $pet->latitude, (float) $pet->longitude));
    }

    protected function paginateWithinRadius(Builder $query, float $lat, float $lon, float $maxKm, int $perPage, int $page): LengthAwarePaginator
    {
        $box = Geo::boundingBox($lat, $lon, $maxKm);
        $minLat = $box['min_lat'];
        $maxLat = $box['max_lat'];
        $minLon = $box['min_lon'];
        $maxLon = $box['max_lon'];

        $nearby = $query
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLon, $maxLon])
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->each(fn (Pet $pet) => $this->attachDistance($pet, $lat, $lon))
            ->filter(fn (Pet $pet) => $pet->getAttribute('distance_km') <= $maxKm)
            ->sortBy(fn (Pet $pet) => $pet->getAttribute('distance_km'))
            ->values();

        return new LengthAwarePaginator(
            $nearby->forPage($page, $perPage)->values(),
            $nearby->count(),
            $perPage,
            $page,
            ['path' => request()->url()],
        );
    }
}
