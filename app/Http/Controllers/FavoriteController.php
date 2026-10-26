<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = auth()->user()->favorites()
            ->with('destination.category')
            ->latest()
            ->paginate(12);

        return view('favorites.index', ['favorites' => $favorites]);
    }

    public function store(Destination $destination): RedirectResponse
    {
        auth()->user()->favorites()->firstOrCreate(['destination_id' => $destination->id]);

        return back()->with('status', 'Ditambahkan ke favorit.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        auth()->user()->favorites()->where('destination_id', $destination->id)->delete();

        return back()->with('status', 'Dihapus dari favorit.');
    }
}
