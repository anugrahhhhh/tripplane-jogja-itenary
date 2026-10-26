<?php

namespace App\Services;

use App\Models\Destination;
use App\Services\Concerns\BuildsDestinationCatalog;
use App\Services\Concerns\CallsGeminiApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AiItineraryPlannerService
{
    use BuildsDestinationCatalog;
    use CallsGeminiApi;

    /**
     * Minta LLM menyusun itinerary multi-hari lengkap (destinasi, urutan,
     * dan jam kunjungan per hari) berdasarkan preferensi dan konteks lokasi
     * pengguna.
     *
     * @param  array{interests?: array<int, string>, notes?: string|null, budget_per_day?: int|null, total_days: int, lat?: float|null, lng?: float|null}  $preferences
     * @return array<int, array<int, array{destination: Destination, start_time: string, duration_minutes: int, note: string}>> daftar butir per day_number (1..total_days)
     */
    public function plan(array $preferences): array
    {
        $destinations = Destination::where('is_active', true)->with('category')->get();

        $catalog = $this->buildDestinationCatalog($destinations, $preferences['lat'] ?? null, $preferences['lng'] ?? null);

        $prompt = $this->buildPrompt($preferences, $catalog);

        $decoded = $this->requestGeminiJson($prompt, $this->responseSchema());

        if (! isset($decoded['days']) || ! is_array($decoded['days'])) {
            Log::error('Respons Gemini tidak sesuai format itinerary yang diharapkan', ['decoded' => $decoded]);

            throw new RuntimeException('Respons AI tidak dapat diproses. Coba lagi.');
        }

        return $this->mapToDestinations($decoded['days'], $destinations, $preferences['total_days']);
    }

    private function buildPrompt(array $preferences, Collection $catalog): string
    {
        $minat = ! empty($preferences['interests']) ? implode(', ', $preferences['interests']) : 'tidak ditentukan';
        $catatan = trim((string) ($preferences['notes'] ?? '')) !== '' ? $preferences['notes'] : '-';
        $budget = $preferences['budget_per_day'] ?? null;
        $budgetText = $budget ? 'Rp'.number_format((int) $budget, 0, ',', '.').' per hari' : 'tidak ditentukan';
        $totalDays = $preferences['total_days'];
        $lokasiInfo = ($preferences['lat'] ?? null) !== null
            ? 'Titik keberangkatan pengguna sudah diketahui. Field "jarak_km" pada tiap destinasi dihitung dari titik tersebut, jadikan pertimbangan kasar kedekatan.'
            : 'Titik keberangkatan pengguna tidak diketahui, abaikan jarak dalam pertimbangan.';

        $catalogJson = $catalog->values()->toJson();

        return <<<PROMPT
            Kamu adalah asisten penyusun itinerary wisata untuk aplikasi JogjaTrip yang berfokus di Kota Yogyakarta.

            Preferensi pengguna:
            - Minat/kategori: {$minat}
            - Catatan tambahan: {$catatan}
            - Budget: {$budgetText}
            - Jumlah hari perjalanan: {$totalDays} hari
            - {$lokasiInfo}

            Berikut daftar destinasi yang tersedia (JSON array, field "id" adalah kunci utama untuk dirujuk kembali; field "sedang_buka" hanya mencerminkan status saat prompt ini dibuat dan TIDAK relevan untuk tanggal di masa depan — abaikan field itu dan gunakan "jam_operasional" saja untuk menyusun jadwal):
            {$catalogJson}

            Tugasmu: susun itinerary lengkap untuk {$totalDays} hari. Untuk setiap hari, pilih 3-5 destinasi dengan ketentuan:
            - Waktu kunjungan (start_time dan durasi) berurutan kronologis dalam satu hari, dengan jeda perjalanan yang wajar antar destinasi.
            - Sesuai jam_operasional destinasi (jangan jadwalkan di luar jam buka).
            - Bervariasi kategori dalam satu hari, kecuali preferensi pengguna secara eksplisit meminta fokus sempit.
            - Tidak mengulang destinasi yang sama di lebih dari satu hari.
            - Sesuai budget jika ditentukan.
            - Mempertimbangkan jarak antar destinasi dalam hari yang sama supaya rute masuk akal, tidak lompat jauh bolak-balik.

            Untuk tiap destinasi terpilih, sertakan "note": alasan singkat (1 kalimat, Bahasa Indonesia) kenapa destinasi ini cocok ditempatkan di slot waktu itu.

            Balas HANYA dengan JSON valid tanpa teks lain di luar JSON, dengan skema:
            {"days": [{"day_number": 1, "items": [{"destination_id": <id>, "start_time": "HH:MM", "duration_minutes": <menit>, "note": "<alasan>"}]}]}

            day_number harus berurutan dari 1 sampai {$totalDays}, dan setiap angka hari dalam rentang itu harus muncul di array "days" dengan minimal 2 destinasi.
            PROMPT;
    }

    private function responseSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'days' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'day_number' => ['type' => 'INTEGER'],
                            'items' => [
                                'type' => 'ARRAY',
                                'items' => [
                                    'type' => 'OBJECT',
                                    'properties' => [
                                        'destination_id' => ['type' => 'INTEGER'],
                                        'start_time' => ['type' => 'STRING'],
                                        'duration_minutes' => ['type' => 'INTEGER'],
                                        'note' => ['type' => 'STRING'],
                                    ],
                                    'required' => ['destination_id', 'start_time', 'duration_minutes', 'note'],
                                ],
                            ],
                        ],
                        'required' => ['day_number', 'items'],
                    ],
                ],
            ],
            'required' => ['days'],
        ];
    }

    /**
     * @param  array<int, array{day_number: int, items: array<int, array{destination_id: int, start_time: string, duration_minutes: int, note: string}>}>  $days
     * @return array<int, array<int, array{destination: Destination, start_time: string, duration_minutes: int, note: string}>>
     */
    private function mapToDestinations(array $days, Collection $destinations, int $totalDays): array
    {
        $result = [];

        foreach ($days as $day) {
            $dayNumber = (int) ($day['day_number'] ?? 0);

            if ($dayNumber < 1 || $dayNumber > $totalDays) {
                continue;
            }

            $items = [];

            foreach ((array) ($day['items'] ?? []) as $item) {
                $destination = $destinations->firstWhere('id', $item['destination_id'] ?? null);

                if (! $destination) {
                    continue;
                }

                $startTime = $item['start_time'] ?? null;

                if (! is_string($startTime) || ! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $startTime)) {
                    continue;
                }

                $duration = (int) ($item['duration_minutes'] ?? 60);
                $duration = max(15, min(600, $duration ?: 60));

                $items[] = [
                    'destination' => $destination,
                    'start_time' => $startTime,
                    'duration_minutes' => $duration,
                    'note' => (string) ($item['note'] ?? ''),
                ];
            }

            if (! empty($items)) {
                $result[$dayNumber] = $items;
            }
        }

        return $result;
    }
}
