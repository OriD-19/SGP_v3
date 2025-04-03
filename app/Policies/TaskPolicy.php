<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;

class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $ability): bool|null
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Get tasks');

        return $checkRole;
    }

    public function view(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Get task by id');

        return $checkRole;
    }

    public function store(User $user, int $projectId)
    {

        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Create tasks');

        return $checkRole;
    }

    public function update(User $user, Task $task, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Edit tasks');

        return $checkRole;
    }

    public function delete(User $user, Task $task, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Delete tasks');

        return $checkRole;
    }

    public function assign(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Assign tasks to a team member');

        return $checkRole;
    }
}
