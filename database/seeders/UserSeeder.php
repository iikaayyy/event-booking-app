<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 organisers
        User::create([
            'name' => 'Alice Organizer',
            'email' => 'alice@eventapp.com',
            'password' => Hash::make('password123'),
            'role' => 'organiser',
        ]);

        User::create([
            'name' => 'Bob Organizer',
            'email' => 'bob@eventapp.com',
            'password' => Hash::make('password123'),
            'role' => 'organiser',
        ]);

        // Create one attendee (for testing)
        User::create([
            'name' => 'Charlie Attendee',
            'email' => 'charlie@eventapp.com',
            'password' => Hash::make('password123'),
            'role' => 'attendee',
        ]);
    }
}
