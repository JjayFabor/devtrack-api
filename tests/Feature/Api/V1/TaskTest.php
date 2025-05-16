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

it('can create a task on a specific project', function() {
    $taskData = [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => 'todo',
        'priority' => 'high',
        'deadline' => now()->addDays(7)->toDateString(),
        'is_recurring' => false,
    ];

    $response = $this->postJson('/api/v1/projects/' . $this->project->id . '/tasks', $taskData);
    $response->assertCreated()
        ->assertJson([
            'task' => [
                'title' => 'Test Task',
                'description' => 'This is a test task.',
                'status' => 'todo',
                'priority' => 'high',
                'deadline' => now()->addDays(7)->toDateString(),
                'is_recurring' => false,
                'project_id' => $this->project->id,
            ]
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => $taskData['title'],
        'description' => $taskData['description'],
        'status' => $taskData['status'],
        'priority' => $taskData['priority'],
        'deadline' => $taskData['deadline'],
        'is_recurring' => $taskData['is_recurring'],
        'project_id' => $this->project->id,
    ]);
});

it('can show all task on a specific project', function () {
    $tasks = Task::factory()->count(3)->create(['project_id' => $this->project->id]);

    $response = $this->getJson('/api/v1/projects/'. $this->project->id . '/tasks');

     $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
        ]);

        foreach ($tasks as $task) {
        $response->assertJsonFragment([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'deadline' => $task->deadline ? $task->deadline->format('Y-m-d H:i:s') : null,
            'is_recurring' => $task->is_recurring,
            'project_id' => $this->project->id,
        ]);
    }

    $this->assertDatabaseHas(
         'tasks', [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'deadline' => $task->deadline,
            'is_recurring' => $task->is_recurring,
            'project_id' => $this->project->id,
        ]
    );
});

it('can update a specific task', function() {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $updateData = [
        'title' => 'Updated Task',
        'description' => 'This is an updated test task.',
        'status' => 'in_progress',
        'priority' => 'medium',
        'deadline' => now()->addDays(3)->toDateString(),
        'is_recurring' => true,
    ];

    $response = $this->putJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $task->id, $updateData);
    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Task updated successfully.',
            'task' => [
                'title' => 'Updated Task',
                'description' => 'This is an updated test task.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'deadline' => now()->addDays(3)->toDateString(),
                'is_recurring' => true,
            ]
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => $updateData['title'],
        'description' => $updateData['description'],
        'status' => $updateData['status'],
        'priority' => $updateData['priority'],
        'deadline' => $updateData['deadline'],
        'is_recurring' => $updateData['is_recurring'],
    ]);
});

it('can delete a specific task', function() {
    $task = Task::factory()->create(['project_id' => $this->project->id]);

    $response = $this->deleteJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $task->id);
    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});
