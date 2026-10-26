<?php

namespace Tests\Feature\Console;

use App\Models\Itinerary;
use App\Models\User;
use App\Notifications\ItineraryReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendItineraryRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_owner_and_collaborators_for_itineraries_starting_tomorrow(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $itinerary = Itinerary::factory()->for($owner)->create([
            'start_date' => today()->addDay(),
            'end_date' => today()->addDays(2),
        ]);
        $itinerary->collaborators()->attach($collaborator->id);

        $this->artisan('app:send-itinerary-reminders')->assertSuccessful();

        Notification::assertSentTo([$owner, $collaborator], ItineraryReminderNotification::class);
        $this->assertNotNull($itinerary->fresh()->reminder_sent_at);
    }

    public function test_it_does_not_notify_for_itineraries_not_starting_tomorrow(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        Itinerary::factory()->for($owner)->create([
            'start_date' => today()->addDays(5),
            'end_date' => today()->addDays(6),
        ]);

        $this->artisan('app:send-itinerary-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_it_does_not_send_duplicate_reminders_on_a_second_run(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        Itinerary::factory()->for($owner)->create([
            'start_date' => today()->addDay(),
            'end_date' => today()->addDays(2),
        ]);

        $this->artisan('app:send-itinerary-reminders')->assertSuccessful();
        Notification::assertSentToTimes($owner, ItineraryReminderNotification::class, 1);

        $this->artisan('app:send-itinerary-reminders')->assertSuccessful();
        Notification::assertSentToTimes($owner, ItineraryReminderNotification::class, 1);
    }
}
