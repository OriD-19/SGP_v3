<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;


class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
    ];

    public function team_members()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function permissions()
    {
        // Relación muchos a muchos con la tabla 'permissions'
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}
