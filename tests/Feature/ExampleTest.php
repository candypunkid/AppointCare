<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The landing page renders for an active tenant (the app is multi-tenant,
     * so / resolves a tenant from the request host before rendering).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Tenant::create([
            'name' => 'AppointCare',
            'slug' => 'appointcare',
            'domain' => 'appointcare.local',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
