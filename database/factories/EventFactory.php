<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_date' => Carbon::now()->addDays(rand(1, 30)),
            'location' => $this->faker->city(),
            'capacity' => $this->faker->numberBetween(10, 50),
            'category' => $this->faker->randomElement(['Workshop', 'Seminar', 'Conference', 'Hackathon']),
            'organiser_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
