<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\TaskRequest;
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
            'message' => 'All Task list retrieved successfully.',
            'tasks' => $tasks
        ], 200);
    }

    public function store(TaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->all());

        return response()->json(['success' => true, 'message' => 'Task created successfully.', 'task' => $task], 201);
    }

    public function show(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Task retrieved successfully.', 'task' => $task], 200);
    }

    public function update(TaskRequest $request, Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

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
