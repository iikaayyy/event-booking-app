<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary me-2">Events</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h1 class="mb-4">My Bookings</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($bookings->isEmpty())
        <div class="alert alert-info">You haven’t booked any events yet.</div>
    @else
        <div class="list-group">
            @foreach ($bookings as $booking)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $booking->event->title }}</h5>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($booking->event->event_date)->format('F j, Y, g:i a') }}
                            • {{ $booking->event->location }}
                        </small>
                    </div>
                    <form method="POST" action="{{ route('bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">Cancel</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>

</body>
</html>

