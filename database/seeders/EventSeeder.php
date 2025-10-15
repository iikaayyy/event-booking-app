<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch organisers dynamically
        $alice = User::where('email', 'alice@eventapp.com')->first();
        $bob   = User::where('email', 'bob@eventapp.com')->first();

        if (!$alice || !$bob) {
            $this->command->warn('Organisers not found. Run UserSeeder first.');
            return;
        }

        // Event by Alice
        Event::create([
            'title' => 'Tech Conference 2025',
            'description' => 'A full-day event with talks and workshops about web development trends.',
            'event_date' => Carbon::now()->addDays(10),
            'location' => 'Brisbane Convention Centre',
            'capacity' => 150,
            'organiser_id' => $alice->id,
        ]);

        // Event by Bob
        Event::create([
            'title' => 'Design Thinking Workshop',
            'description' => 'An interactive workshop focused on innovation through design thinking.',
            'event_date' => Carbon::now()->addDays(15),
            'location' => 'Gold Coast Innovation Hub',
            'capacity' => 100,
            'organiser_id' => $bob->id,
        ]);
    }
}
