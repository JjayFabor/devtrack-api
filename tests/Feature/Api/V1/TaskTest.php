<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson, getJson, actingAs, assertDatabaseHas};

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'tags' => json_encode(['test', 'project']),
        'github_url' => 'http://github.com/TestUser/test-repo',
        'status' => 'planning',
    ]);
});

it('can show all tasks', function() {
    Task::factory()->count(3)->create(['project_id' => $this->project->id]);

    $response = $this->getJson('/api/v1/tasks');
    $response->assertOk()
        ->assertJsonCount(3, 'tasks')
        ->assertJsonStructure(['tasks' => [['id', 'title', 'description', 'status', 'created_at', 'updated_at']]]);
});
