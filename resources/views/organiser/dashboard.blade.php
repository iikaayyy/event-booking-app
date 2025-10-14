<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organiser Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home', [], false) }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ route('events.create', [], false) }}" class="btn btn-primary me-2">+ Create Event</a>
            <form method="POST" action="{{ route('logout', [], false) }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h1 class="text-primary mb-4 text-center">🎯 Organiser Dashboard</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($events->isEmpty())
        <div class="alert alert-info text-center">
            You haven’t created any events yet. Click <strong>+ Create Event</strong> to add your first one.
        </div>
    @else
        <div class="table-responsive shadow-sm bg-white rounded">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Bookings</th>
                        <th>Spots Left</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        @php $spotsLeft = max(0, $event->capacity - $event->bookings_count); @endphp
                        <tr>
                            <td><strong>{{ $event->title }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y, g:i a') }}</td>
                            <td>{{ $event->bookings_count }}</td>
                            <td>
                                <span class="badge bg-{{ $spotsLeft > 0 ? 'success' : 'secondary' }}">
                                    {{ $spotsLeft }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="{{ route('event.edit', ['id' => $event->id], false) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                <a href="{{ route('organiser.attendees', ['id' => $event->id], false) }}" class="btn btn-sm btn-outline-info">Attendees</a>
                                <form method="POST" action="{{ route('event.delete', ['id' => $event->id], false) }}" class="d-inline" onsubmit="return confirm('Delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
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
