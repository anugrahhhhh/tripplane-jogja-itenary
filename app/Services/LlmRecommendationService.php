<?php

namespace App\Services;

use App\Models\Destination;
use App\Services\Concerns\BuildsDestinationCatalog;
use App\Services\Concerns\CallsGeminiApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LlmRecommendationService
{
    use BuildsDestinationCatalog;
    use CallsGeminiApi;

    /**
     * Minta rekomendasi destinasi yang dipersonalisasi dari LLM berdasarkan
     * preferensi pengguna (minat, budget, durasi) dan konteks lokasi
     * (opsional: lat/lng pengguna saat ini).
     *
     * @param  array{interests?: array<int, string>, notes?: string, budget_max?: int|null, duration?: string|null, lat?: float|null, lng?: float|null}  $preferences
     * @return array<int, array{destination: Destination, reason: string}>
     */
    public function recommend(array $preferences): array
    {
        $destinations = Destination::where('is_active', true)->with('category')->get();

        $catalog = $this->buildDestinationCatalog($destinations, $preferences['lat'] ?? null, $preferences['lng'] ?? null);

        $prompt = $this->buildPrompt($preferences, $catalog);

        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'recommendations' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'destination_id' => ['type' => 'INTEGER'],
                            'reason' => ['type' => 'STRING'],
                        ],
                        'required' => ['destination_id', 'reason'],
                    ],
                ],
            ],
            'required' => ['recommendations'],
        ];

        $decoded = $this->requestGeminiJson($prompt, $schema);

        if (! isset($decoded['recommendations']) || ! is_array($decoded['recommendations'])) {
            Log::error('Respons Gemini tidak sesuai format rekomendasi yang diharapkan', ['decoded' => $decoded]);

            throw new RuntimeException('Respons AI tidak dapat diproses. Coba lagi.');
        }

        return $this->mapToDestinations($decoded['recommendations'], $destinations);
    }

    private function buildPrompt(array $preferences, Collection $catalog): string
    {
        $minat = ! empty($preferences['interests']) ? implode(', ', $preferences['interests']) : 'tidak ditentukan';
        $catatan = trim((string) ($preferences['notes'] ?? '')) !== '' ? $preferences['notes'] : '-';
        $budget = $preferences['budget_max'] ?? null;
        $budgetText = $budget ? 'Rp'.number_format((int) $budget, 0, ',', '.') : 'tidak ditentukan';
        $durasi = $preferences['duration'] ?? 'tidak ditentukan';
        $lokasiInfo = ($preferences['lat'] ?? null) !== null
            ? 'Pengguna membagikan lokasinya saat ini. Field "jarak_km" pada tiap destinasi sudah dihitung dari titik tersebut.'
            : 'Pengguna tidak membagikan lokasi saat ini, abaikan jarak dalam pertimbangan.';

        $catalogJson = $catalog->values()->toJson();

        return <<<PROMPT
            Kamu adalah asisten rekomendasi wisata untuk aplikasi JogjaTrip yang berfokus di Kota Yogyakarta.

            Preferensi pengguna:
            - Minat/kategori: {$minat}
            - Catatan tambahan dari pengguna: {$catatan}
            - Budget maksimal per aktivitas: {$budgetText}
            - Durasi kunjungan yang tersedia: {$durasi}
            - {$lokasiInfo}

            Berikut daftar destinasi yang tersedia (JSON array, field "id" adalah kunci utama untuk dirujuk kembali):
            {$catalogJson}

            Tugasmu: pilih 6 destinasi paling relevan dari daftar di atas berdasarkan preferensi pengguna, urutkan dari paling relevan ke paling kurang relevan. Pertimbangkan kesesuaian minat, kesesuaian budget, status buka/tutup jika relevan dengan durasi kunjungan, dan jarak jika tersedia. Untuk setiap destinasi terpilih, tulis alasan personalisasi singkat (1-2 kalimat, Bahasa Indonesia, spesifik terhadap preferensi pengguna ini, jangan generik).

            Balas HANYA dengan JSON valid tanpa teks lain di luar JSON, persis dengan skema berikut:
            {"recommendations": [{"destination_id": <id dari daftar di atas>, "reason": "<alasan personalisasi>"}]}
            PROMPT;
    }

    /**
     * @param  array<int, array{destination_id: int, reason: string}>  $recommendations
     * @return array<int, array{destination: Destination, reason: string}>
     */
    private function mapToDestinations(array $recommendations, Collection $destinations): array
    {
        $result = [];

        foreach ($recommendations as $item) {
            $destination = $destinations->firstWhere('id', $item['destination_id'] ?? null);

            if ($destination) {
                $result[] = [
                    'destination' => $destination,
                    'reason' => (string) ($item['reason'] ?? ''),
                ];
            }
        }

        return $result;
    }
}
