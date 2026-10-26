<?php

namespace App\Console\Commands;

use App\Models\Itinerary;
use App\Notifications\ItineraryReminderNotification;
use Illuminate\Console\Command;

class SendItineraryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-itinerary-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat (email + notifikasi dalam-aplikasi) H-1 untuk itinerary yang mulai besok, ke pemilik dan seluruh kolaborator.';

    public function handle(): int
    {
        $tomorrow = today()->addDay();

        $itineraries = Itinerary::query()
            ->whereDate('start_date', $tomorrow)
            ->whereNull('reminder_sent_at')
            ->with(['user', 'collaborators'])
            ->get();

        if ($itineraries->isEmpty()) {
            $this->info('Tidak ada itinerary yang mulai besok ('.$tomorrow->toDateString().'). Tidak ada pengingat dikirim.');

            return self::SUCCESS;
        }

        foreach ($itineraries as $itinerary) {
            $recipients = collect([$itinerary->user])
                ->merge($itinerary->collaborators)
                ->unique('id');

            foreach ($recipients as $recipient) {
                $recipient->notify(new ItineraryReminderNotification($itinerary));
            }

            $itinerary->forceFill(['reminder_sent_at' => now()])->save();

            $this->info('Pengingat terkirim: "'.$itinerary->title.'" ke '.$recipients->count().' orang.');
        }

        return self::SUCCESS;
    }
}
