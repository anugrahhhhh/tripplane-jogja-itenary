<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('destinations')->orderBy('name')->get();

        $popular = Destination::query()
            ->where('is_active', true)
            ->withCount(['visibleReviews as reviews_count'])
            ->withAvg('visibleReviews as reviews_avg_rating', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->limit(8)
            ->get();

        return view('home', [
            'categories' => $categories,
            'popular' => $popular,
        ]);
    }
}
