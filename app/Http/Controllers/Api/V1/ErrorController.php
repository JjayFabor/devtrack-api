<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Error;
use App\Http\Requests\ErrorRequest;
use App\Http\Controllers\Controller;

class ErrorController extends Controller
{
    public function index(Project $project, Task $task)
    {
        return response()->json([
            'success' => true,
            'message' => 'Errors Data retrieved successfully',
            'error_data' => $task->errors()->with('project')->get(),
        ], 200);
    }

    public function store(ErrorRequest $request, Project $project, Task $task)
    {
        $error_data = $task->errors()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Error created successfully',
            'error_data' => $error_data,
        ], 201);
    }

    public function show(Project $project, Task $task, Error $error)
    {
        if ($error->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Error not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Error Data retrieved successfully',
            'error_data' => $error,
        ], 200);
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

        return response()->json([
            "success" => true,
            "message" => "Error updated successfully",
            "error_data" => $error,
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
