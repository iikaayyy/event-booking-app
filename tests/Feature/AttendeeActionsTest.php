<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AttendeeActionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_book_an_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 10,
            'event_date' => Carbon::now()->addDays(3),
        ]);

        $response = $this->actingAs($user)->post("/event/{$event->id}/book");

        $response->assertRedirect(); // redirects back after booking
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }

    /** @test */
    public function a_user_cannot_book_the_same_event_twice(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'capacity' => 5,
            'event_date' => Carbon::now()->addDays(5),
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($user)->post("/event/{$event->id}/book");
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You have already booked this event.');
    }

    /** @test */
    public function an_authenticated_user_can_view_their_bookings(): void
    {
        $user = User::factory()->create();
        $event1 = Event::factory()->create(['title' => 'Event One']);
        $event2 = Event::factory()->create(['title' => 'Event Two']);

        Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event1->id,
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event2->id,
        ]);

        $response = $this->actingAs($user)->get('/bookings');

        $response->assertStatus(200);
        $response->assertSeeText('Event One');
        $response->assertSeeText('Event Two');
    }

    /** @test */
    public function an_authenticated_user_can_cancel_a_booking(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['title' => 'Cancellable Event']);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($user)->delete("/bookings/{$booking->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }
/** @test */
public function a_user_can_successfully_register_as_an_attendee(): void
{
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'attendee',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
}

/** @test */
public function a_registered_attendee_can_log_in_and_log_out(): void
{
    $user = \App\Models\User::factory()->create(['role' => 'attendee', 'password' => bcrypt('password123')]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);

    $this->post('/logout');
    $this->assertGuest();
}

/** @test */
public function an_attendee_cannot_book_a_full_event(): void
{
    $attendee = \App\Models\User::factory()->create(['role' => 'attendee']);
    $event = \App\Models\Event::factory()->create(['capacity' => 1]);
    \App\Models\Booking::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

    $otherUser = \App\Models\User::factory()->create(['role' => 'attendee']);
    $response = $this->actingAs($otherUser)->post("/event/{$event->id}/book");

    $response->assertSessionHas('error', 'This event is already full.');
}

/** @test */
public function an_attendee_cannot_see_edit_or_delete_buttons_on_any_event_page(): void
{
    $attendee = \App\Models\User::factory()->create(['role' => 'attendee']);
    $event = \App\Models\Event::factory()->create();

    $response = $this->actingAs($attendee)->get("/event/{$event->id}");
    $response->assertDontSee('Edit');
    $response->assertDontSee('Delete');
}

}
