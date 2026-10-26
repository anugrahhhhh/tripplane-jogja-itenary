<?php

namespace Tests\Unit\Services;

use App\Services\DistanceService;
use PHPUnit\Framework\TestCase;

class DistanceServiceTest extends TestCase
{
    public function test_distance_between_identical_points_is_zero(): void
    {
        $this->assertSame(0.0, round(DistanceService::haversineKm(-7.8014, 110.3644, -7.8014, 110.3644), 4));
    }

    public function test_distance_of_one_degree_latitude_is_approximately_111_km(): void
    {
        // Satu derajat lintang selalu ±111.19 km di mana pun, ini fakta
        // geografis yang bisa dipakai memverifikasi rumus Haversine benar.
        $distance = DistanceService::haversineKm(0, 0, 1, 0);

        $this->assertEqualsWithDelta(111.19, $distance, 0.5);
    }

    public function test_estimate_duration_for_zero_distance_is_zero_minutes(): void
    {
        $this->assertSame(0, DistanceService::estimateDurationMinutes(0));
    }

    public function test_estimate_duration_for_30km_at_30kmh_is_60_minutes(): void
    {
        $this->assertSame(60, DistanceService::estimateDurationMinutes(30, 30));
    }

    public function test_estimate_duration_never_rounds_down_to_zero_for_a_nonzero_distance(): void
    {
        $this->assertSame(1, DistanceService::estimateDurationMinutes(0.001, 30));
    }
}
