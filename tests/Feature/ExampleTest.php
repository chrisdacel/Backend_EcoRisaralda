<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the API health endpoint works.
     */
    public function test_the_api_returns_a_successful_response(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
    }
}
