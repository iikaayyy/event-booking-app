<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function organiser_can_assign_category_to_event(): void
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $eventData = [
            'title' => 'Music Night',
            'category' => 'Music',
            'event_date' => now()->addDays(5),
            'location' => 'Brisbane',
            'capacity' => 100,
        ];

        $this->actingAs($organiser)->post('/events', $eventData);
        $this->assertDatabaseHas('events', ['title' => 'Music Night', 'category' => 'Music']);
    }

    /** @test */
    public function attendees_can_view_event_categories(): void
    {
        $event = Event::factory()->create(['category' => 'Technology']);
        $response = $this->get('/');
        $response->assertSee('Technology');
    }

    /** @test */
    public function filtering_by_category_returns_only_matching_events(): void
    {
        Event::factory()->create(['title' => 'Tech Expo', 'category' => 'Tech']);
        Event::factory()->create(['title' => 'Art Fair', 'category' => 'Art']);

        $response = $this->get('/events/filter?category=Tech');

        $response->assertSee('Tech Expo');
        $response->assertDontSee('Art Fair');
    }
}
