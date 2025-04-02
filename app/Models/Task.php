<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
    ];

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function team_members() 
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function user_story()
    {
        return $this->belongsTo(UserStory::class);
    }
}
