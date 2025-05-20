<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        return response()->json([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
            'tasks' => $project->tasks
        ], 200);
    }

    public function showAllTasks()
    {
        $tasks = Task::all();

        return response()->json([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
            'tasks' => $tasks
        ], 200);
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
            'deadline' => 'nullable|date',
            'is_recurring' => 'boolean'
        ]);

        $task = $project->tasks()->create($request->all());

        return response()->json(['success' => true, 'message' => 'Task created successfully.', 'task' => $task], 201);
    }

    public function show(Project $project, Task $task)
    {
        // Verify the task belongs to the project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Task retrieved successfully.', 'task' => $task], 200);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|string|in:todo,in_progress,done',
            'priority' => 'sometimes|required|string|in:low,medium,high',
            'deadline' => 'sometimes|nullable|date',
            'is_recurring' => 'sometimes|boolean'
        ]);

        $task->update($request->all());
        return response()->json(['success' => true, 'message' => 'Task updated successfully.', 'task' => $task], 200);
    }

    public function destroy(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted successfully.'], 200);
    }
}
