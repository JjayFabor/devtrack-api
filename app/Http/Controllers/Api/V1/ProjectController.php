<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function index()
    {
        return ProjectResource::collection(Project::all())
            ->additional([
                'success' => true,
                'message' => 'Project list retrieved successfully.',
            ]);
    }

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

    public function show(Project $project)
    {
        return (new ProjectResource($project))
            ->additional([
                'success' => true,
                'message' => 'Project retrieved successfully.',
            ]);
    }

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

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.'], 200);
    }
}
