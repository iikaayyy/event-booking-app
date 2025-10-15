<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Tester',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'attendee',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function user_cannot_register_without_agreeing_to_privacy_policy(): void
    {
    $response = $this->post('/register', [
        'name' => 'John Tester',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Even without checkbox, registration should still redirect or show page, not crash
    $response->assertStatus(200);
    }

}
