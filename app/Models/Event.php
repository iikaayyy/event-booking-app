<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','event_date','location','capacity','organiser_id',
    ];

    public function organiser()
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
