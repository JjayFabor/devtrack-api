<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Models\LearningLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\LearningLogRequest;
use App\Http\Resources\LearningLogResource;

/**
 * @group Learning Log
 *
 * APIs for managing learning logs related to tasks.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN} "Bearer 1|abc123..."
 * @header x-api-key your-api-key-hear
 */
class LearningLogController extends Controller
{
    public function index(Project $project, Task $task)
    {
        return LearningLogResource::collection($task->learningLogs)
            ->additional([
                'success' => true,
                'message' => 'Learning logs retrieved successfully',
            ]);
    }

    public function store(LearningLogRequest $request, Project $project, Task $task)
    {
        $learningLog = $task->learningLogs()->create($request->all());

        return (new LearningLogResource($learningLog))
            ->additional([
                'success' => true,
                'message' => 'Learning log created successfully',
            ]);
    }

    public function show(Project $project, Task $task, LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        return (new LearningLogResource($learningLog))
            ->additional([
                'success' => true,
                'message' => 'Learning log retrieved successfully',
            ]);
    }

    public function update(LearningLogRequest $request, Project $project, Task $task,  LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        $learningLog->update($request->all());

        return (new LearningLogResource($learningLog))
            ->additional([
                'success' => true,
                'message' => 'Learning log updated successfully',
            ]);
    }

    public function destroy(Project $project, Task $task, LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        $learningLog->delete();
        return response()->json([
            'success' => true,
            'message' => 'Learning log deleted successfully',
        ], 204);
    }
}
