<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoryCreateRequest;
use App\Http\Requests\UserStoryUpdateRequest;
use App\Http\Resources\UserStoryResource;
use App\Models\UserStory;
use Illuminate\Http\Request;

class UserStoryController extends Controller
{
    public function index(Request $request, $organizationId, $projectId)
    {
        // Logic to get all user stories
        if ($request->user()->cannot('viewAny', [UserStory::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $userStories = UserStory::where('project_id', $projectId)->get();

        return UserStoryResource::collection($userStories);
    }

    public function show($id)
    {
        // Logic to get a specific user story by ID
    }

    public function store(UserStoryCreateRequest $request, $organizationId, $projectId)
    {
        // Logic to create a new user story
        if ($request->user()->cannot('create', [UserStory::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        $user_story = UserStory::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'priority_id' => $validated['priority_id'],
            'project_id' => $projectId,
        ]);

        return response()->json([
            'message' => 'User story created successfully',
            'user_story' => $user_story,
        ], 201);
    }

    public function update(UserStoryUpdateRequest $request, $organizationId, $projectId, $userStoryId)
    {
        // Logic to update an existing user story

        if ($request->user()->cannot('update', [UserStory::class, $projectId])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userStory = UserStory::findOrFail($userStoryId);
        $validated = $request->validated();

        $userStory->fill($validated);
        $userStory->save();

        return response()->json([
            'message' => 'User story updated successfully',
            'user_story' => $userStory,
        ], 200);

    }

    public function destroy(Request $request, $organizationId, $projectId, $userStoryId)
    {
        if ($request->user()->cannot('delete', [UserStory::class, $projectId])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userStory = UserStory::findOrFail($userStoryId);

        $userStory->delete();
        // Logic to delete a user story
        return response()->json(null, 204);
    }

    public function changePriority(Request $request, $organizationId, $projectId, $userStoryId) 
    {
        if ($request->user()->cannot('changePriority', [UserStory::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'priority_id' => 'required|integer|exists:priorities,id',
        ]);

        $userStory = UserStory::findOrFail($userStoryId);
        $userStory->priority_id = $validated['priority_id'];
        $userStory->save();

        return response()->json([
            'message' => 'user story priority updated successfully',
            'user_story' => $userStory,
        ], 200);
    }

    public function changeSprint(Request $request, $organizationId, $projectId, $userStoryId) 
    {
        if ($request->user()->cannot('changeSprint', [UserStory::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'sprint_id' => 'required|integer|exists:sprints,id',
        ]);

        $userStory = UserStory::findOrFail($userStoryId);
        $userStory->sprint_id = $validated['sprint_id'];
        $userStory->save();

        return response()->json([
            'message' => 'user story sprint updated successfully',
            'user_story' => $userStory,
        ], 200);
    }

}
