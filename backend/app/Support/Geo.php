<?php

declare(strict_types=1);

namespace App\Support;

final class Geo
{
    private const EARTH_RADIUS_KM = 6371.0;

    public static function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return self::EARTH_RADIUS_KM * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function boundingBox(float $lat, float $lon, float $radiusKm): array
    {
        $dLat = rad2deg($radiusKm / self::EARTH_RADIUS_KM);
        $dLon = rad2deg($radiusKm / (self::EARTH_RADIUS_KM * max(cos(deg2rad($lat)), 0.01)));

        return [
            'min_lat' => $lat - $dLat,
            'max_lat' => $lat + $dLat,
            'min_lon' => max($lon - $dLon, -180.0),
            'max_lon' => min($lon + $dLon, 180.0),
        ];
    }
}
