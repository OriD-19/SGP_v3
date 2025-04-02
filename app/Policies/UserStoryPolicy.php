<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;

class UserStoryPolicy
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

    public function create(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Create user stories');

        return $checkRole;
    }

    public function update(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Edit user stories');

        return $checkRole;
    }

    public function delete(User $user, int $projectId)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $projectId)
            ->firstOrFail()->can('Delete user stories');

        return $checkRole;
    }

}
