<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Error;
use App\Models\Project;
use App\Http\Requests\ErrorRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ErrorResource;

/**
 * @group Errors
 *
 * APIs for managing errors related to tasks.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN}
 * @header x-api-key {YOUR_API_KEY}
 */
class ErrorController extends Controller
{

    /**
     * List errors for a task
     *
     * Retrieve all errors associated with a specific task in a project.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Error list retrieved successfully",
     *   "data": [...]
     * }
     */
    public function index(Project $project, Task $task)
    {
        return ErrorResource::collection($task->errors)
            ->additional([
                'success' => true,
                'message' => 'Error list retrieved successfully',
            ]);
    }

    /**
     * Create a new error for a task
     *
     * Store a new error entry for a specific task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @bodyParam message string required The error message. Example: "Null pointer exception"
     * @bodyParam details string Additional error details. Example: "Stack trace..."
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Error created successfully",
     *   "data": {...}
     * }
     */
    public function store(ErrorRequest $request, Project $project, Task $task)
    {
        $error_data = $task->errors()->create($request->all());

        return (new ErrorResource($error_data))
            ->additional([
                'success' => true,
                'message' => 'Error created successfully',
            ]);
    }

    /**
     * Show a specific error
     *
     * Retrieve details of a specific error for a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam error int required The ID of the error.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Error retrieved successfully",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Error not found"
     * }
     */
    public function show(Project $project, Task $task, Error $error)
    {
        if ($error->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Error not found',
            ], 404);
        }

        return (new ErrorResource($error))
            ->additional([
                'success' => true,
                'message' => 'Error retrieved successfully',
            ]);
    }

    /**
     * Update an error
     *
     * Update the details of a specific error for a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam error int required The ID of the error.
     * @bodyParam message string The updated error message. Example: "Updated error message"
     * @bodyParam details string The updated error details. Example: "Updated stack trace..."
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Error updated successfully",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Error not found"
     * }
     */
    public function update(ErrorRequest $request, Project $project, Task $task, Error $error)
    {
        if ($error->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Error not found',
            ], 404);
        }

        $error->update($request->all());

        return (new ErrorResource($error))
            ->additional([
                'success' => true,
                'message' => 'Error updated successfully',
            ]);
    }

    /**
     * Delete an error
     *
     * Delete a specific error from a task.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @urlParam error int required The ID of the error.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Error deleted successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Error not found"
     * }
     */
    public function destroy(Project $project, Task $task, Error $error)
    {
        if ($error->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Error not found',
            ], 404);
        }

        $error->delete();
        return response()->json([
            'success' => true,
            'message' => 'Error deleted successfully',
        ], 200);
    }
}
