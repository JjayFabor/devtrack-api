<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="Retrieve all list of tasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Task list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task list retrieved successfully."),
     *             @OA\Property(
     *                 property="tasks",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="project_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Task Title"),
     *                     @OA\Property(property="description", type="string", example="Task Description"),
     *                     @OA\Property(property="status", type="string", example="todo", enum={"todo", "in_progress", "done"}),
     *                     @OA\Property(property="priority", type="string", example="low", enum={"low", "medium", "high"}),
     *                     @OA\Property(property="deadline", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                     @OA\Property(property="is_recurring", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T00:00:00Z")
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
    public function index()
    {
        return response()->json(['success' => true, 'message' => 'Task list retrieved successfully.', 'tasks' => Task::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
