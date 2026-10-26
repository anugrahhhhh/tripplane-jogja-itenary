<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ItineraryCollaboratorController extends Controller
{
    /**
     * Undang traveler lain (harus sudah terdaftar, karena aplikasi ini
     * belum mengirim email undangan sungguhan) sebagai kolaborator.
     * Hanya pemilik itinerary yang boleh mengundang.
     */
    public function store(Request $request, Itinerary $itinerary): RedirectResponse
    {
        abort_unless($itinerary->isOwnedBy(auth()->user()), 403);

        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Belum ada akun terdaftar dengan email ini.',
            ]);
        }

        if ($user->id === $itinerary->user_id) {
            throw ValidationException::withMessages([
                'email' => 'Ini email kamu sendiri, kamu sudah jadi pemilik itinerary ini.',
            ]);
        }

        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'email' => 'Akun Admin tidak bisa diundang sebagai kolaborator.',
            ]);
        }

        if ($itinerary->collaborators->contains('id', $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Traveler ini sudah jadi kolaborator di itinerary ini.',
            ]);
        }

        $itinerary->collaborators()->attach($user->id);

        return back()->with('status', $user->name.' berhasil diundang sebagai kolaborator.');
    }

    /**
     * Hapus kolaborator — boleh dilakukan oleh pemilik (mengeluarkan orang
     * lain) maupun oleh kolaborator itu sendiri (keluar dari itinerary).
     */
    public function destroy(Itinerary $itinerary, User $collaborator): RedirectResponse
    {
        $isOwner = $itinerary->isOwnedBy(auth()->user());
        $isSelf = auth()->id() === $collaborator->id;

        abort_unless($isOwner || $isSelf, 403);

        $itinerary->collaborators()->detach($collaborator->id);

        if ($isSelf && ! $isOwner) {
            return redirect()->route('itineraries.index')
                ->with('status', 'Kamu sudah keluar dari itinerary ini.');
        }

        return back()->with('status', $collaborator->name.' dikeluarkan dari kolaborator.');
    }
}
