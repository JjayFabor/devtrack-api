<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'projects' => Project::all()], 200);
    }

    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = $request->user()->id;
        $project = Project::create($validated);

        $responseData = $project->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'project' => $responseData], 201);
    }

    public function show(Project $project)
    {
        $responseData = $project->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'project' => $responseData], 200);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        if (isset($validated['tags']) && is_array($validated['tags']) && count($validated['tags']) === 1 && is_array($validated['tags'][0])) {
            $validated['tags'] = $validated['tags'][0];
        }

        $project->update($validated);

        $responseData = $project->fresh()->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'message' => 'Project updated successfully.', 'project' => $responseData], 200);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.'], 200);
    }
}
