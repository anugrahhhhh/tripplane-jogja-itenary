<?php

namespace Tests\Feature\Services;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Services\NearestNeighborSorter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NearestNeighborSorterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_greedily_visits_the_nearest_unvisited_destination_each_step(): void
    {
        $itinerary = Itinerary::factory()->create();

        // Tiga titik segaris di sepanjang garis lintang 0: A=0, B=1, C=2 derajat bujur.
        $pointA = Destination::factory()->create(['latitude' => 0, 'longitude' => 0]);
        $pointB = Destination::factory()->create(['latitude' => 0, 'longitude' => 1]);
        $pointC = Destination::factory()->create(['latitude' => 0, 'longitude' => 2]);

        // Sengaja dibuat berurutan C, A, B supaya urutan awal bukan urutan geografis.
        $itemC = $itinerary->items()->create(['destination_id' => $pointC->id, 'day_number' => 1, 'start_time' => '08:00', 'duration_minutes' => 30, 'sort_order' => 0]);
        $itemA = $itinerary->items()->create(['destination_id' => $pointA->id, 'day_number' => 1, 'start_time' => '09:00', 'duration_minutes' => 30, 'sort_order' => 1]);
        $itemB = $itinerary->items()->create(['destination_id' => $pointB->id, 'day_number' => 1, 'start_time' => '10:00', 'duration_minutes' => 30, 'sort_order' => 2]);

        $items = $itinerary->items()->with('destination')->orderBy('sort_order')->get();

        $sorted = NearestNeighborSorter::sort($items);

        // Mulai dari item pertama (C), lalu B (tetangga terdekat C), lalu A.
        $this->assertSame([$itemC->id, $itemB->id, $itemA->id], $sorted->pluck('id')->all());
    }

    public function test_it_starts_from_the_given_point_instead_of_the_first_item(): void
    {
        $itinerary = Itinerary::factory()->create();

        $pointA = Destination::factory()->create(['latitude' => 0, 'longitude' => 0]);
        $pointB = Destination::factory()->create(['latitude' => 0, 'longitude' => 1]);
        $pointC = Destination::factory()->create(['latitude' => 0, 'longitude' => 2]);

        $itemC = $itinerary->items()->create(['destination_id' => $pointC->id, 'day_number' => 1, 'start_time' => '08:00', 'duration_minutes' => 30, 'sort_order' => 0]);
        $itemA = $itinerary->items()->create(['destination_id' => $pointA->id, 'day_number' => 1, 'start_time' => '09:00', 'duration_minutes' => 30, 'sort_order' => 1]);
        $itemB = $itinerary->items()->create(['destination_id' => $pointB->id, 'day_number' => 1, 'start_time' => '10:00', 'duration_minutes' => 30, 'sort_order' => 2]);

        $items = $itinerary->items()->with('destination')->orderBy('sort_order')->get();

        // Titik awal ada di longitude -1, paling dekat dengan A, lalu B, lalu C.
        $sorted = NearestNeighborSorter::sort($items, 0, -1);

        $this->assertSame([$itemA->id, $itemB->id, $itemC->id], $sorted->pluck('id')->all());
    }

    public function test_a_single_item_is_returned_unchanged(): void
    {
        $itinerary = Itinerary::factory()->create();
        $destination = Destination::factory()->create();
        $item = $itinerary->items()->create(['destination_id' => $destination->id, 'day_number' => 1, 'start_time' => '08:00', 'duration_minutes' => 30, 'sort_order' => 0]);

        $items = $itinerary->items()->with('destination')->get();
        $sorted = NearestNeighborSorter::sort($items);

        $this->assertSame([$item->id], $sorted->pluck('id')->all());
    }
}
