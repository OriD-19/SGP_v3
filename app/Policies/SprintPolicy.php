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
        $checkRole = TeamMember::where('user_id', $user->user)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Get sprint by id');

        return $checkRole;
    }

    public function create(TeamMember $teamMember, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $teamMember->user_id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Create sprints');

        return $checkRole;
    }

    public function update(TeamMember $teamMember, Sprint $sprint)
    {
        $checkRole = TeamMember::where('user_id', $teamMember->user_id)
            ->where('sprint_id', $sprint->id)
            ->firstOrFail()->can('Edit sprints');

        return $checkRole;
    }

    public function delete(TeamMember $teamMember, Sprint $sprint)
    {
        $checkRole = TeamMember::where('user_id', $teamMember->user_id)
            ->where('sprint_id', $sprint->id)
            ->firstOrFail()->can('Delete sprints');

        return $checkRole;
    }
}
