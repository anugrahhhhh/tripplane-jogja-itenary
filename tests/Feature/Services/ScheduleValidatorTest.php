<?php

namespace Tests\Feature\Services;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Services\ScheduleValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_scheduled_within_operating_hours_has_no_warning(): void
    {
        $itinerary = Itinerary::factory()->create();
        $destination = Destination::factory()->create(['open_time' => '08:00', 'close_time' => '17:00']);

        $item = $itinerary->items()->create([
            'destination_id' => $destination->id,
            'day_number' => 1,
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'sort_order' => 0,
        ]);

        $warnings = ScheduleValidator::validateDay($itinerary->items()->with('destination')->get());

        $this->assertSame([], $warnings[$item->id]);
    }

    public function test_item_scheduled_outside_operating_hours_is_flagged(): void
    {
        $itinerary = Itinerary::factory()->create();
        $destination = Destination::factory()->create(['open_time' => '08:00', 'close_time' => '17:00']);

        $item = $itinerary->items()->create([
            'destination_id' => $destination->id,
            'day_number' => 1,
            'start_time' => '20:00',
            'duration_minutes' => 60,
            'sort_order' => 0,
        ]);

        $warnings = ScheduleValidator::validateDay($itinerary->items()->with('destination')->get());

        $this->assertNotEmpty($warnings[$item->id]);
        $this->assertStringContainsString('luar jam operasional', $warnings[$item->id][0]);
    }

    public function test_overlapping_items_are_flagged_on_both_sides(): void
    {
        $itinerary = Itinerary::factory()->create();
        $destinationA = Destination::factory()->create(['name' => 'Destinasi A', 'open_time' => '00:00', 'close_time' => '23:59']);
        $destinationB = Destination::factory()->create(['name' => 'Destinasi B', 'open_time' => '00:00', 'close_time' => '23:59']);

        $first = $itinerary->items()->create([
            'destination_id' => $destinationA->id,
            'day_number' => 1,
            'start_time' => '09:00',
            'duration_minutes' => 90, // berakhir 10:30
            'sort_order' => 0,
        ]);

        $second = $itinerary->items()->create([
            'destination_id' => $destinationB->id,
            'day_number' => 1,
            'start_time' => '10:00', // mulai sebelum yang pertama selesai -> bentrok
            'duration_minutes' => 60,
            'sort_order' => 1,
        ]);

        $warnings = ScheduleValidator::validateDay($itinerary->items()->with('destination')->get());

        $this->assertStringContainsString('bertabrakan', $warnings[$first->id][0]);
        $this->assertStringContainsString('bertabrakan', $warnings[$second->id][0]);
    }

    public function test_back_to_back_items_without_gap_do_not_conflict(): void
    {
        $itinerary = Itinerary::factory()->create();
        $destinationA = Destination::factory()->create(['open_time' => '00:00', 'close_time' => '23:59']);
        $destinationB = Destination::factory()->create(['open_time' => '00:00', 'close_time' => '23:59']);

        $first = $itinerary->items()->create([
            'destination_id' => $destinationA->id,
            'day_number' => 1,
            'start_time' => '09:00',
            'duration_minutes' => 60, // berakhir tepat 10:00
            'sort_order' => 0,
        ]);

        $second = $itinerary->items()->create([
            'destination_id' => $destinationB->id,
            'day_number' => 1,
            'start_time' => '10:00', // mulai tepat saat yang pertama selesai -> tidak bentrok
            'duration_minutes' => 60,
            'sort_order' => 1,
        ]);

        $warnings = ScheduleValidator::validateDay($itinerary->items()->with('destination')->get());

        $this->assertSame([], $warnings[$first->id]);
        $this->assertSame([], $warnings[$second->id]);
    }
}
