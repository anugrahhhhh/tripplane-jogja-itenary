<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Services\DistanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NearbyController extends Controller
{
    public function index(): View
    {
        return view('nearby.index');
    }

    /**
     * Endpoint AJAX untuk mencari destinasi terdekat dari titik pengguna
     * (dipanggil dari JavaScript setelah browser memberi izin lokasi, atau
     * setelah pengguna memilih titik di peta).
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'in:1,3,5'],
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        $radius = (float) $request->query('radius', 3);

        $destinations = Destination::query()
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->map(function (Destination $destination) use ($lat, $lng) {
                $distance = DistanceService::haversineKm($lat, $lng, $destination->latitude, $destination->longitude);

                return [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                    'category' => $destination->category->name,
                    'cover_photo' => $destination->cover_photo,
                    'latitude' => $destination->latitude,
                    'longitude' => $destination->longitude,
                    'price_min' => $destination->price_min,
                    'price_max' => $destination->price_max,
                    'distance_km' => round($distance, 2),
                    'duration_minutes' => DistanceService::estimateDurationMinutes($distance),
                    'is_open' => $destination->isOpenNow(),
                ];
            })
            ->filter(fn (array $item) => $item['distance_km'] <= $radius)
            ->sortBy('distance_km')
            ->values();

        return response()->json(['data' => $destinations]);
    }
}
