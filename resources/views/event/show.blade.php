<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ route('home', [], false) }}" class="btn btn-outline-secondary me-2">All Events</a>
            @auth
                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<div class="container pb-5">
    <!-- Messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Event Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-primary">{{ $event->title }}</h2>
            <p class="text-muted">{{ $event->description }}</p>

            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y, g:i a') }}</p>
            <p><strong>Location:</strong> {{ $event->location }}</p>
            <p><strong>Organiser:</strong> {{ optional($event->organiser)->name ?? '—' }}</p>

            @php
                $booked = $event->bookings_count ?? $event->bookings()->count();
                $spotsLeft = max(0, $event->capacity - $booked);
            @endphp

            <p><strong>Capacity:</strong> {{ $event->capacity }}</p>
            <p><strong>Spots Left:</strong>
                <span class="badge bg-{{ $spotsLeft ? 'success' : 'secondary' }}">
                    {{ $spotsLeft }}
                </span>
            </p>

            <!-- User Actions -->
            @auth
                @php
                    $user = auth()->user();
                    $alreadyBooked = $event->bookings()->where('user_id', $user->id)->exists();
                @endphp

                @if($user->isAttendee())
                    @if($alreadyBooked)
                        <div class="alert alert-info">You’ve already booked this event.</div>
                    @elseif($spotsLeft <= 0)
                        <div class="alert alert-secondary">This event is full.</div>
                    @else
                        <form method="POST" action="{{ route('book.event', ['id' => $event->id], false) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Book this event</button>
                        </form>
                    @endif
                @elseif($user->isOrganiser() && $user->id === $event->organiser_id)
                    <!-- Organiser Controls -->
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('event.edit', ['id' => $event->id], false) }}" class="btn btn-outline-warning">Edit</a>

                        <form method="POST" action="{{ route('event.delete', ['id' => $event->id], false) }}"
                              onsubmit="return confirm('Are you sure you want to delete this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning">Organisers cannot book events.</div>
                @endif
            @else
                <div class="alert alert-light border">
                    Please <a href="{{ route('login', [], false) }}">log in</a> to book this event.
                </div>
            @endauth
        </div>
    </div>
    @if(isset($relatedEvents) && $relatedEvents->isNotEmpty())
    <hr class="my-5">
    <h3 class="text-primary mb-3">🎯 Related Events in {{ $event->category }}</h3>

    <div class="row">
        @foreach($relatedEvents as $related)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $related->title }}</h5>
                        <p class="text-muted mb-2">
                            {{ \Carbon\Carbon::parse($related->event_date)->format('F j, Y, g:i a') }}
                        </p>
                        <p class="text-muted mb-3">
                            📍 {{ $related->location }}
                        </p>
                        <a href="{{ route('event.show', ['id' => $related->id], false) }}"
                           class="btn btn-outline-primary w-100">
                            View Event
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

</div>

</body>
</html>
