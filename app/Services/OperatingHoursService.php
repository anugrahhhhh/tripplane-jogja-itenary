<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class OperatingHoursService
{
    /**
     * Cek apakah suatu waktu (misal: "sekarang") berada dalam jam operasional.
     */
    public static function isOpenAt(string $openTime, string $closeTime, Carbon $moment): bool
    {
        $minutes = $moment->hour * 60 + $moment->minute;

        return self::isWithinWindow($openTime, $closeTime, $minutes, $minutes);
    }

    /**
     * Cek apakah rentang waktu [startMinutes, endMinutes] (menit sejak 00:00)
     * seluruhnya berada dalam jam operasional destinasi. Mendukung destinasi
     * yang buka hingga lewat tengah malam (mis. buka 18:00, tutup 02:00).
     */
    public static function isWithinWindow(string $openTime, string $closeTime, int $startMinutes, int $endMinutes): bool
    {
        $openMinutes = self::toMinutes($openTime);
        $closeMinutes = self::toMinutes($closeTime);

        // Buka 24 jam penuh (00:00 - 23:59).
        if ($openMinutes === 0 && $closeMinutes === 1439) {
            return true;
        }

        if ($closeMinutes <= $openMinutes) {
            // Melewati tengah malam, contoh: buka 18:00, tutup 02:00 (hari berikutnya).
            $closeMinutes += 24 * 60;

            if ($startMinutes < $openMinutes) {
                $startMinutes += 24 * 60;
                $endMinutes += 24 * 60;
            }
        }

        return $startMinutes >= $openMinutes && $endMinutes <= $closeMinutes;
    }

    protected static function toMinutes(string $time): int
    {
        $carbon = Carbon::parse($time);

        return ((int) $carbon->format('H')) * 60 + (int) $carbon->format('i');
    }
}
