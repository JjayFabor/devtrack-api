<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Error;
use App\Models\Project;
use Illuminate\Http\Request;
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

    public function store(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code_snippet' => 'nullable|string',
            'cause' => 'nullable|string',
            'resolution' => 'nullable|string',
            'severity' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:unresolved,resolved',
        ]);

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

    public function update(Request $request, Project $project, Task $task, Error $error)
    {
        if ($error->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Error not found',
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code_snippet' => 'nullable|string',
            'cause' => 'nullable|string',
            'resolution' => 'nullable|string',
            'severity' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:unresolved,resolved',
        ]);

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
