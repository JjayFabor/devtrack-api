<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Error;
use App\Models\Project;
use App\Http\Requests\ErrorRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\ErrorResource;

/**
 * @group Error
 *
 * APIs for managing errors related to tasks.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN} "Bearer 1|abc123..."
 * @header x-api-key your-api-key-hear
 */
class ErrorController extends Controller
{
    public function index(Project $project, Task $task)
    {
        return ErrorResource::collection($task->errors)
            ->additional([
                'success' => true,
                'message' => 'Error list retrieved successfully',
            ]);
    }

    public function store(ErrorRequest $request, Project $project, Task $task)
    {
        $error_data = $task->errors()->create($request->all());

        return (new ErrorResource($error_data))
            ->additional([
                'success' => true,
                'message' => 'Error created successfully',
            ]);
    }

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
