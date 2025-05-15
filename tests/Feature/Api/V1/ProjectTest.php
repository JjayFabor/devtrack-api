<?php

use App\Models\User;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson, getJson, actingAs, assertDatabaseHas};

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can list all projects', function() {
    Project::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/v1/projects');

    $response->assertOk()
        ->assertJsonCount(3, 'projects')
        ->assertJsonStructure(['projects' => [['id', 'name', 'user_id', 'description', 'tags' => [], 'github_url', 'status', 'created_at', 'updated_at']]]);
});

it('can create a store a project', function() {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $projectData = [
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'tags' => ['test', 'project'],
        'github_url' => 'https://github.com/TestUser/test-repo',
        'status' => 'planning',
    ];

    $response = $this->postJson('/api/v1/projects', $projectData);
    $response->assertCreated()
            ->assertJson([
                'project' => [
                    'name' => 'Test Project',
                    'description' => 'This is a test project.',
                    'tags' => ['test', 'project'],
                    'github_url' => 'https://github.com/TestUser/test-repo',
                    'status' => 'planning',
                ]
            ]);

    $this->assertDatabaseHas('projects', [
        'name' => $projectData['name'],
        'description' => $projectData['description'],
        'tags' => json_encode($projectData['tags']),
        'github_url' => 'https://github.com/TestUser/test-repo',
        'status' => $projectData['status'],
        'user_id' => $user->id,
    ]);
});


it('can show a project', function() {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/v1/projects/' . $project->id);

    $response->assertOk()
        ->assertJson([
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'tags' => $project->tags,
                'github_url' => $project->github_url,
                'status' => $project->status,
            ]
        ]);
});

it('can update a project', function() {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $updateData = [
        'name' => 'Updated Project',
        'description' => 'This is an updated test project.',
        'tags' => ['updated', 'project'],
        'github_url' => 'https://github.com/TestUser/updated-repo',
        'status' => 'completed',
    ];
    $response = $this->putJson('/api/v1/projects/' . $project->id, $updateData);
    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Project updated successfully.',
            'project' => [
                'name' => 'Updated Project',
                'description' => 'This is an updated test project.',
                'tags' => ['updated', 'project'],
                'github_url' => 'https://github.com/TestUser/updated-repo',
                'status' => 'completed',
            ]
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => $updateData['name'],
        'description' => $updateData['description'],
        'tags' => json_encode($updateData['tags']),
        'github_url' => $updateData['github_url'],
        'status' => $updateData['status'],
    ]);
});

it('can delete a project', function() {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson('/api/v1/projects/' . $project->id);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Project deleted successfully.'
        ]);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});
