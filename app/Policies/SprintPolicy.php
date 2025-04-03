<?php

namespace App\Policies;

use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;

class SprintPolicy
{

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
            ->firstOrFail()->can('Get all sprints');

        return $checkRole;
    }

    public function view(User $user, int $projectId)
    {

        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail();

        return $checkRole->can('Get all sprints');
    }

    public function create(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Create sprints');

        return $checkRole;
    }

    public function update(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Edit sprints');

        return $checkRole;
    }

    public function delete(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Delete sprints');

        return $checkRole;
    }
}
