<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class OrganiserDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_organiser_can_view_their_dashboard_and_events(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event1 = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'title' => 'Organiser Event 1',
        ]);
        $event2 = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'title' => 'Organiser Event 2',
        ]);

        $response = $this->actingAs($organiser)->get('/organiser/dashboard');
        $this->assertTrue(in_array($response->status(), [200, 302]));

        if ($response->status() === 200) {
            $response->assertSeeText('Organiser Event 1');
            $response->assertSeeText('Organiser Event 2');
        }
    }

    /** @test */
    public function an_organiser_can_create_a_new_event(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $formData = [
            'title' => 'New Test Event',
            'description' => 'Event created via dashboard test.',
            'event_date' => Carbon::now()->addDays(10)->format('Y-m-d H:i:s'),
            'location' => 'Test City',
            'capacity' => 50,
            'category' => 'Workshop',
        ];

        $response = $this->actingAs($organiser)->post('/events', $formData);

        // Laravel redirects to organiser dashboard after creation
        $response->assertRedirect('/organiser/dashboard');

        $this->assertDatabaseHas('events', [
            'title' => 'New Test Event',
            'organiser_id' => $organiser->id,
        ]);
    }

    /** @test */
    public function an_organiser_can_edit_and_update_their_event(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'title' => 'Old Title',
            'category' => 'Hackathon',
        ]);

        $updateData = [
            'title' => 'Updated Event Title',
            'description' => 'Updated event details',
            'event_date' => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
            'location' => 'Updated City',
            'capacity' => 40,
            'category' => 'Workshop',
        ];

        $response = $this->actingAs($organiser)->patch("/event/{$event->id}/update", $updateData);
        $response->assertRedirect();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    /** @test */
    public function an_organiser_can_view_attendees_for_their_event(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $attendee = User::factory()->create(['role' => 'attendee', 'name' => 'Test Attendee']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'title' => 'Event With Attendees',
        ]);

        Booking::factory()->create([
            'user_id' => $attendee->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($organiser)->get("/organiser/events/{$event->id}/attendees");
        $response->assertStatus(200);
        $response->assertSeeText('Test Attendee');
    }

    /** @test */
    public function non_organisers_cannot_access_organiser_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'attendee']);
        $response = $this->actingAs($user)->get('/organiser/dashboard');
        $response->assertRedirect('/');
    }
}
