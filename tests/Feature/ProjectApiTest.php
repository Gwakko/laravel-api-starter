<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectApiTest extends TestCase
{
    public function test_health_check(): void
    {
        $response = $this->getJson('/');
        $response->assertStatus(200);
    }
}
