<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home', [], false) }}">Event Booking</a>
        <div class="d-flex">
            <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="btn btn-outline-secondary me-2">Back</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h1 class="mb-4">Edit Event</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-bold mb-2">Please fix the following:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('event.update', ['id' => $event->id], false) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label">Event Title</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $event->title) }}" required maxlength="100">
        </div>
        
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" id="category" name="category" 
            class="form-control" 
            value="{{ old('category', $event->category ?? '') }}" 
            placeholder="e.g. Workshop, Conference, Meetup" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Date</label>
            <input type="datetime-local" name="event_date" class="form-control"
                   value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control"
                   value="{{ old('location', $event->location) }}" required maxlength="255">
        </div>

        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control"
                   value="{{ old('capacity', $event->capacity) }}" min="1" max="1000" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('event.show', ['id' => $event->id], false) }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

</body>
</html>
