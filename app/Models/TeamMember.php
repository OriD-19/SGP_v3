<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TeamMember extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\TeamMemberFactory> */
    use HasFactory, HasRoles;


    protected $fillable = [
        'user_id',
        'project_id',
        'organization_id',
    ];

    public function getDefaultGuardName()
    {
        return 'web';
    }

    public $guard_name = 'web';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks() {
        return $this->belongsToMany(Task::class);
    }
}
