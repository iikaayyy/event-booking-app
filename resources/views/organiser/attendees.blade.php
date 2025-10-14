{{-- resources/views/organiser/attendees.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees – {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home', [], false) }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ route('organiser.dashboard', [], false) }}" class="btn btn-outline-secondary me-2">My Dashboard</a>
            <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="btn btn-outline-primary me-2">View Event</a>
            <form method="POST" action="{{ route('logout', [], false) }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">

    {{-- Page header + CSV download --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-primary mb-0">👥 Attendees for: {{ $event->title }}</h1>

        <a href="{{ route('organiser.attendees.csv', ['id' => $event->id], false) }}"
           class="btn btn-success">
            ⬇️ Download CSV
        </a>
    </div>

    <p class="text-muted mb-4">
        {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y, g:i a') }} • {{ $event->location }}
    </p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- KPIs --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @php
                        $count = $bookings->count();
                        $spotsLeft = max(0, $event->capacity - $count);
                    @endphp
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">Total Bookings</div>
                            <div class="fs-4">{{ $count }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Spots Left</div>
                            <span class="badge bg-{{ $spotsLeft > 0 ? 'success' : 'secondary' }} fs-6">
                                {{ $spotsLeft }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendees table --}}
    @if($bookings->isEmpty())
        <div class="alert alert-info">
            No attendees yet. Share your event to get bookings!
        </div>
    @else
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Booked At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ optional($booking->user)->name ?? '—' }}</td>
                            <td>{{ optional($booking->user)->email ?? '—' }}</td>
                            <td>{{ optional($booking->created_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</body>
</html>
