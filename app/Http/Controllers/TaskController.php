<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTaskToTeamMembersRequest;
use App\Http\Requests\PatchTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\UserStory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, $organizationId, $projectId, $userStoryId)
    {
        if ($request->user()->cannot('viewAny', [Task::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $tasks = Task::where('user_story_id', $userStoryId)
            ->get();

        return response()->json(TaskResource::collection($tasks), 200);
    }

    public function store(Request $request, $organizationId, $projectId, $userStoryId)
    {
        // Logic to store a new task
        if ($request->user()->cannot('store', [Task::class, $projectId])) {
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

        return response()->json([
            'message' => 'task created successfully.',
            'task' => TaskResource::make($task),
        ], 201);
    }

    public function show($id)
    {
        // Logic to display a specific task
    }

    public function update(PatchTaskRequest $request, $organizationId, $projectId, $userStoryId, $taskId)
    {
        if ($request->user()->cannot('update', [Task::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        // Logic to update an existing task
        $validated = $request->validated();

        $task = Task::findOrFail($taskId);

        $task->fill($validated);
        $task->save();

        return response()->json(
            [
                'message' => 'task updated successfully.',
                'task' => TaskResource::make($task),
            ],
            200
        );
    }

    public function destroy(Request $request, $organizationId, $projectId, $userStoryId, $taskId)
    {
        if($request->user()->cannot('delete', [Task::class, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $task = Task::findOrFail($taskId);

        $task->delete();
        return response()->json(null, 204);
    }

    public function assignUsers(AssignTaskToTeamMembersRequest $request, $organizationId, $projectId, $user_story, $taskId)
    {
        // Logic to assign a user to a task

        $tm = TeamMember::where('user_id', $request->user()->id)
            ->where('project_id', $projectId)
            ->firstOrFail();

        if ($request->user()->cannot('Assign tasks to a team member') && $tm->cannot('Assign tasks to a team member')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        $task = Task::findOrFail($taskId);

        $task->team_members()->sync($validated['users']);
        $task->save();

        return response()->json([
            'message' => 'users assigned to task successfully',
        ], 200);
    }

    public function changeStatus(Request $request, $organizationId, $projectId, $userStoryId, $taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($request->user()->cannot('changeStatus', [$task, $projectId])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $task->status_id = $validated['status_id'];
        $task->save();

        return response()->json([
            'message' => 'task status updated successfully',
            'task' => TaskResource::make($task),
        ], 200);
    }
}
