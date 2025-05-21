<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;


/**
 * @group Projects
 *
 * APIs for managing projects.
 *
 * @authenticated
 * @header Authorization Bearer {YOUR ACCESS TOKEN}
 * @header x-api-key {YOUR_API_KEY}
 *
 */
class ProjectController extends Controller
{
    /**
     * List all projects
     *
     * Retrieve a list of all projects for the authenticated user.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Project list retrieved successfully.",
     *   "data": [...]
     * }
     */
    public function index()
    {
        return ProjectResource::collection(Project::all())
            ->additional([
                'success' => true,
                'message' => 'Project list retrieved successfully.',
            ]);
    }

    /**
     * Create a new project
     *
     * Store a new project for the authenticated user.
     *
     * @bodyParam name string required The name of the project. Example: "My Project"
     * @bodyParam description string The project description. Example: "A sample project"
     * @bodyParam tags array List of tags. Example: ["php", "laravel"]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Project created successfully.",
     *   "data": {...}
     * }
     */
    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = $request->user()->id;
        $project = Project::create($validated);

        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project created successfully.',
            ]);
    }

    /**
     * Show a project
     *
     * Retrieve details of a specific project.
     *
     * @urlParam project int required The ID of the project.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Project retrieved successfully.",
     *   "data": {...}
     * }
     */
    public function show(Project $project)
    {
        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project retrieved successfully.',
            ]);
    }

    /**
     * Update a project
     *
     * Update the details of a specific project.
     *
     * @urlParam project int required The ID of the project.
     * @bodyParam name string The updated name. Example: "Updated Project"
     * @bodyParam description string The updated description. Example: "Updated description"
     * @bodyParam tags array List of tags. Example: ["api", "backend"]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Project updated successfully.",
     *   "data": {...}
     * }
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        if (isset($validated['tags']) && is_array($validated['tags']) && count($validated['tags']) === 1 && is_array($validated['tags'][0])) {
            $validated['tags'] = $validated['tags'][0];
        }

        $project->update($validated);

        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project updated successfully.',
            ]);
    }

    /**
     * Delete a project
     *
     * Delete a specific project.
     *
     * @urlParam project int required The ID of the project.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Project deleted successfully."
     * }
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.'], 200);
    }
}
