<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use App\Models\LearningLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LearningLogController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{project}/tasks/{task}/learning-logs",
     *     summary="Retrieve all learning logs for a specific task within a project",
     *     tags={"Learning Logs"},
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
     *         description="ID of the task to retrieve learning logs from",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Learning logs retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Learning logs retrieved successfully"),
     *             @OA\Property(
     *                 property="learning_logs",
     *                 type="array",
     *                 @OA\Items(
     *                    @OA\Property(property="id", type="integer"),
     *                   @OA\Property(property="title", type="string"),
     *                   @OA\Property(property="topic", type="string"),
     *                   @OA\Property(property="summary", type="string"),
     *                   @OA\Property(property="duration", type="integer"),
     *                   @OA\Property(property="resources", type="array", @OA\Items(type="string")),
     *                   @OA\Property(property="created_at", type="string", format="date-time"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time"),
     *                   @OA\Property(
     *                       property="project",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                   )
     *               )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Project or Task not found.")
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
    public function index(Project $project, Task $task)
    {
        return response()->json([
            'success' => true,
            'message' => 'Learning logs retrieved successfully',
            'learning_logs' => $task->learningLogs()->with('project')->get(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/projects/{project}/tasks/{task}/learning-logs",
     *     summary="Create a new learning log for a specific task within a project",
     *     tags={"Learning Logs"},
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
     *         description="ID of the task to create a learning log for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "topic", "summary", "duration"},
     *             @OA\Property(property="title", type="string", example="Learning Log Title"),
     *             @OA\Property(property="topic", type="string", example="Learning Topic"),
     *             @OA\Property(property="summary", type="string", example="Summary of the learning log"),
     *             @OA\Property(property="duration", type="integer", example=120),
     *             @OA\Property(property="resources", type="array", @OA\Items(type="string"), example={"resource1", "resource2"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Learning log created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Learning log created successfully"),
     *             @OA\Property(
     *                 property="learning_log",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="topic", type="string"),
     *                 @OA\Property(property="summary", type="string"),
     *                 @OA\Property(property="duration", type="integer"),
     *                 @OA\Property(property="resources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="project",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 ),
     *                 @OA\Property(
     *                     property="task",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="priority", type="string"),
     *                     @OA\Property(property="deadline", type="string", format="date-time"),
     *                     @OA\Property(property="is_recurring", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Project or Task not found.")
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
    public function store(Request $request, Project $project, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'summary' => 'required|string',
            'duration' => 'required|integer',
            'resources' => 'nullable|array',
        ]);

        $learningLog = $task->learningLogs()->create($request->all());

        $responseData = $learningLog->toArray();
        $responseData['resources'] = $learningLog->resources;

        return response()->json([
            'success' => true,
            'message' => 'Learning log created successfully',
            'learning_log' => $responseData,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{project}/tasks/{task}/learning-logs/{learningLog}",
     *     summary="Retrieve a specific learning log for a task within a project",
     *     tags={"Learning Logs"},
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
     *     @OA\Parameter(
     *         name="learningLog",
     *         in="path",
     *         required=true,
     *         description="ID of the learning log",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Learning log retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Learning log retrieved successfully"),
     *             @OA\Property(
     *                 property="learning_log",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="topic", type="string"),
     *                 @OA\Property(property="summary", type="string"),
     *                 @OA\Property(property="duration", type="integer"),
     *                 @OA\Property(property="resources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="project",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 ),
     *                 @OA\Property(
     *                     property="task",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="priority", type="string"),
     *                     @OA\Property(property="deadline", type="string", format="date-time"),
     *                     @OA\Property(property="is_recurring", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Learning log not found for this task.")
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
    public function show(Project $project, Task $task, LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Learning log retrieved successfully',
            'learning_log' => $learningLog,
        ]);
    }

   /**
    * @OA\Put(
    *     path="/api/v1/projects/{project}/tasks/{task}/learning-logs/{learningLog}",
    *     summary="Update a specific learning log for a task within a project",
    *     tags={"Learning Logs"},
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
    *     @OA\Parameter(
    *         name="learningLog",
    *         in="path",
    *         required=true,
    *         description="ID of the learning log",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             @OA\Property(property="title", type="string", example="Updated Learning Log Title"),
    *             @OA\Property(property="topic", type="string", example="Updated Learning Topic"),
    *             @OA\Property(property="summary", type="string", example="Updated summary of the learning log"),
    *             @OA\Property(property="duration", type="integer", example=150),
    *             @OA\Property(property="resources", type="array", @OA\Items(type="string"), example={"updated_resource1", "updated_resource2"}),
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Learning log updated successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=true),
    *             @OA\Property(property="message", type="string", example="Learning log updated successfully"),
    *             @OA\Property(
    *                 property="learning_log",
    *                 type="object",
    *                 @OA\Property(property="id", type="integer"),
    *                 @OA\Property(property="title", type="string"),
    *                 @OA\Property(property="topic", type="string"),
    *                 @OA\Property(property="summary", type="string"),
    *                 @OA\Property(property="duration", type="integer"),
    *                 @OA\Property(property="resources", type="array", @OA\Items(type="string")),
    *                 @OA\Property(property="created_at", type="string", format="date-time"),
    *                 @OA\Property(property="updated_at", type="string", format="date-time"),
    *                 @OA\Property(
    *                     property="project",
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="created_at", type="string", format="date-time"),
    *                     @OA\Property(property="updated_at", type="string", format="date-time"),
    *                 ),
    *                 @OA\Property(
    *                     property="task",
    *                     type="object",
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="title", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="status", type="string"),
    *                     @OA\Property(property="priority", type="string"),
    *                     @OA\Property(property="deadline", type="string", format="date-time"),
    *                     @OA\Property(property="is_recurring", type="boolean"),
    *                     @OA\Property(property="created_at", type="string", format="date-time"),
    *                     @OA\Property(property="updated_at", type="string", format="date-time"),
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="Learning log not found for this task.")
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example=false),
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(
    *                 property="errors",
    *                 type="object",
    *                 @OA\AdditionalProperties(
    *                     type="array",
    *                     @OA\Items(type="string")
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
    public function update(Request $request, Project $project, Task $task,  LearningLog $learningLog)
    {
        if ($learningLog->task_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Learning log not found for this task.',
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'topic' => 'sometimes|required|string|max:255',
            'summary' => 'sometimes|required|string',
            'duration' => 'sometimes|required|integer',
            'resources' => 'sometimes|nullable|array',
        ]);

        $learningLog->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Learning log updated successfully',
            'learning_log' => $learningLog,
        ]);
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/projects/{project}/tasks/{task}/learning-logs/{learningLog}",
     *   summary="Delete a specific learning log for a task within a project",
     *   tags={"Learning Logs"},
     *  security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *        name="project",
     *       in="path",
     *       required=true,
     *      description="ID of the project",
     *      @OA\Schema(type="integer")
     *    ),
     *   @OA\Parameter(
     *       name="task",
     *      in="path",
     *      required=true,
     *     description="ID of the task",
     *     @OA\Schema(type="integer")
     *    ),
     *  @OA\Parameter(
     *       name="learningLog",
     *      in="path",
     *     required=true,
     *    description="ID of the learning log",
     *    @OA\Schema(type="integer")
     *   ),
     *  @OA\Response(
     *       response=200,
     *      description="Learning log deleted successfully",
     *     @OA\JsonContent(
     *           @OA\Property(property="success", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="Learning log deleted successfully")
     *        )
     *   ),
     * @OA\Response(
     *       response=404,
     *      description="Not found",
     *     @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean", example=false),
     *         @OA\Property(property="message", type="string", example="Learning log not found for this task.")
     *       )
     *  ),
     * @OA\Response(
     *      response=401,
     *     description="Unauthenticated",
     *    @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=false),
     *        @OA\Property(property="message", type="string", example="Unauthenticated.")
     *      )
     * )
     * )
     *
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
