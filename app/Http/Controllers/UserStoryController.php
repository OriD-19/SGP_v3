<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        // Logic to create a new user story
        return response()->json([
            'message' => 'User story created successfully',
            'user_story' => [
                'id' => 1,
                'title' => $request->input('title'),
            ]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing user story
    }

    public function destroy($organizationId, $projectId, $userStoryId)
    {
        $userStory = UserStory::findOrFail($userStoryId);

        $userStory->delete();
        // Logic to delete a user story
        return response()->json(null, 204);
    }
}
