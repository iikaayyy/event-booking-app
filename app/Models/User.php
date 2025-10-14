<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- important
    ];

    /**
     * Hidden for arrays / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ------------------------------
    // Role helpers used by Blade
    // ------------------------------
    public function isOrganiser(): bool
    {
        return $this->role === 'organiser';
    }

    public function isAttendee(): bool
    {
        return $this->role === 'attendee';
    }
}
