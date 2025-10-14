{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">Event Booking</a>

        <div class="d-flex">
            @auth
                @if (auth()->user()->isOrganiser())
                    <a href="{{ route('organiser.dashboard', [], false) }}" class="btn btn-outline-secondary me-2">
                        My Dashboard
                    </a>
                    <a href="{{ route('events.create', [], false) }}" class="btn btn-primary me-2">
                        + Create Event
                    </a>
                @endif

                @if (auth()->user()->isAttendee())
                    <a href="{{ route('bookings.mine', [], false) }}" class="btn btn-outline-secondary me-2">
                        My Bookings
                    </a>
                @endif

                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>


<div class="container py-4">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <h1 class="mb-3 text-center text-primary">🎟️ Upcoming Events</h1>

    {{-- FILTERS CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filtersForm" class="row g-3">
                {{-- Category --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" id="filterCategory" class="form-select">
                        <option value="all">All</option>
                        @foreach(($categories ?? collect()) as $cat)
                            @if($cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" id="filterQ" class="form-control" placeholder="Title or description">
                </div>

                {{-- Location --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" id="filterLocation" class="form-control" placeholder="e.g. Brisbane">
                </div>

                {{-- Sort --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" id="filterSort" class="form-select">
                        <option value="date_asc">Date ↑</option>
                        <option value="date_desc">Date ↓</option>
                        <option value="capacity_asc">Capacity ↑</option>
                        <option value="capacity_desc">Capacity ↓</option>
                    </select>
                </div>

                {{-- Date range --}}
                <div class="col-12 col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" id="filterDateFrom" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" id="filterDateTo" class="form-control">
                </div>

                {{-- Capacity range --}}
                <div class="col-6 col-md-3">
                    <label class="form-label">Min Capacity</label>
                    <input type="number" min="1" name="capacity_min" id="filterCapMin" class="form-control" placeholder="1">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">Max Capacity</label>
                    <input type="number" min="1" name="capacity_max" id="filterCapMax" class="form-control" placeholder="1000">
                </div>

                {{-- Actions --}}
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <button type="button" id="resetFilters" class="btn btn-outline-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EVENTS CONTAINER (AJAX will replace this content) --}}
    <div id="events-container">
        @include('partials.events-list', ['events' => $events])
    </div>
</div>

<script>
(function(){
  const form = document.getElementById('filtersForm');
  const resetBtn = document.getElementById('resetFilters');
  const container = document.getElementById('events-container');
  const endpoint = "{{ route('events.filter', [], false) }}"; // relative path for proxy safety

  // --- Helpers ---
  function formToParams(formEl) {
    const params = new URLSearchParams();
    for (const [k, v] of new FormData(formEl).entries()) {
      if (v !== '') params.append(k, v);
    }
    return params;
  }
  function paramsToForm(params, formEl) {
    params.forEach((v, k) => {
      const el = formEl.querySelector(`[name="${CSS.escape(k)}"]`);
      if (el) el.value = v;
    });
  }
  function setAddressBar(params) {
    const url = new URL(window.location.href);
    url.search = params.toString();
    history.replaceState({}, '', url);
  }

  async function fetchAndRender(params) {
    const url = new URL(endpoint, window.location.origin);
    url.search = params.toString();

    container.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <div class="mt-2 text-muted">Loading…</div>
      </div>`;

    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      const html = await res.text();
      container.innerHTML = html;
    } catch (e) {
      console.error(e);
      container.innerHTML = '<div class="alert alert-danger">Failed to load events. Please try again.</div>';
    }
  }

  // --- Prefill from URL on load, then fetch ---
  const initialParams = new URLSearchParams(window.location.search);
  if (initialParams.toString()) paramsToForm(initialParams, form);
  fetchAndRender(initialParams);

  // --- Apply filters ---
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const params = formToParams(form);
    setAddressBar(params);
    fetchAndRender(params);
  });

  // --- Reset filters ---
  resetBtn.addEventListener('click', () => {
    form.reset();
    const params = new URLSearchParams(); // empty -> show all upcoming
    setAddressBar(params);
    fetchAndRender(params);
  });
})();
</script>

</body>
</html>
