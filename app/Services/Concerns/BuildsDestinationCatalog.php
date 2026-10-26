<?php

namespace App\Services\Concerns;

use App\Models\Destination;
use App\Services\DistanceService;
use Illuminate\Support\Collection;

trait BuildsDestinationCatalog
{
    /**
     * Ringkas katalog destinasi aktif menjadi array kompak untuk dikirim
     * ke prompt LLM, opsional menyertakan jarak dari titik lat/lng.
     * Field "sedang_buka" mencerminkan status pada saat prompt dibuat
     * (bukan untuk tanggal tertentu di masa depan) — pemanggil yang
     * merencanakan tanggal mendatang sebaiknya menginstruksikan LLM
     * untuk mengabaikan field ini dan memakai "jam_operasional" saja.
     */
    protected function buildDestinationCatalog(Collection $destinations, ?float $lat, ?float $lng): Collection
    {
        return $destinations->map(function (Destination $destination) use ($lat, $lng) {
            $distanceKm = null;

            if ($lat !== null && $lng !== null) {
                $distanceKm = round(DistanceService::haversineKm($lat, $lng, $destination->latitude, $destination->longitude), 1);
            }

            return [
                'id' => $destination->id,
                'nama' => $destination->name,
                'kategori' => $destination->category->name,
                'harga' => $destination->price_min === 0 && $destination->price_max === 0
                    ? 'Gratis'
                    : 'Rp'.number_format($destination->price_min, 0, ',', '.').'-Rp'.number_format($destination->price_max, 0, ',', '.'),
                'jam_operasional' => substr($destination->open_time, 0, 5).'-'.substr($destination->close_time, 0, 5),
                'sedang_buka' => $destination->isOpenNow() ? 'ya' : 'tidak',
                'alamat' => $destination->address,
                'jarak_km' => $distanceKm,
                'deskripsi' => $destination->description,
            ];
        });
    }
}
