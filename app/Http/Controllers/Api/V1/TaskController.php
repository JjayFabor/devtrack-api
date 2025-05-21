<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\TaskRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;

/**
 * @group Tasks
 *
 * APIs for managing tasks related to projects.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN}
 * @header x-api-key {YOUR_API_KEY}
 */
class TaskController extends Controller
{
    /**
     * List tasks for a project
     *
     * Retrieve all tasks associated with a specific project.
     *
     * @urlParam project int required The ID of the project.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task list retrieved successfully.",
     *   "data": [...]
     * }
     */
    public function index(Project $project)
    {
        return TaskResource::collection($project->tasks)
            ->additional([
                'success' => true,
                'message' => 'Task list retrieved successfully.',
            ]);
    }

    /**
     * List all tasks
     *
     * Retrieve all tasks across all projects.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "All tasks retrieved successfully.",
     *   "data": [...]
     * }
     */
    public function showAllTasks()
    {
        return TaskResource::collection(Task::all())
            ->additional([
                'success' => true,
                'message' => 'All tasks retrieved successfully.',
            ]);
    }

    /**
     * Create a new task for a project
     *
     * Store a new task under a specific project.
     *
     * @urlParam project int required The ID of the project.
     * @bodyParam title string required The title of the task. Example: "Implement login"
     * @bodyParam description string The task description. Example: "Implement user login with validation"
     * @bodyParam status string The status of the task. Example: "todo" | ['todo', 'in_progress', 'done']
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Task created successfully.",
     *   "data": {...}
     * }
     */
    public function store(TaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->all());

        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'message' => 'Task created successfully.',
            ]);
    }

    /**
     * Show a specific task in a project
     *
     * Retrieve details of a specific task for a project.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task retrieved successfully.",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Task not found in this project."
     * }
     */
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

    /**
     * Update a task in a project
     *
     * Update the details of a specific task for a project.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     * @bodyParam title string The updated title. Example: "Update login"
     * @bodyParam description string The updated description. Example: "Update login validation"
     * @bodyParam status string The updated status. Example: "done" | ['todo', 'in_progress', 'done']
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task updated successfully.",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Task not found in this project."
     * }
     */
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

    /**
     * Delete a task from a project
     *
     * Delete a specific task from a project.
     *
     * @urlParam project int required The ID of the project.
     * @urlParam task int required The ID of the task.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Task deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Task not found in this project."
     * }
     */
    public function destroy(Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted successfully.'], 200);
    }
}
