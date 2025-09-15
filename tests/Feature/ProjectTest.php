<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_with_permission_can_view_projects()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('projects.view');

        $project = Project::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_without_permission_cannot_view_projects()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/projects');

        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_create_projects()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('projects.edit');

        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description'],
                'message',
            ]);
    }

    public function test_user_without_permission_cannot_create_projects()
    {
        $user = User::factory()->create();

        $projectData = [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/projects', $projectData);

        $response->assertStatus(403);
    }
}
