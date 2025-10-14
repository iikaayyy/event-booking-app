<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Event Booking App — main routes for attendees and organisers.
| Includes authentication, filtering, CRUD, dashboards, and CSV export.
|--------------------------------------------------------------------------
*/

// 🏠 Public Routes (accessible to all)
Route::get('/', [EventController::class, 'index'])->name('home');

// 📅 View specific event details
Route::get('/event/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('event.show');

// 🔎 AJAX: Filter events (category, search, etc.)
Route::get('/events/filter', [EventController::class, 'filter'])
    ->name('events.filter');


// 🔐 Authenticated User Routes
Route::middleware('auth')->group(function () {

    /* ======================================================
     * 🎟️ ATTENDEE ROUTES
     * ====================================================== */

    // Book an event
    Route::post('/event/{id}/book', [EventController::class, 'book'])
        ->whereNumber('id')
        ->name('book.event');

    // View my bookings
    Route::get('/bookings', [EventController::class, 'myBookings'])
        ->name('bookings.mine');

    // Cancel a booking
    Route::delete('/bookings/{booking}', [EventController::class, 'cancelBooking'])
        ->whereNumber('booking')
        ->name('bookings.cancel');


    /* ======================================================
     * 🧑‍💼 ORGANISER ROUTES
     * ====================================================== */

    // Create + store event
    Route::get('/events/create', [EventController::class, 'create'])
        ->name('events.create');
    Route::post('/events', [EventController::class, 'store'])
        ->name('events.store');

    // Edit, update, delete event
    Route::get('/event/{id}/edit', [EventController::class, 'edit'])
        ->whereNumber('id')
        ->name('event.edit');
    Route::patch('/event/{id}/update', [EventController::class, 'update'])
        ->whereNumber('id')
        ->name('event.update');
    Route::delete('/event/{id}/delete', [EventController::class, 'delete'])
        ->whereNumber('id')
        ->name('event.delete');

    // Dashboard
    Route::get('/organiser/dashboard', [EventController::class, 'organiserDashboard'])
        ->name('organiser.dashboard');
    Route::get('/organiser/events', [EventController::class, 'organiserDashboard'])
        ->name('organiser.events');

    // Attendees list + CSV export
    Route::get('/organiser/events/{id}/attendees', [EventController::class, 'attendees'])
        ->whereNumber('id')
        ->name('organiser.attendees');
    Route::get('/organiser/events/{id}/attendees.csv', [EventController::class, 'attendeesCsv'])
        ->whereNumber('id')
        ->name('organiser.attendees.csv');
});


// 👤 Profile Management (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// 🚪 Dashboard Redirect (default)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');


// 🔐 Authentication (Login, Register, Forgot Password)
require __DIR__ . '/auth.php';
