<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'capacity',
        'organiser_id',
        'category',
    ];

    /**
     * 🔤 Automatically format category names (e.g. 'workshop' → 'Workshop')
     */
    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = ucwords(strtolower(trim($value)));
    }

    /**
     * 👤 Relationship: Event belongs to an organiser (User)
     */
    public function organiser()
    {
        return $this->belongsTo(User::class, 'organiser_id');
    }

    /**
     * 🎟️ Relationship: Event has many bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
