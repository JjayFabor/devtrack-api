<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Error;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ErrorController extends Controller
{
   /**
    * @OA\Get(
    *     path="/api/v1/projects/{project}/tasks/{task}/errors",
    *     summary="Get all errors for a specific task",
    *     tags={"Errors"},
    *     @OA\Parameter(
    *         name="project",
    *         in="path",
    *         required=true,
    *         description="Project ID",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Parameter(
    *         name="task",
    *         in="path",
    *         required=true,
    *         description="Task ID",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Errors retrieved successfully",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean"),
    *             @OA\Property(property="message", type="string"),
    *             @OA\Property(
    *                 property="error_data",
    *                 type="array",
    *                 @OA\Items(
    *                     @OA\Property(property="id", type="integer"),
    *                     @OA\Property(property="title", type="string"),
    *                     @OA\Property(property="description", type="string"),
    *                     @OA\Property(property="code_snippet", type="string"),
    *                     @OA\Property(property="cause", type="string"),
    *                     @OA\Property(property="resolution", type="string"),
    *                     @OA\Property(property="severity", type="string"),
    *                     @OA\Property(property="status", type="string"),
    *                     @OA\Property(property="created_at", type="string", format="date-time"),
    *                     @OA\Property(property="updated_at", type="string", format="date-time"),
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Error not found",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean"),
    *             @OA\Property(property="message", type="string")
    *         )
    *     )
    * )
    */
    public function index(Project $project, Task $task)
    {
        return response()->json([
            'success' => true,
            'message' => 'Errors Data retrieved successfully',
            'error_data' => $task->errors()->with('project')->get(),
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/projects/{project}/tasks/{task}/errors",
     *     summary="Create a new error for a specific task",
     *     tags={"Errors"},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="code_snippet", type="string"),
     *             @OA\Property(property="cause", type="string"),
     *             @OA\Property(property="resolution", type="string"),
     *             @OA\Property(property="severity", type="string", enum={"low", "medium", "high"}),
     *             @OA\Property(property="status", type="string", enum={"unresolved", "resolved"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Error created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="error_data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="code_snippet", type="string"),
     *                 @OA\Property(property="cause", type="string"),
     *                 @OA\Property(property="resolution", type="string"),
     *                 @OA\Property(property="severity", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="code_snippet", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="cause", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="resolution", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="severity", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="status", type="array", @OA\Items(type="string")),
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *    path="/api/v1/projects/{project}/tasks/{task}/errors/{error}",
     *   summary="Get a specific error data on a specific task",
     *   tags={"Errors"},
     *  @OA\Parameter(
     *        name="project",
     *       in="path",
     *       required=true,
     *      description="Project ID",
     *       @OA\Schema(type="integer")
     *   ),
     *  @OA\Parameter(
     *       name="task",
     *      in="path",
     *      required=true,
     *     description="Task ID",
     *      @OA\Schema(type="integer")
     *  ),
     * @OA\Parameter(
     *       name="error",
     *      in="path",
     *     required=true,
     *     description="Error ID",
     *     @OA\Schema(type="integer")
     *  ),
     * @OA\Response(
     *        response=200,
     *       description="Error data retrieved successfully",
     *      @OA\JsonContent(
     *           @OA\Property(property="success", type="boolean"),
     *          @OA\Property(property="message", type="string"),
     *          @OA\Property(
     *               property="error_data",
     *               type="object",
     *               @OA\Property(property="id", type="integer"),
     *               @OA\Property(property="title", type="string"),
     *               @OA\Property(property="description", type="string"),
     *               @OA\Property(property="code_snippet", type="string"),
     *               @OA\Property(property="cause", type="string"),
     *               @OA\Property(property="resolution", type="string"),
     *               @OA\Property(property="severity", type="string"),
     *               @OA\Property(property="status", type="string"),
     *               @OA\Property(property="created_at", type="string", format="date-time"),
     *               @OA\Property(property="updated_at", type="string", format="date-time"),
     *           )
     *       )
     *   ),
     * @OA\Response(
     *       response=404,
     *      description="Error not found",
     *     @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean"),
     *         @OA\Property(property="message", type="string")
     *     )
     *   )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/v1/projects/{project}/tasks/{task}/errors/{error}",
     *     summary="Update a specific error data on a specific task",
     *     tags={"Errors"},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="error",
     *         in="path",
     *         required=true,
     *         description="Error ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="code_snippet", type="string"),
     *             @OA\Property(property="cause", type="string"),
     *             @OA\Property(property="resolution", type="string"),
     *             @OA\Property(property="severity", type="string", enum={"low", "medium", "high"}),
     *             @OA\Property(property="status", type="string", enum={"unresolved", "resolved"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Error updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="error_data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="code_snippet", type="string"),
     *                 @OA\Property(property="cause", type="string"),
     *                 @OA\Property(property="resolution", type="string"),
     *                 @OA\Property(property="severity", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error not found",
     *        @OA\JsonContent(
     *            @OA\Property(property="success", type="boolean"),
     *           @OA\Property(property="message", type="string")
     *        )
     *    )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{project}/tasks/{task}/errors/{error}",
     *     summary="Delete a specific error data on a specific task",
     *     tags={"Errors"},
     *     @OA\Parameter(
     *         name="project",
     *         in="path",
     *         required=true,
     *         description="Project ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="error",
     *         in="path",
     *         required=true,
     *         description="Error ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Error deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *    ),
     *    @OA\Response(
     *        response=404,
     *       description="Error not found",
     *       @OA\JsonContent(
     *           @OA\Property(property="success", type="boolean"),
     *          @OA\Property(property="message", type="string")
     *        )
     *    )
     * )
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
