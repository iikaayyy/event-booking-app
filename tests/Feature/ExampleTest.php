<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // ⬅️ add this
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // ⬅️ add this

    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
