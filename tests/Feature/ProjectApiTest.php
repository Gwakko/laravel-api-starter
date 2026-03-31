<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_can_list_projects(): void
    {
        $this->user->projects()->createMany([
            ['name' => 'Project 1'],
            ['name' => 'Project 2'],
            ['name' => 'Project 3'],
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/projects');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_can_create_project(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/projects', [
                'name' => 'New Project',
                'description' => 'A test project',
            ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'New Project']);
        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_show_project_with_tasks(): void
    {
        $project = $this->user->projects()->create(['name' => 'My Project']);
        Task::forceCreate([
            'title' => 'Task 1',
            'project_id' => $project->id,
            'user_id' => $this->user->id,
        ]);
        Task::forceCreate([
            'title' => 'Task 2',
            'project_id' => $project->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'My Project']);
        $response->assertJsonCount(2, 'tasks');
    }

    public function test_can_update_project(): void
    {
        $project = $this->user->projects()->create(['name' => 'Old Name']);

        $response = $this->withToken($this->token)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'New Name',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'New Name']);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_project(): void
    {
        $project = $this->user->projects()->create(['name' => 'Delete Me']);

        $response = $this->withToken($this->token)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_create_project_requires_name(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/projects', [
                'description' => 'No name provided',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }
}
