<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Show the bookings for the logged-in user
    public function index()
    {
        $bookings = Booking::with('event')
                    ->where('user_id', Auth::id())
                    ->get();

        return view('bookings.index', compact('bookings'));
    }
}
