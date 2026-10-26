<?php

namespace App\Services;

class DistanceService
{
    private const EARTH_RADIUS_KM = 6371.0;

    /**
     * Jarak garis lurus antara dua koordinat memakai rumus Haversine (km).
     */
    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Estimasi waktu tempuh dalam menit. Memakai asumsi kecepatan rata-rata
     * kendaraan di dalam kota (30 km/jam) karena aplikasi ini tidak
     * terhubung ke layanan routing/lalu lintas sungguhan.
     */
    public static function estimateDurationMinutes(float $distanceKm, float $averageSpeedKmh = 30.0): int
    {
        if ($distanceKm <= 0) {
            return 0;
        }

        return (int) max(1, ceil(($distanceKm / $averageSpeedKmh) * 60));
    }
}
