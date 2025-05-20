<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Models\LearningLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function store(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'summary' => 'required|string',
            'duration' => 'required|integer',
            'resources' => 'nullable|array',
        ]);

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

    public function update(Request $request, Project $project, Task $task,  LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'topic' => 'sometimes|required|string|max:255',
            'summary' => 'sometimes|required|string',
            'duration' => 'sometimes|required|integer',
            'resources' => 'sometimes|nullable|array',
        ]);

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
