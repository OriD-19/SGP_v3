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
}
