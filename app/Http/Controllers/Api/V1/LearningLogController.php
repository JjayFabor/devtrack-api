<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Models\LearningLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\LearningLogRequest;
use App\Http\Resources\LearningLogResource;

/**
 * @group Learning Logs
 *
 * APIs for managing learning logs related to tasks.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN}
 * @header x-api-key {YOUR_API_KEY}
 */
class LearningLogController extends Controller
{
    /**
     * List learning logs for a task
     *
     * Retrieve all learning logs associated with a specific task in a project.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Learning logs retrieved successfully",
     *   "data": [...]
     * }
     */
    public function index(Project $project, Task $task)
    {
        return LearningLogResource::collection($task->learningLogs)
            ->additional([
                'success' => true,
                'message' => 'Learning logs retrieved successfully',
            ]);
    }

    /**
     * Create a new learning log for a task
     *
     * Store a new learning log entry for a specific task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @bodyParam description string required The learning log description. Example: "Read Laravel docs"
     * @bodyParam duration int The time spent in minutes. Example: 60
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Learning log created successfully",
     *   "data": {...}
     * }
     */
    public function store(LearningLogRequest $request, Project $project, Task $task)
    {
        $learningLog = $task->learningLogs()->create($request->all());

        return (new LearningLogResource($learningLog))
            ->additional([
                'success' => true,
                'message' => 'Learning log created successfully',
            ]);
    }

    /**
     * Show a specific learning log
     *
     * Retrieve details of a specific learning log for a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam learning_log int required The ID of the learning log.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Learning log retrieved successfully",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Learning log not found for this task."
     * }
     */
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

    /**
     * Update a learning log
     *
     * Update the details of a specific learning log for a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam learning_log int required The ID of the learning log.
     * @bodyParam description string The updated description. Example: "Watched Laravel video"
     * @bodyParam duration int The updated duration in minutes. Example: 90
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Learning log updated successfully",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Learning log not found for this task."
     * }
     */
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

    /**
     * Delete a learning log
     *
     * Delete a specific learning log from a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam learning_log int required The ID of the learning log.
     *
     * @response 204 {
     *   "success": true,
     *   "message": "Learning log deleted successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Learning log not found for this task."
     * }
     */
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
