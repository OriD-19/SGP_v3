<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\UserStory;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Logic to display a list of tasks
    }

    public function store(Request $request, $organizationId, $projectId, $userStoryId)
    {
        // Logic to store a new task

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'priority_id' => 'required|exists:priorities,id',
            'due_date' => 'nullable|date_format:Y-m-d',
        ]);

        $userStory = UserStory::findOrFail($userStoryId);

        $task = $userStory->tasks()->save(new Task([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status_id' => $request->input('status_id'),
            'priority_id' => $request->input('priority_id'),
            'due_date' => $request->input('due_date'),
        ]));

        return response()->json(TaskResource::make($task), 201);
    }

    public function show($id)
    {
        // Logic to display a specific task
    }

    public function update(Request $request, $id)
    {
        // Logic to update a specific task
    }

    public function destroy($organizationId, $projectId, $userStoryId, $taskId)
    {
        $task = Task::findOrFail($taskId);

        $task->delete();
        return response()->json(null, 204);
    }

    public function assignUser(Request $request, $taskId, $userId)
    {
        // Logic to assign a user to a task
        return response()->json([
            'message' => 'User assigned to task successfully',
            'task' => [
                'id' => $taskId,
                'assigned_user_id' => $userId,
            ]
        ], 200);
    }
}
