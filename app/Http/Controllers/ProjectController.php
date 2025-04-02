<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectCreateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request, $organizationId)
    {
        // Logic to list projects for the organization
        if ($request->user()->cannot('viewAny', Project::class)) {
            abort(403, 'Unauthorized action.');
        }

        $projects = Project::where('organization_id', $organizationId)->get();

        array_filter($projects->toArray(), function ($project) use ($organizationId) {
            return $project['organization_id'] == $organizationId;
        });

        return ProjectResource::collection($projects);
    }

    public function store(ProjectCreateRequest $request, $organizationId)
    {
        $validated = $request->validated();

        // get the organization id frmo the route

        Project::create([
            'name' => $validated['project_name'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'organization_id' => $organizationId,
        ]);

        return response()->json([
            'message' => 'project created successfully',
        ], 201);

    }
    public function show(Request $request, $organizationId, $projectId)
    {
        $project = Project::findOrFail($projectId);

        if (!$request->user()->can('view', $project)) {
            abort(403, 'Unauthorized action.');
        }

        return ProjectResource::make($project);

    }

    public function update(Request $request, $organizationId, $projectId)
    {
        // Logic to update a specific project
    }
    public function destroy(Request $request, $organizationId, $projectId)
    {
        // Logic to delete a specific project
    }
}
