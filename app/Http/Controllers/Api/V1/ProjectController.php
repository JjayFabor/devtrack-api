<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'projects' => Project::all()], 200);
    }

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

    public function show(Project $project)
    {
        $responseData = $project->toArray();
        $responseData['tags'] = $project->tags;

        return response()->json(['success' => true, 'project' => $responseData], 200);
    }

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

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted successfully.'], 200);
    }
}
