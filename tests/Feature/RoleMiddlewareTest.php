<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_role_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'organizer',
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_superadmin_role_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
