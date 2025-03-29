<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /** @use HasFactory<\Database\Factories\StatusFactory> */
    use HasFactory;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function projects() 
    {
        return $this->hasMany(Project::class);
    }
}
