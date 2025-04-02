<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoryCreateRequest;
use App\Http\Requests\UserStoryUpdateRequest;
use App\Models\UserStory;
use Illuminate\Http\Request;

class UserStoryController extends Controller
{
    public function index()
    {
        // Logic to get all user stories
        return response()->json([
            'user_stories' => [
                // Example data
                ['id' => 1, 'title' => 'User Story 1'],
                ['id' => 2, 'title' => 'User Story 2'],
            ]
        ], 200);
    }
    public function show($id)
    {
        // Logic to get a specific user story by ID
    }

    public function store(UserStoryCreateRequest $request, $organizationId, $projectId)
    {
        // Logic to create a new user story
        if ($request->user()->cannot('create', UserStory::class)) {
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

    public function update(UserStoryUpdateRequest $request, $organizationId, $projcetId, $userStoryId)
    {
        // Logic to update an existing user story

        $validated = $request->validated();

        if ($request->user()->cannot('update', UserStory::class, $userStoryId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $userStory = UserStory::findOrFail($userStoryId);

        $userStory->fill($validated);
        $userStory->save();

        return response()->json([
            'message' => 'User story updated successfully',
            'user_story' => $userStory,
        ], 200);

    }

    public function destroy(Request $request, $organizationId, $projectId, $userStoryId)
    {
        if ($request->user()->cannot('delete', UserStory::class, $projectId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userStory = UserStory::findOrFail($userStoryId);

        $userStory->delete();
        // Logic to delete a user story
        return response()->json(null, 204);
    }
}
