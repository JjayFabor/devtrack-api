<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
    {
    /**
     * @OA\Get(
     *     path="/api/v1/projects",
     *     summary="Get all projects",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *            @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="projects",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *                         @OA\Items(type="string")
     *                     ),
     *                     @OA\Property(property="github_url", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['success' => true, 'projects' => Project::all()], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/projects",
     *     summary="Create a new project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Project Name"),
     *             @OA\Property(property="description", type="string", example="Project Description"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"tag1", "tag2"}),
     *             @OA\Property(property="github_url", type="string", example="https://github.com/TestUser/test-repo"),
     *             @OA\Property(property="status", type="string", example="planning"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(
     *           @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="project", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Project Name"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Project Description"),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(type="string", example={"tag1", "tag2"})
     *                 ),
     *                 @OA\Property(property="github_url", type="string", example="https://github.com/TestUser/test-repo"),
     *                 @OA\Property(property="status", type="string", example="planning"),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="array",
     *                     @OA\Items(type="string", example="The description field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected status is invalid.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|array',
            'github_url' => app()->environment('local') ? 'nullable|string' : 'nullable|url:https',
            'status' => 'required|string|in:planning,active,paused,completed',
        ]);

        $validated['user_id'] = $request->user()->id;
        $project = Project::create($validated);

        $responseData = $project->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'project' => $responseData], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}",
     *     summary="Get a specific project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="project", type="object",
     *                @OA\Property(property="id", type="integer", example=1),
     *                @OA\Property(property="name", type="string", example="Project Name"),
     *                @OA\Property(property="user_id", type="integer", example=1),
     *                @OA\Property(property="description", type="string", example="Project Description"),
     *                @OA\Property(
     *                   property="tags",
     *                   type="array",
     *                   @OA\Items(type="string", example={"tag1", "tag2"})
     *               ),
     *                @OA\Property(property="github_url", type="string", example="https://github.com/TestUser/test-repo"),
     *                @OA\Property(property="status", type="string", example="planning"),
     *                @OA\Property(
     *                   property="created_at",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *               @OA\Property(
     *                  property="updated_at",
     *                 type="string",
     *                format="date-time"
     *            )
     *           )
     *        )
     *    ),
     *    @OA\Response(
     *        response=404,
     *       description="Project not found",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Project not found")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *      description="Unauthenticated",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="message",
     *             type="string",
     *             example="Unauthenticated."
     *         )
     *     )
     *  )
     * )
     */
    public function show(Project $project)
    {
        $responseData = $project->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'project' => $responseData], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'tags' => 'nullable|array',
            'github_url' => app()->environment('local') ? 'sometimes|nullable|string' : 'sometimes|nullable|url:https',
            'status' => 'sometimes|required|string|in:planning,active,paused,completed',
        ]);

        if (isset($validated['tags']) && is_array($validated['tags']) && count($validated['tags']) === 1 && is_array($validated['tags'][0])) {
            $validated['tags'] = $validated['tags'][0];
        }

        $project->update($validated);

        $responseData = $project->fresh()->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'message' => 'Project updated successfully.', 'project' => $responseData], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{id}",
     *     summary="Delete a project",
     *     tags={"Projects"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Project deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Project not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.'], 200);
    }
}
