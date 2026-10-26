<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::with(['user', 'destination'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_visible', $request->query('status') === 'tampil');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function update(Review $review): RedirectResponse
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with('status', $review->is_visible ? 'Ulasan ditampilkan kembali.' : 'Ulasan disembunyikan.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Ulasan berhasil dihapus.');
    }
}
