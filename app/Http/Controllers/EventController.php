<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * List upcoming events (home page).
     */
    public function index()
    {
        $events = Event::where('event_date', '>', now())
            ->orderBy('event_date')
            ->paginate(8);

        return view('home', compact('events'));
    }

    /**
     * Show a single event.
     */
    public function show($id)
    {
        $event = Event::with('organiser')
            ->withCount('bookings')
            ->findOrFail($id);

        return view('event.show', compact('event'));
    }

    /**
     * Book an event (attendees only).
     */
    public function book($id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $event = Event::findOrFail($id);

        // Organisers cannot book
        if ($user->isOrganiser()) {
            return back()->with('error', 'Organisers cannot book events.');
        }

        // Prevent double booking
        $already = Booking::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($already) {
            return back()->with('error', 'You have already booked this event.');
        }

        // Capacity check
        $current = Booking::where('event_id', $event->id)->count();
        if ($current >= $event->capacity) {
            return back()->with('error', 'This event is already full.');
        }

        Booking::create([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        return back()->with('success', 'You have successfully booked this event!');
    }

    /**
     * Show edit form (organiser only, must own event).
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to edit this event.');
        }

        return view('event.edit', compact('event'));
    }

    /**
     * Update event (organiser only, must own event).
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to update this event.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string',
            'event_date'  => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1|max:1000',
        ]);

        $event->update($validated);

        return redirect()
            ->route('event.show', $event->id)
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Delete event (organiser only, must own event, no active bookings).
     */
    public function delete($id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to delete this event.');
        }

        if ($event->bookings()->count() > 0) {
            return redirect()
                ->route('event.show', $id)
                ->with('error', 'You cannot delete an event that has active bookings.');
        }

        $event->delete();

        return redirect()->route('home')->with('success', 'Event deleted successfully.');
    }

    /**
     * Attendee: see my bookings.
     */
    public function myBookings()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Organisers cannot view attendee bookings.');
        }

        $bookings = Booking::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Attendee: cancel my booking.
     */
    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'You are not allowed to cancel this booking.');
        }

        $booking->delete();

        return redirect()
            ->route('bookings.mine')
            ->with('success', 'Your booking has been cancelled.');
    }

    /**
     * Organiser: show event creation form.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can create events.');
        }

        return view('event.create');
    }

    /**
     * Organiser: store newly created event.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can create events.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string',
            'event_date'  => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1|max:1000',
        ]);

        $validated['organiser_id'] = $user->id;

        Event::create($validated);

        return redirect()->route('home')->with('success', 'Event created successfully!');
    }

    /**
     * Organiser dashboard: list my events + booking counts.
     */
    public function organiserDashboard()
    {
        $user = Auth::user();
        if (!$user || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can access this page.');
        }

        $events = Event::where('organiser_id', $user->id)
            ->withCount('bookings')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('organiser.dashboard', compact('events'));
    }
}
