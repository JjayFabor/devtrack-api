<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Error;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson, getJson, actingAs, assertDatabaseHas};

uses(RefreshDatabase::class);

beforeEach(function(){
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project',
        'description' => 'This is a test project.',
        'tags' => json_encode(['test', 'project']),
        'github_url' => 'https://github.com/TestUser/test-repo',
        'status' => 'planning',
    ]);

    $this->task = Task::factory()->create([
        'project_id' => $this->project->id,
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => 'todo',
        'priority' => 'high',
        'deadline' => now()->addDays(7)->toDateString(),
        'is_recurring' => false,
    ]);
});

it('can show all error data encounter', function(){
    Error::factory()->count(3)->create(['task_id' => $this->task->id]);

    $response = $this->getJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/errors');
    $response->assertOk()
        ->assertJsonCount(3, 'error_data')
        ->assertJsonStructure(['error_data' => [['id', 'title', 'description', 'code_snippet', 'cause', 'resolution', 'severity', 'status', 'created_at', 'updated_at']]]);
});

it('can create an error data on a specific task', function(){
    $errorData = [
        'title' => 'Test Error',
        'description' => 'This is a test error.',
        'code_snippet' => 'console.log("Hello World")',
        'cause' => 'Syntax Error',
        'resolution' => 'Fix the syntax',
        'severity' => 'high',
        'status' => 'unresolved',
    ];

    $response = $this->postJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/errors', $errorData);
    $response->assertCreated()
        ->assertJson([
            'error_data' => [
                'title' => 'Test Error',
                'description' => 'This is a test error.',
                'code_snippet' => 'console.log("Hello World")',
                'cause' => 'Syntax Error',
                'resolution' => 'Fix the syntax',
                'severity' => 'high',
                'status' => 'unresolved',
                'task_id' => $this->task->id,
            ]
        ]);

    $this->assertDatabaseHas('errors', [
        'title' => $errorData['title'],
        'description' => $errorData['description'],
        'code_snippet' => $errorData['code_snippet'],
        'cause' => $errorData['cause'],
        'resolution' => $errorData['resolution'],
        'severity' => $errorData['severity'],
        'status' => $errorData['status'],
        'task_id' => $this->task->id,
    ]);
});

it('can show a specific error data on a specific task', function(){
    $error = Error::factory()->create(['task_id' => $this->task->id]);

    $response = $this->getJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/errors/' . $error->id);
    $response->assertOk()
        ->assertJson([
            'error_data' => [
                'id' => $error->id,
                'title' => $error->title,
                'description' => $error->description,
                'code_snippet' => $error->code_snippet,
                'cause' => $error->cause,
                'resolution' => $error->resolution,
                'severity' => $error->severity,
                'status' => $error->status,
            ]
        ]);
});

it('can update a specific error data on a specific task', function(){
    $error = Error::factory()->create(['task_id' => $this->task->id]);

    $updateData = [
        'title' => 'Updated Error',
        'description' => 'This is an updated test error.',
        'code_snippet' => 'console.log("Hello World")',
        'cause' => 'Syntax Error',
        'resolution' => 'Fix the syntax',
        'severity' => 'medium',
        'status' => 'resolved',
    ];

    $response = $this->putJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/errors/' . $error->id, $updateData);
    $response->assertOk()
        ->assertJson([
            'error_data' => [
                'title' => 'Updated Error',
                'description' => 'This is an updated test error.',
                'code_snippet' => 'console.log("Hello World")',
                'cause' => 'Syntax Error',
                'resolution' => 'Fix the syntax',
                'severity' => 'medium',
                'status' => 'resolved',
            ]
        ]);

    $this->assertDatabaseHas('errors', [
        'title' => $updateData['title'],
        'description' => $updateData['description'],
        'code_snippet' => $updateData['code_snippet'],
        'cause' => $updateData['cause'],
        'resolution' => $updateData['resolution'],
        'severity' => $updateData['severity'],
        'status' => $updateData['status'],
    ]);
});

it('can delete a specific error data on a specific task', function(){
    $error = Error::factory()->create(['task_id' => $this->task->id]);

    $response = $this->deleteJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/errors/' . $error->id);
    $response->assertNoContent();

    $this->assertDatabaseMissing('errors', [
        'id' => $error->id,
        'task_id' => $this->task->id,
    ]);
});
