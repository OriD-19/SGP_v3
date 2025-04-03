<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(Request $request, $organizationId, $projectId)
    {
        // Logic to get all team members for a project
        if ($request->user()->cannot('viewAny', [TeamMember::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $teamMembers = TeamMember::where('project_id', $projectId)->get();

        return response()->json($teamMembers, 200);
    }

    public function show($id)
    {
        // Logic to get a specific team member by ID
        $teamMember = TeamMember::findOrFail($id);

        return response()->json($teamMember, 200);
    }

    public function store(Request $request, $organizationId, $projectId)
    {
        // Logic to add a new team member to a project
        if ($request->user()->cannot('create', [TeamMember::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:255|exists:roles,name',
        ]);

        $teamMember = TeamMember::create([
            'user_id' => $request->input('user_id'),
            'project_id' => $projectId,
            'organization_id' => $organizationId,
        ]);

        $teamMember->assignRole($request->input('role'));
        return response()->json($teamMember, 201);
    }

    public function update(Request $request, $organizationId, $projectId, $teamMemberId)
    {
        // Logic to update an existing team member's role
        if ($request->user()->cannot('update', [TeamMember::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'role' => 'required|string|max:255|exists:roles,name',
        ]);

        $teamMember = TeamMember::findOrFail($teamMemberId);
        $teamMember->syncRoles([$request->input('role')]);

        return response()->json($teamMember, 200);
    }

    public function destroy(Request $request, $organizationId, $projectId, $teamMemberId)
    {
        // Logic to remove a team member from a project
        if ($request->user()->cannot('delete', [TeamMember::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $teamMember = TeamMember::findOrFail($teamMemberId);
        $teamMember->delete();

        return response()->json(['message' => 'Team member removed successfully'], 200);
    }
}
