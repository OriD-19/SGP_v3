<?php

namespace App\Providers;

use App\Policies\ProjectPolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{


    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
    }

    public function register(): void
    {
        //
    }

}
