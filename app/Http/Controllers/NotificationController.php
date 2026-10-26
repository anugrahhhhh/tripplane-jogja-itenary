<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('notifications.index', [
            'notifications' => auth()->user()->notifications()->paginate(15),
        ]);
    }

    /**
     * Tandai satu notifikasi terbaca lalu arahkan ke itinerary terkait.
     */
    public function markRead(string $notification): RedirectResponse
    {
        $record = auth()->user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        if (isset($record->data['itinerary_id'])) {
            return redirect()->route('itineraries.show', $record->data['itinerary_id']);
        }

        return redirect()->route('home');
    }

    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
