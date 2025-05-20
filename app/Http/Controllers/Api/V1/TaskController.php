<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\TaskRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        return TaskResource::collection($project->tasks)
            ->additional([
                'success' => true,
                'message' => 'Task list retrieved successfully.',
            ]);
    }

    public function showAllTasks()
    {
        return TaskResource::collection(Task::all())
            ->additional([
                'success' => true,
                'message' => 'All tasks retrieved successfully.',
            ]);
    }

    public function store(TaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->all());

        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' => 'Task created successfully.',
            ]);
    }

    public function show(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' => 'Task retrieved successfully.',
            ]);
    }

    public function update(TaskRequest $request, Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        $task->update($request->all());
        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' => 'Task updated successfully.',
            ]);
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
