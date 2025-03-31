<?php

namespace App\Policies;
use App\Models\TeamMember;
use App\Models\Project;

class ProjectPolicy
{
    public function create(TeamMember $teamMember, Project $project)
    {
        return $this->hasRole($teamMember, $project, 'administrator');
    }

    public function update(TeamMember $teamMember, Project $project)
    {
        return $this->hasRole($teamMember, $project, 'administrator');
    }

    public function delete(TeamMember $teamMember, Project $project)
    {
        return $this->hasRole($teamMember, $project, 'administrator');
    }

    public function assignMember(TeamMember $teamMember, Project $project)
    {
        return $this->hasRole($teamMember, $project, 'administrator');
    }

    public function updateMember(TeamMember $teamMember, Project $project)
    {
        return $this->hasRole($teamMember, $project, 'administrator');
    }

    private function hasRole(TeamMember $teamMember, Project $project, string $roleName)
    {
        return TeamMember::where('user_id', $teamMember->user_id)
            ->where('project_id', $project->id)
            ->whereHas('role', function ($query) use ($roleName) {
                $query->where('role', $roleName);
            })
            ->exists();
    }
}
