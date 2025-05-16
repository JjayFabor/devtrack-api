<?php

use App\Models\Task;

use App\Models\User;
use App\Models\Project;
use App\Models\LearningLog;
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


it('can show all learning logs', function() {
    LearningLog::factory()->count(3)->create(['task_id' => $this->task->id]);

    $response = $this->getJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/learning-logs');
    $response->assertOk()
        ->assertJsonCount(3, 'learning_logs')
        ->assertJsonStructure(['learning_logs' => [['id', 'title', 'topic', 'summary', 'duration', 'resources', 'created_at', 'updated_at']]]);
});

it('can create a learning log on a specific task', function() {
    $learningLogData = [
        'title' => 'Test Learning Log',
        'topic' => 'Learning Topic',
        'summary' => 'This is a test learning log.',
        'duration' => 2,
        'resources' => [
            'https://example.com/resource1',
            'https://example.com/resource2',
            'https://example.com/resource3',
        ],
    ];

    $response = $this->postJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/learning-logs', $learningLogData);
    $response->assertCreated()
        ->assertJson([
            'learning_log' => [
                'title' => 'Test Learning Log',
                'topic' => 'Learning Topic',
                'summary' => 'This is a test learning log.',
                'duration' => 2,
                'resources' => [
                    'https://example.com/resource1',
                    'https://example.com/resource2',
                    'https://example.com/resource3',
                ],
                'task_id' => $this->task->id,
            ]
        ]);

    $this->assertDatabaseHas('learning_logs', [
        'title' => $learningLogData['title'],
        'topic' => $learningLogData['topic'],
        'summary' => $learningLogData['summary'],
        'duration' => $learningLogData['duration'],
        'resources' => json_encode($learningLogData['resources']),
        'task_id' => $this->task->id,
    ]);
});

it('can show a learning log on a specific task', function() {
    $learningLog = LearningLog::factory()->create(['task_id' => $this->task->id]);

    $response = $this->getJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/learning-logs/' . $learningLog->id);

    $response->assertOk()
        ->assertJson([
            'learning_log' => [
                'id' => $learningLog->id,
                'title' => $learningLog->title,
                'topic' => $learningLog->topic,
                'summary' => $learningLog->summary,
                'duration' => $learningLog->duration,
                'resources' => $learningLog->resources,
                'task_id' => $this->task->id,
            ]
        ]);
});

it('can update a learning log on a specific task', function() {
    $learningLog = LearningLog::factory()->create(['task_id' => $this->task->id]);

    $updateData = [
        'title' => 'Updated Learning Log',
        'topic' => 'Updated Topic',
        'summary' => 'This is an updated test learning log.',
        'duration' => 3,
        'resources' => [
            'https://example.com/updated-resource1',
            'https://example.com/updated-resource2',
            'https://example.com/updated-resource3',
        ],
    ];

    $response = $this->putJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/learning-logs/' . $learningLog->id, $updateData);
    $response->assertOk()
        ->assertJson([
            'learning_log' => [
                'title' => 'Updated Learning Log',
                'topic' => 'Updated Topic',
                'summary' => 'This is an updated test learning log.',
                'duration' => 3,
                'resources' => [
                    'https://example.com/updated-resource1',
                    'https://example.com/updated-resource2',
                    'https://example.com/updated-resource3',
                ],
                'task_id' => $this->task->id,
            ]
        ]);

    $this->assertDatabaseHas('learning_logs', [
        'id' => $learningLog->id,
        'title' => $updateData['title'],
        'topic' => $updateData['topic'],
        'summary' => $updateData['summary'],
        'duration' => $updateData['duration'],
        'resources' => json_encode($updateData['resources']),
    ]);
});

it('can delete a learning log on a specific task', function() {
    $learningLog = LearningLog::factory()->create(['task_id' => $this->task->id]);

    $response = $this->deleteJson('/api/v1/projects/' . $this->project->id . '/tasks/' . $this->task->id . '/learning-logs/' . $learningLog->id);
    $response->assertNoContent();

    $this->assertDatabaseMissing('learning_logs', [
        'id' => $learningLog->id,
        'task_id' => $this->task->id,
    ]);
});
