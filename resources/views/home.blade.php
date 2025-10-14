<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">Event Booking</a>

        <div class="d-flex align-items-center">
            @auth
                {{-- Organiser navigation --}}
                @if (auth()->user()->isOrganiser())
                    <a href="{{ route('organiser.dashboard', [], false) }}" class="btn btn-outline-secondary me-2">
                        My Dashboard
                    </a>
                    <a href="{{ route('events.create', [], false) }}" class="btn btn-primary me-2">
                        + Create Event
                    </a>
                @endif

                {{-- Attendee navigation --}}
                @if (auth()->user()->isAttendee())
                    <a href="{{ route('bookings.mine', [], false) }}" class="btn btn-outline-secondary me-2">
                        My Bookings
                    </a>
                @endif

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            @else
                {{-- Guest view --}}
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container py-5">
    <h1 class="mb-4 text-center text-primary">🎟️ Upcoming Events</h1>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    {{-- Events List --}}
    @if($events->isEmpty())
        <div class="alert alert-warning text-center">
            No upcoming events found.
        </div>
    @else
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $event->title }}</h5>
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit($event->description, 100) }}
                            </p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y, g:i a') }}</p>
                            <p><strong>Location:</strong> {{ $event->location }}</p>
                            <p><strong>Capacity:</strong> {{ $event->capacity }}</p>

                            <a href="{{ route('event.show', ['id' => $event->id], false) }}" 
                               class="btn btn-outline-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $events->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

</body>
</html>
