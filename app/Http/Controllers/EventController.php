<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * 🏠 Home page – list upcoming events.
     */
    public function index()
    {
        $events = Event::where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->paginate(8);

        // Distinct categories for filters
        $categories = Event::whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('home', compact('events', 'categories'));
    }

    /**
     * 📅 Show a single event.
     */
    public function show($id)
    {
        $event = Event::with('organiser')
            ->withCount('bookings')
            ->findOrFail($id);

        return view('event.show', compact('event'));
    }

    /**
     * 🔎 AJAX: Filter events (category + advanced filters) with pagination.
     */
    public function filter(Request $request)
    {
        $validated = $request->validate([
            'category'      => 'nullable|string|max:100',
            'q'             => 'nullable|string|max:100',
            'location'      => 'nullable|string|max:150',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date|after_or_equal:date_from',
            'capacity_min'  => 'nullable|integer|min:1',
            'capacity_max'  => 'nullable|integer|min:1',
            'sort'          => 'nullable|string|in:date_asc,date_desc,capacity_asc,capacity_desc',
            'page'          => 'nullable|integer|min:1',
        ]);

        $query = Event::query()->where('event_date', '>', now());

        // Category
        if (!empty($validated['category']) && $validated['category'] !== 'all') {
            $query->where('category', $validated['category']);
        }

        // Free-text search
        if (!empty($validated['q'])) {
            $q = trim($validated['q']);
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Location
        if (!empty($validated['location'])) {
            $loc = trim($validated['location']);
            $query->where('location', 'like', "%{$loc}%");
        }

        // Date range
        if (!empty($validated['date_from'])) {
            $query->where('event_date', '>=', $validated['date_from']);
        }
        if (!empty($validated['date_to'])) {
            $query->where('event_date', '<=', $validated['date_to']);
        }

        // Capacity range
        if (!empty($validated['capacity_min'])) {
            $query->where('capacity', '>=', (int) $validated['capacity_min']);
        }
        if (!empty($validated['capacity_max'])) {
            $query->where('capacity', '<=', (int) $validated['capacity_max']);
        }

        // Sorting
        switch ($validated['sort'] ?? 'date_asc') {
            case 'date_desc':
                $query->orderBy('event_date', 'desc');
                break;
            case 'capacity_asc':
                $query->orderBy('capacity', 'asc')->orderBy('event_date', 'asc');
                break;
            case 'capacity_desc':
                $query->orderBy('capacity', 'desc')->orderBy('event_date', 'asc');
                break;
            default:
                $query->orderBy('event_date', 'asc');
                break;
        }

        // ✅ Paginate for AJAX (6 per page)
        $events = $query->paginate(6);

        // Return partial view for AJAX
        return view('partials.events-list', compact('events'));
    }

    /**
     * 🎟️ Book an event (attendee only).
     */
    public function book($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $event = Event::findOrFail($id);

        if ($event->event_date <= now()) {
            return back()->with('error', 'You cannot book a past event.');
        }

        if (method_exists($user, 'isOrganiser') && $user->isOrganiser()) {
            return back()->with('error', 'Organisers cannot book events.');
        }

        if (Booking::where('user_id', $user->id)->where('event_id', $event->id)->exists()) {
            return back()->with('error', 'You have already booked this event.');
        }

        if ($event->bookings()->count() >= $event->capacity) {
            return back()->with('error', 'This event is already full.');
        }

        Booking::create([
            'user_id'  => $user->id,
            'event_id' => $event->id,
        ]);

        return back()->with('success', 'You have successfully booked this event!');
    }

    /**
     * ✏️ Edit event (organiser only & owner).
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
     * 📝 Update event.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to update this event.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            'event_date'  => 'required|date|after:now',
            'location'    => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1|max:1000',
        ]);

        $event->update($validated);
        return redirect()->route('event.show', $event->id)
            ->with('success', 'Event updated successfully.');
    }

    /**
     * ❌ Delete event (only if no bookings).
     */
    public function delete($id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to delete this event.');
        }

        if ($event->bookings()->count() > 0) {
            return redirect()->route('event.show', $id)
                ->with('error', 'You cannot delete an event with active bookings.');
        }

        $event->delete();
        return redirect()->route('home')->with('success', 'Event deleted successfully.');
    }

    /**
     * 📖 Attendee: view my bookings.
     */
    public function myBookings()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if (method_exists($user, 'isOrganiser') && $user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Organisers cannot view attendee bookings.');
        }

        $bookings = Booking::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * 🚫 Cancel a booking (attendee only & owner).
     */
    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'You are not allowed to cancel this booking.');
        }

        $booking->delete();
        return redirect()->route('bookings.mine')
            ->with('success', 'Your booking has been cancelled.');
    }

    /**
     * ➕ Create event form (organiser only).
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'isOrganiser') || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can create events.');
        }

        return view('event.create');
    }

    /**
     * 💾 Store new event.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'isOrganiser') || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can create events.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'category'    => 'required|string|max:100',
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
     * 📊 Organiser Dashboard.
     */
    public function organiserDashboard()
    {
        $user = Auth::user();
        if (!$user || !method_exists($user, 'isOrganiser') || !$user->isOrganiser()) {
            return redirect()->route('home')->with('error', 'Only organisers can access this page.');
        }

        $events = Event::where('organiser_id', $user->id)
            ->withCount('bookings')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('organiser.dashboard', compact('events'));
    }

    /**
     * 👥 View attendees (organiser only).
     */
    public function attendees($id)
    {
        $event = Event::with(['bookings.user'])->findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to view attendees for this event.');
        }

        // Provide $bookings for the Blade view
        $bookings = $event->bookings()->with('user')->orderBy('created_at', 'asc')->get();

        return view('organiser.attendees', compact('event', 'bookings'));
    }

    /**
     * 📤 Export attendees as CSV (organiser only).
     */
    public function attendeesCsv($id)
    {
        $event = Event::with(['bookings.user'])->findOrFail($id);

        if (Auth::id() !== $event->organiser_id) {
            return redirect()->route('home')->with('error', 'You are not authorised to export attendees for this event.');
        }

        $filename = 'attendees_event_' . $event->id . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($event) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Booked At']);

            foreach ($event->bookings as $booking) {
                fputcsv($handle, [
                    optional($booking->user)->name,
                    optional($booking->user)->email,
                    optional($booking->created_at)->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
