<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\LlmRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use RuntimeException;

class RecommendationController extends Controller
{
    public function create(): View
    {
        return view('recommendations.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, LlmRecommendationService $llm): View|RedirectResponse
    {
        $throttleKey = 'ai-recommendation:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withInput()->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
        }

        RateLimiter::hit($throttleKey, 60);

        $data = $request->validate([
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'budget_max' => ['nullable', 'integer', 'min:0'],
            'duration' => ['nullable', 'string', 'in:singkat,setengah-hari,seharian'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $durationLabels = [
            'singkat' => '1-2 jam',
            'setengah-hari' => 'Setengah hari (3-5 jam)',
            'seharian' => 'Seharian (6+ jam)',
        ];

        try {
            $recommendations = $llm->recommend([
                'interests' => $data['interests'] ?? [],
                'notes' => $data['notes'] ?? null,
                'budget_max' => $data['budget_max'] ?? null,
                'duration' => $durationLabels[$data['duration'] ?? ''] ?? null,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return view('recommendations.result', [
            'recommendations' => $recommendations,
            'preferences' => $data,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
        ]);
    }
}
