<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Models\LearningLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\LearningLogRequest;

class LearningLogController extends Controller
{
    public function index(Project $project, Task $task)
    {
        return response()->json([
            'success' => true,
            'message' => 'Learning logs retrieved successfully',
            'learning_logs' => $task->learningLogs()->with('project')->get(),
        ]);
    }

    public function store(LearningLogRequest $request, Project $project, Task $task)
    {
        $learningLog = $task->learningLogs()->create($request->all());

        $responseData = $learningLog->toArray();
        $responseData['resources'] = $learningLog->resources;

        return response()->json([
            'success' => true,
            'message' => 'Learning log created successfully',
            'learning_log' => $responseData,
        ], 201);
    }

    public function show(Project $project, Task $task, LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Learning log retrieved successfully',
            'learning_log' => $learningLog,
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
        return response()->json([
            'success' => true,
            'message' => 'Learning log updated successfully',
            'learning_log' => $learningLog,
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
