<?php

namespace App\Services\Concerns;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

trait CallsGeminiApi
{
    /**
     * Kirim prompt ke Gemini API dan kembalikan hasil JSON yang sudah
     * di-decode. Structured output dipaksa lewat responseSchema.
     *
     * Ketahanan terhadap server Gemini yang sedang sibuk (503), kena rate
     * limit (429), atau model dihentikan Google (404): tiap model dicoba
     * dengan retry singkat, dan kalau gagal karena salah satu dari ketiga
     * hal itu, otomatis pindah ke model cadangan dari keluarga model lain
     * (kemungkinan besar kapasitas servernya terpisah) sebelum benar-benar
     * menyerah ke pengguna. Error lain (mis. 400 permintaan salah, 401/403
     * kredensial) dianggap bukan soal kapasitas/ketersediaan model, jadi
     * langsung berhenti tanpa mencoba model lain.
     *
     * @return array<string, mixed>
     */
    protected function requestGeminiJson(string $prompt, array $responseSchema): array
    {
        $apiKey = config('services.gemini.key');

        if (! $apiKey) {
            throw new RuntimeException('GEMINI_API_KEY belum diatur di file .env.');
        }

        $retryableStatuses = [404, 429, 503];
        $models = $this->geminiModelChain();
        $attempted = [];
        $lastResponse = null;
        $lastConnectionError = null;

        foreach ($models as $model) {
            $attempted[] = $model;

            try {
                $response = $this->callGeminiModel($apiKey, $model, $prompt, $responseSchema);
            } catch (ConnectionException $e) {
                // Timeout / gagal konek sama sekali (bukan respons error
                // HTTP biasa) — anggap seperti server sibuk, coba model lain.
                $lastConnectionError = $e;
                $lastResponse = null;

                Log::warning('Koneksi ke model Gemini gagal/timeout, pindah ke model cadangan', [
                    'model_gagal' => $model,
                    'pesan' => $e->getMessage(),
                ]);

                continue;
            }

            if ($response->successful()) {
                return $this->decodeGeminiResponse($response);
            }

            $lastResponse = $response;
            $lastConnectionError = null;

            if (! in_array($response->status(), $retryableStatuses, true)) {
                break;
            }

            Log::warning('Model Gemini gagal (sibuk/dihentikan), pindah ke model cadangan', [
                'model_gagal' => $model,
                'status' => $response->status(),
            ]);
        }

        Log::error('Semua model Gemini gagal dihubungi', [
            'models_dicoba' => $attempted,
            'status_terakhir' => $lastResponse?->status(),
            'body_terakhir' => $lastResponse?->body(),
            'connection_error_terakhir' => $lastConnectionError?->getMessage(),
        ]);

        throw new RuntimeException('Server AI sedang sangat sibuk. Coba lagi dalam beberapa menit.');
    }

    /**
     * Model utama (dari .env) diikuti model cadangan. Duplikat dibuang
     * supaya tidak mencoba model yang sama dua kali.
     *
     * @return array<int, string>
     */
    private function geminiModelChain(): array
    {
        $primary = config('services.gemini.model');
        $fallbacks = array_filter(array_map('trim', explode(',', (string) config('services.gemini.fallback_models'))));

        return array_values(array_unique(array_filter([$primary, ...$fallbacks])));
    }

    private function callGeminiModel(string $apiKey, string $model, string $prompt, array $responseSchema): Response
    {
        return Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'content-type' => 'application/json',
        ])->timeout(20)->retry(2, 1000, function ($exception) {
            if ($exception instanceof RequestException) {
                return in_array($exception->response->status(), [429, 503], true);
            }

            // Timeout / koneksi putus di tengah jalan — layak dicoba ulang
            // sekali lagi sebelum menyerah ke model ini dan pindah cadangan.
            return $exception instanceof ConnectionException;
        }, throw: false)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseSchema' => $responseSchema,
            ],
        ]);
    }

    private function decodeGeminiResponse(Response $response): array
    {
        $text = (string) $response->json('candidates.0.content.parts.0.text', '');
        $decoded = json_decode($this->extractJson($text), true);

        if (! is_array($decoded)) {
            Log::error('Respons Gemini bukan JSON valid', ['text' => $text]);

            throw new RuntimeException('Respons AI tidak dapat diproses. Coba lagi.');
        }

        return $decoded;
    }

    private function extractJson(string $text): string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start === false || $end === false || $end < $start) {
            return $text;
        }

        return substr($text, $start, $end - $start + 1);
    }
}
