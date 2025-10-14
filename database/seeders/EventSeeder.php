<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Event by Alice Organizer
        Event::create([
            'title' => 'Tech Conference 2025',
            'description' => 'A full-day event with talks and workshops about web development trends.',
            'event_date' => Carbon::now()->addDays(10),
            'location' => 'Brisbane Convention Centre',
            'capacity' => 150,
            'organiser_id' => 1, // Alice
        ]);

        // Event by Bob Organizer
        Event::create([
            'title' => 'Design Thinking Workshop',
            'description' => 'An interactive workshop focused on innovation through design thinking.',
            'event_date' => Carbon::now()->addDays(15),
            'location' => 'Gold Coast Innovation Hub',
            'capacity' => 100,
            'organiser_id' => 2, // Bob
        ]);
    }
}
