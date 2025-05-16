<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{project}/tasks",
     *     summary="Retrieve all tasks for a specific project",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="ID of the project to retrieve tasks from",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task list retrieved successfully."),
     *             @OA\Property(
     *                 property="task",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Task Title"),
     *                 @OA\Property(property="description", type="string", example="Task Description"),
     *                 @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *                 @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="is_recurring", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Project not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Project $project)
    {
        return response()->json([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
            'tasks' => $project->tasks
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="Retrieve all tasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Task list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task list retrieved successfully."),
     *             @OA\Property(
     *                 property="task",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Task Title"),
     *                 @OA\Property(property="description", type="string", example="Task Description"),
     *                 @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *                 @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="is_recurring", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function showAllTasks()
    {
        $tasks = Task::all();

        return response()->json([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
            'tasks' => $tasks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

   /**
     * @OA\Post(
     *     path="/api/v1/projects/{project}/tasks",
     *     summary="Create a new task for a specific project",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="ID of the project to create a task for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Task Title"),
     *             @OA\Property(property="description", type="string", example="Task Description"),
     *             @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *             @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *             @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *             @OA\Property(property="is_recurring", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task created successfully."),
     *             @OA\Property(
     *                 property="task",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Task Title"),
     *                 @OA\Property(property="description", type="string", example="Task Description"),
     *                 @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *                 @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="is_recurring", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(type="string", example="The title field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="array",
     *                     @OA\Items(type="string", example="The status field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="priority",
     *                     type="array",
     *                     @OA\Items(type="string", example="The priority field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="deadline",
     *                     type="array",
     *                     @OA\Items(type="string", example="The deadline field must be a valid date.")
     *                 ),
     *                 @OA\Property(
     *                     property="is_recurring",
     *                     type="array",
     *                     @OA\Items(type="string", example="The is recurring field must be true or false.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/api/v1/projects/{project}/tasks/{task}",
     *     summary="Retrieve a specific task from a project",
     *     description="Returns details of a specific task belonging to the specified project.",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task retrieved successfully."),
     *             @OA\Property(
     *                 property="task",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Task Title"),
     *                 @OA\Property(property="description", type="string", example="Task Description"),
     *                 @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *                 @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="is_recurring", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Task not found in this project.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Project not found.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show(Project $project, Task $task)
    {
        // Verify the task belongs to the project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found in this project.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Task retrieved successfully.', 'task' => $task], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/projects/{project}/tasks/{task}",
     *     summary="Update a specific task in a project",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Task Title"),
     *             @OA\Property(property="description", type="string", example="Updated Task Description"),
     *             @OA\Property(property="status", type="string", example="in_progress", enum={"todo", "in_progress", "done"}),
     *             @OA\Property(property="priority", type="string", example="medium", enum={"low", "medium", "high"}),
     *             @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *             @OA\Property(property="is_recurring", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task updated successfully."),
     *             @OA\Property(
     *                 property="task",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="project_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Task Title"),
     *                 @OA\Property(property="description", type="string", example="Updated Task Description"),
     *                 @OA\Property(property="status", type="string", example="in_progress", enum={"todo", "in_progress", "done"}),
     *                 @OA\Property(property="priority", type="string", example="medium", enum={"low", "medium", "high"}),
     *                 @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="is_recurring", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Task not found in this project.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Project not found.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(type="string", example="The title field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="array",
     *                     @OA\Items(type="string", example="The status field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="priority",
     *                     type="array",
     *                     @OA\Items(type="string", example="The priority field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="deadline",
     *                     type="array",
     *                     @OA\Items(type="string", example="The deadline field must be a valid date.")
     *                 ),
     *                 @OA\Property(
     *                     property="is_recurring",
     *                     type="array",
     *                     @OA\Items(type="string", example="The is recurring field must be true or false.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{project}/tasks/{task}",
     *     summary="Delete a specific task from a project",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="ID of the project",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="ID of the task",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Task not found in this project.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="success", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Project not found.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     * )
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
