<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Main application routes for Event Booking App.
| Includes attendee and organiser functionality with authentication.
|--------------------------------------------------------------------------
*/

// 🏠 Home page – lists upcoming events
Route::get('/', [EventController::class, 'index'])->name('home');

// 📅 View event details
Route::get('/event/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('event.show');

// 🔐 Auth-required routes
Route::middleware('auth')->group(function () {

    // 🧾 Attendee bookings
    Route::post('/event/{id}/book', [EventController::class, 'book'])
        ->whereNumber('id')
        ->name('book.event');

    Route::get('/bookings', [EventController::class, 'myBookings'])
        ->name('bookings.mine');

    Route::delete('/bookings/{booking}', [EventController::class, 'cancelBooking'])
        ->whereNumber('booking')
        ->name('bookings.cancel');

    // 🧑‍💼 Organiser: create & store event
    Route::get('/events/create', [EventController::class, 'create'])
        ->name('events.create');

    Route::post('/events', [EventController::class, 'store'])
        ->name('events.store');

    // 🧑‍💼 Organiser: edit, update, delete
    Route::get('/event/{id}/edit', [EventController::class, 'edit'])
        ->whereNumber('id')
        ->name('event.edit');

    Route::patch('/event/{id}/update', [EventController::class, 'update'])
        ->whereNumber('id')
        ->name('event.update');

    Route::delete('/event/{id}/delete', [EventController::class, 'delete'])
        ->whereNumber('id')
        ->name('event.delete');

    // 📊 Organiser dashboard (primary, used by UI)
    Route::get('/organiser/dashboard', [EventController::class, 'organiserDashboard'])
        ->name('organiser.dashboard');

    // 📊 Legacy/alias path for dashboard (keeps old links working)
    Route::get('/organiser/events', [EventController::class, 'organiserDashboard'])
        ->name('organiser.events');
});

// 👤 Profile management (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🚪 Dashboard redirect (Breeze default -> home)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔐 Auth routes (login/register/password)
require __DIR__.'/auth.php';
