<?php

namespace App\Policies;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
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

    public function viewAny(User $user)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->firstOrFail()
            ->can('Get all projects');

        return $checkRole;
    }

    public function view(User $user, Project $project)
    {
        $checkRole = TeamMember::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first()
            ->can('Get project by id');

        return $checkRole;
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }


    public function update(User $user, Project $project)
    {
        $checkRole = TeamMember::where('user_id', $user->user_id)
            ->where('project_id', $project->id)
            ->first()
            ->can('Edit projects');
        return $checkRole;
    }

    public function delete(User $user, Project $project)
    {
        $checkRole = TeamMember::where('user_id', $user->user_id)
            ->where('project_id', $project->id)
            ->first()
            ->can('Delete projects');
        return $checkRole;
    }

    public function assignMember(User $user, Project $project)
    {
        return $user->hasRole('administrator');
    }

    public function updateMember(TeamMember $teamMember, Project $project)
    {
        return $teamMember->hasRole($teamMember, $project, 'administrator');
    }
}
