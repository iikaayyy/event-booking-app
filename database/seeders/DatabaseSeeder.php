<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Run user seeder first
        $this->call(UserSeeder::class);

        // ✅ Then run events (now users exist)
        $this->call(EventSeeder::class);
    }
}
