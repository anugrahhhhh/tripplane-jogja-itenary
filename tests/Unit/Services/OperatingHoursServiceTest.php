<?php

namespace Tests\Unit\Services;

use App\Services\OperatingHoursService;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class OperatingHoursServiceTest extends TestCase
{
    public function test_normal_hours_open_and_closed_moments(): void
    {
        $today = Carbon::parse('2026-01-01');

        $this->assertTrue(OperatingHoursService::isOpenAt('08:00', '17:00', $today->copy()->setTime(10, 0)));
        $this->assertFalse(OperatingHoursService::isOpenAt('08:00', '17:00', $today->copy()->setTime(20, 0)));
    }

    public function test_24_hour_destination_is_always_open(): void
    {
        $moment = Carbon::parse('2026-01-01 03:17');

        $this->assertTrue(OperatingHoursService::isOpenAt('00:00', '23:59', $moment));
    }

    public function test_overnight_window_spanning_midnight(): void
    {
        $today = Carbon::parse('2026-01-01');

        // Buka 18:00, tutup 02:00 (hari berikutnya).
        $this->assertTrue(OperatingHoursService::isOpenAt('18:00', '02:00', $today->copy()->setTime(23, 0)));
        $this->assertTrue(OperatingHoursService::isOpenAt('18:00', '02:00', $today->copy()->setTime(1, 0)));
        $this->assertFalse(OperatingHoursService::isOpenAt('18:00', '02:00', $today->copy()->setTime(12, 0)));
    }

    public function test_is_within_window_rejects_a_range_that_ends_after_closing(): void
    {
        // Buka 08:00-17:00, kunjungan 16:30 selama 60 menit berakhir 17:30 (lewat tutup).
        $this->assertFalse(OperatingHoursService::isWithinWindow('08:00', '17:00', 16 * 60 + 30, 17 * 60 + 30));
    }
}
