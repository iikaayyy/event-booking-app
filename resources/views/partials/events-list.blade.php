{{-- resources/views/partials/events-list.blade.php --}}
@if($events->isEmpty())
    <div class="col-12">
        <div class="alert alert-warning text-center shadow-sm">
            No events found for this category.
        </div>
    </div>
@else
    <div class="row">
        @foreach($events as $event)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $event->title }}</h5>
                        <p class="text-muted mb-2">
                            <strong>Category:</strong> {{ $event->category ?? 'Uncategorised' }}
                        </p>
                        @if($event->description)
                            <p class="card-text text-muted">
                                {{ \Illuminate\Support\Str::limit($event->description, 100) }}
                            </p>
                        @endif
                        <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y, g:i a') }}</p>
                        <p class="mb-1"><strong>Location:</strong> {{ $event->location }}</p>
                        <p class="mb-3"><strong>Capacity:</strong> {{ $event->capacity }}</p>

                        <a href="{{ route('event.show', ['id' => $event->id], false) }}"
                           class="btn btn-outline-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- ✅ Pagination --}}
<div class="d-flex justify-content-center mt-4">
    {{ $events->links('pagination::bootstrap-5') }}
</div>
