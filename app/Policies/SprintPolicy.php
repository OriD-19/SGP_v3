<?php

namespace App\Policies;

use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;

class SprintPolicy
{
    public function create(TeamMember $teamMember, Sprint $sprint)
    {
        return $this->hasRole($teamMember, $sprint, 'administrator');
    }

    public function update(TeamMember $teamMember, Sprint $sprint)
    {
        return $this->hasRole($teamMember, $sprint, 'administrator');
    }

    public function delete(TeamMember $teamMember, Sprint $sprint)
    {
        return $this->hasRole($teamMember, $sprint, 'administrator');
    }

    private function hasRole(TeamMember $teamMember, Sprint $sprint, string $roleName)
    {
        return TeamMember::where('user_id', $teamMember->user_id)
            ->where('sprint_id', $sprint->id)
            ->whereHas('role', function ($query) use ($roleName) {
                $query->where('role', $roleName);
            })
            ->exists();
    }
}
