<?php

namespace App\Http\Controllers;

use App\Http\Requests\SprintCreateRequest;
use App\Http\Requests\SprintUpdateRequest;
use App\Http\Resources\SprintResource;
use App\Models\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Request $request, $organizationId, $projectId)
    {
        // Logic to retrieve and return all sprints
        // Check if the user has permission to view sprints
        if ($request->user()->cannot('viewAny', [Sprint::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $sprints = Sprint::where('project_id', $projectId)->get();

        return SprintResource::collection($sprints);
    }
    public function store(SprintCreateRequest $request)
    {
        $validated = $request->validated();

        $sprint = Sprint::create([
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'start_date' => $validated['start_date'],
            'active' => false, // active by default
        ]);

        return response()->json([
            'message' => 'Sprint created successfully',
            'sprint' => $sprint,
        ], 201);

    }
    public function show($id)
    {
        // Logic to retrieve and return a specific sprint
    }
    public function update(SprintUpdateRequest $request, $organizationId, $projectId, $sprintId)
    {
        // Check if the user has permission to update the sprint
        $sprint = Sprint::findOrFail($sprintId);
        if ($request->user()->cannot('update', $sprint)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $validated = $request->validated();

        // Logic to update a specific sprint

        $sprint->fill($validated);
        $sprint->save();

        return response()->json([
            'message' => 'Sprint updated successfully',
            'sprint' => $sprint,
        ], 200);
    }

    public function destroy(Request $request, $organizationId, $projectId, $sprintId)
    {
        // Logic to delete a specific sprint
        $sprint = Sprint::findOrFail($sprintId);
        if ($request->user()->cannot('delete', $sprint)) {
            abort(403, 'Unauthorized action.');
        }

        $sprint->delete();

        return response()->json([
            'message' => 'sprint deleted successfully',
        ], 200);
    }
}
