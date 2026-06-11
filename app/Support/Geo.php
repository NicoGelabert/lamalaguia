<?php

namespace App\Support;

use Illuminate\Support\Collection;

class Geo
{
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function isValidCoordinate(?float $lat, ?float $lng): bool
    {
        if ($lat === null || $lng === null) {
            return false;
        }

        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    public static function sortByDistance(Collection $items, float $userLat, float $userLng): Collection
    {
        return $items
            ->map(function ($item) use ($userLat, $userLng) {
                $lat = $item->lat !== null ? (float) $item->lat : null;
                $lng = $item->lng !== null ? (float) $item->lng : null;

                if (self::isValidCoordinate($lat, $lng)) {
                    $km = self::distanceKm($userLat, $userLng, $lat, $lng);
                    $item->distancia_sort = $km;
                    $item->distancia_km = round($km, 1);
                } else {
                    $item->distancia_sort = null;
                    $item->distancia_km = null;
                }

                return $item;
            })
            ->sortBy(fn ($item) => [
                $item->distancia_sort ?? 99999,
                $item->orden_destacado ?? 9999,
            ])
            ->values();
    }
}
