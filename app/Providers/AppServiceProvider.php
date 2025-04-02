<?php

namespace App\Providers;

use App\Policies\ProjectPolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\UserStory;
use App\Policies\SprintPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserStoryPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(UserStory::class, UserStoryPolicy::class);
        Gate::policy(Sprint::class, SprintPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
    }

    public function register(): void
    {
        //
    }

}
