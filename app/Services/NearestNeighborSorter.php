<?php

namespace App\Services;

use App\Models\ItineraryItem;
use Illuminate\Support\Collection;

class NearestNeighborSorter
{
    /**
     * Urutkan itinerary item berdasarkan algoritma nearest neighbor: mulai
     * dari satu titik (item pertama, atau titik pengguna jika diberikan),
     * lalu berulang kali pilih destinasi belum-dikunjungi yang jaraknya
     * paling dekat dari titik saat ini.
     *
     * @param  Collection<int, ItineraryItem>  $items
     * @return Collection<int, ItineraryItem>
     */
    public static function sort(Collection $items, ?float $startLat = null, ?float $startLng = null): Collection
    {
        if ($items->count() <= 1) {
            return $items->values();
        }

        $remaining = $items->values()->all();
        $sorted = [];

        if ($startLat !== null && $startLng !== null) {
            $currentLat = $startLat;
            $currentLng = $startLng;
        } else {
            /** @var ItineraryItem $first */
            $first = array_shift($remaining);
            $sorted[] = $first;
            $currentLat = $first->destination->latitude;
            $currentLng = $first->destination->longitude;
        }

        while (! empty($remaining)) {
            $nearestIndex = null;
            $nearestDistance = null;

            foreach ($remaining as $index => $item) {
                /** @var ItineraryItem $item */
                $distance = DistanceService::haversineKm(
                    $currentLat,
                    $currentLng,
                    $item->destination->latitude,
                    $item->destination->longitude
                );

                if ($nearestDistance === null || $distance < $nearestDistance) {
                    $nearestDistance = $distance;
                    $nearestIndex = $index;
                }
            }

            /** @var ItineraryItem $nearest */
            $nearest = $remaining[$nearestIndex];
            $sorted[] = $nearest;
            $currentLat = $nearest->destination->latitude;
            $currentLng = $nearest->destination->longitude;

            unset($remaining[$nearestIndex]);
            $remaining = array_values($remaining);
        }

        return collect($sorted);
    }
}
