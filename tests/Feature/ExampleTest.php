<?php

namespace Tests\Feature;


use Tests\TestCase;

/**
 * Example feature test to verify the application responds successfully.
 */
class ExampleTest extends TestCase
{
    /**
     * Ensure the home page returns a successful HTTP response.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
