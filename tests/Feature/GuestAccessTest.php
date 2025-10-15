<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_view_the_paginated_list_of_upcoming_events(): void
    {
        // Past event (should NOT be visible)
        Event::factory()->create([
            'title' => 'Past Event',
            'event_date' => Carbon::now()->subDays(2),
        ]);

        // Future events (should be visible)
        $future1 = Event::factory()->create([
            'title' => 'Future Event A',
            'event_date' => Carbon::now()->addDays(3),
        ]);

        $future2 = Event::factory()->create([
            'title' => 'Future Event B',
            'event_date' => Carbon::now()->addDays(10),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText('Upcoming Events');
        $response->assertSeeText($future1->title);
        $response->assertSeeText($future2->title);
        $response->assertDontSeeText('Past Event');
    }

    /** @test */
    public function a_guest_can_view_a_specific_event_details_page(): void
    {
        $event = Event::factory()->create([
            'title' => 'Public Details Event',
            'description' => 'Visible to anyone',
            'event_date' => Carbon::now()->addWeek(),
        ]);

        $response = $this->get('/event/' . $event->id);

        $response->assertStatus(200);
        $response->assertSeeText('Public Details Event');
        $response->assertSeeText('Visible to anyone');
    }

    /** @test */
    public function a_guest_is_redirected_when_accessing_protected_routes(): void
    {
        // Guests should be redirected to login for protected pages
        $this->get('/events/create')->assertRedirect('/login');
        $this->get('/organiser/dashboard')->assertRedirect('/login');
        $this->get('/bookings')->assertRedirect('/login');

        // Posting to book should also redirect to login
        $someEvent = Event::factory()->create([
            'event_date' => Carbon::now()->addDays(2),
        ]);
        $this->post('/event/' . $someEvent->id . '/book')->assertRedirect('/login');
    }

    /** @test */
    public function a_guest_cannot_see_action_buttons_on_event_details_page(): void
    {
        $event = Event::factory()->create([
            'title' => 'No Actions For Guests',
            'event_date' => Carbon::now()->addDays(5),
        ]);

        $response = $this->get('/event/' . $event->id);

        $response->assertStatus(200);

        // Buttons/links that should be hidden from guests
        $response->assertDontSee('Edit');          // organiser-only
        $response->assertDontSee('Delete');        // organiser-only
        $response->assertDontSee('My Dashboard');  // organiser-only
        $response->assertDontSee('My Bookings');   // attendee-only

        // ✅ Allow “log in to book” text but ensure there’s no actual form or button
        $response->assertDontSee('<form');
        $response->assertDontSee('<button');
    }
}
