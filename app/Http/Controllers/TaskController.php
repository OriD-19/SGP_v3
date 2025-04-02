<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\UserStory;
use Illuminate\Support\Facades\Gate;
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
        if ($request->user()->cannot('store', Task::class, $projectId)) {
            abort(403, 'Unauthorized action.');
        }

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

    public function update(PatchTaskRequest $request, $organizationId, $projectId, $userStoryId, $taskId)
    {
        if ($request->user()->cannot('update', Task::class, $projectId)) {
            abort(403, 'Unauthorized action.');
        }

        // Logic to update an existing task
        $validated = $request->validated();

        $task = Task::findOrFail($taskId);

        $task->fill($validated);
        $task->save();

        return response()->json(TaskResource::make($task), 200);
    }

    public function destroy(Request $request, $organizationId, $projectId, $userStoryId, $taskId)
    {
        if($request->user()->cannot('delete', Task::class, $projectId)) {
            abort(403, 'Unauthorized action.');
        }

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
