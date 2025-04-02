<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_name',
        'description',
        'organization_id',
        'status_id',
    ];
 
    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function userStories()
    {
        return $this->hasMany(UserStory::class);
    }
}
