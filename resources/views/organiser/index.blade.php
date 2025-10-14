<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events • Organiser Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">All Events</a>
            <a href="{{ route('events.create') }}" class="btn btn-primary me-2">+ Create Event</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">My Events</h2>
        <a href="{{ route('events.create') }}" class="btn btn-primary">+ Create Event</a>
    </div>

    @if($events->isEmpty())
        <div class="alert alert-info">You haven’t created any events yet.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Bookings</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>
                                <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="text-decoration-none">
                                    {{ $event->title }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M j, Y g:i a') }}</td>
                            <td>{{ $event->location }}</td>
                            <td>{{ $event->capacity }}</td>
                            <td>{{ $event->bookings_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                <form action="{{ route('event.delete', $event->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</body>
</html>
