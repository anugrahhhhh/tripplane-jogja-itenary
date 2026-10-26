<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryCollaborationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_edit_and_delete_the_itinerary(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();

        $this->actingAs($owner)->get(route('itineraries.show', $itinerary))->assertOk();
        $this->actingAs($owner)->get(route('itineraries.edit', $itinerary))->assertOk();
        $this->actingAs($owner)->delete(route('itineraries.destroy', $itinerary))
            ->assertRedirect(route('itineraries.index'));

        $this->assertDatabaseMissing('itineraries', ['id' => $itinerary->id]);
    }

    public function test_collaborator_can_view_and_add_items_but_not_edit_or_delete(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $collaborator = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();
        $itinerary->collaborators()->attach($collaborator->id);
        $destination = Destination::factory()->create();

        $this->actingAs($collaborator)->get(route('itineraries.show', $itinerary))->assertOk();

        $this->actingAs($collaborator)->post(route('itinerary-items.store', $itinerary), [
            'destination_id' => $destination->id,
            'day_number' => 1,
            'start_time' => '09:00',
            'duration_minutes' => 60,
        ])->assertRedirect();

        $this->assertDatabaseHas('itinerary_items', [
            'itinerary_id' => $itinerary->id,
            'destination_id' => $destination->id,
        ]);

        $this->actingAs($collaborator)->get(route('itineraries.edit', $itinerary))->assertForbidden();
        $this->actingAs($collaborator)->delete(route('itineraries.destroy', $itinerary))->assertForbidden();
        $this->actingAs($collaborator)->post(route('itinerary-collaborators.store', $itinerary), [
            'email' => User::factory()->create()->email,
        ])->assertForbidden();

        $this->assertDatabaseHas('itineraries', ['id' => $itinerary->id]);
    }

    public function test_a_stranger_cannot_access_the_itinerary_at_all(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $stranger = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();

        $this->actingAs($stranger)->get(route('itineraries.show', $itinerary))->assertForbidden();
    }

    public function test_owner_can_invite_and_remove_a_collaborator(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $friend = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();

        $this->actingAs($owner)->post(route('itinerary-collaborators.store', $itinerary), [
            'email' => $friend->email,
        ])->assertRedirect();

        $this->assertTrue($itinerary->fresh()->collaborators->contains('id', $friend->id));

        $this->actingAs($owner)->delete(route('itinerary-collaborators.destroy', [$itinerary, $friend]))
            ->assertRedirect();

        $this->assertFalse($itinerary->fresh()->collaborators->contains('id', $friend->id));
    }

    public function test_collaborator_can_leave_on_their_own(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $collaborator = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();
        $itinerary->collaborators()->attach($collaborator->id);

        $this->actingAs($collaborator)
            ->delete(route('itinerary-collaborators.destroy', [$itinerary, $collaborator]))
            ->assertRedirect(route('itineraries.index'));

        $this->assertFalse($itinerary->fresh()->collaborators->contains('id', $collaborator->id));
        $this->actingAs($collaborator)->get(route('itineraries.show', $itinerary))->assertForbidden();
    }

    public function test_cannot_invite_an_admin_or_an_already_invited_collaborator(): void
    {
        $owner = User::factory()->create(['role' => 'traveler']);
        $admin = User::factory()->create(['role' => 'admin']);
        $friend = User::factory()->create(['role' => 'traveler']);
        $itinerary = Itinerary::factory()->for($owner)->create();
        $itinerary->collaborators()->attach($friend->id);

        $this->actingAs($owner)->post(route('itinerary-collaborators.store', $itinerary), [
            'email' => $admin->email,
        ])->assertSessionHasErrors('email');

        $this->actingAs($owner)->post(route('itinerary-collaborators.store', $itinerary), [
            'email' => $friend->email,
        ])->assertSessionHasErrors('email');
    }
}
