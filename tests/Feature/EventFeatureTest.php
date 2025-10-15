<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function home_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Upcoming Events');
    }

    /** @test */
    public function organiser_can_create_an_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $this->actingAs($organiser)
            ->post('/events', [
                'title' => 'Test Laravel Event',
                'category' => 'workshop',
                'description' => 'Testing creation of event',
                'event_date' => now()->addDays(5),
                'location' => 'Gold Coast',
                'capacity' => 50,
            ])
            ->assertRedirect('/organiser/dashboard');


        $this->assertDatabaseHas('events', [
            'title' => 'Test Laravel Event',
            'category' => 'Workshop',
        ]);
    }

    /** @test */
    public function attendee_can_book_an_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $attendee  = User::factory()->create(['role' => 'attendee']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser->id,
            'capacity' => 10,
        ]);

        $this->actingAs($attendee)
            ->post("/event/{$event->id}/book")
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $attendee->id,
            'event_id' => $event->id,
        ]);
    }

    /** @test */
    public function filters_return_only_upcoming_events()
    {
        $past = Event::factory()->create(['event_date' => now()->subDays(2)]);
        $future = Event::factory()->create(['event_date' => now()->addDays(5)]);

        $response = $this->get('/events/filter');
        $response->assertStatus(200);
        $response->assertSee($future->title);
        $response->assertDontSee($past->title);
    }
}
