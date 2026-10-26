<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AiItineraryPlannerService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use RuntimeException;

class AiItineraryController extends Controller
{
    public function create(): View
    {
        return view('itineraries.ai-create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, AiItineraryPlannerService $planner): View|RedirectResponse
    {
        $throttleKey = 'ai-itinerary:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withInput()->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
        }

        RateLimiter::hit($throttleKey, 120);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'budget_per_day' => ['nullable', 'integer', 'min:0'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalDays = (int) $startDate->diffInDays($endDate) + 1;

        if ($totalDays > 7) {
            return back()->withInput()->with('error', 'Maksimal 7 hari untuk satu itinerary buatan AI.');
        }

        try {
            $days = $planner->plan([
                'interests' => $data['interests'] ?? [],
                'notes' => $data['notes'] ?? null,
                'budget_per_day' => $data['budget_per_day'] ?? null,
                'total_days' => $totalDays,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        if (empty($days)) {
            return back()->withInput()->with('error', 'AI tidak berhasil menyusun itinerary untuk preferensi ini. Coba ubah preferensimu.');
        }

        $itinerary = DB::transaction(function () use ($data, $startDate, $endDate, $days) {
            $itinerary = auth()->user()->itineraries()->create([
                'title' => $data['title'] ?: 'Itinerary AI - '.$startDate->translatedFormat('d M Y'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => 'planned',
            ]);

            foreach ($days as $dayNumber => $items) {
                foreach (array_values($items) as $sortOrder => $item) {
                    $itinerary->items()->create([
                        'destination_id' => $item['destination']->id,
                        'day_number' => $dayNumber,
                        'start_time' => $item['start_time'],
                        'duration_minutes' => $item['duration_minutes'],
                        'sort_order' => $sortOrder,
                        'notes' => $item['note'],
                    ]);
                }
            }

            return $itinerary;
        });

        return redirect()->route('itineraries.show', $itinerary)
            ->with('status', 'Itinerary berhasil disusun otomatis oleh AI. Silakan cek dan sesuaikan kalau perlu.');
    }
}
