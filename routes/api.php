<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'SGP/v1'], function () {
    // Rutas públicas
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);


    // Rutas protegidas **
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('organizations.projects', ProjectController::class);
        Route::apiResource('organizations.projects.team_members', TeamMemberController::class);

        Route::patch('organizations/{organization}/projects/{project}/user_stories/{user_story}/changePriority', [UserStoryController::class, 'changePriority']);
        Route::patch('organizations/{organization}/projects/{project}/user_stories/{user_story}/changeSprint', [UserStoryController::class, 'changeSprint']);
        Route::apiResource('organizations.projects.user_stories', UserStoryController::class);

        Route::post('organizations/{organization}/projects/{project}/user_stories/{user_story}/tasks/{task}/assign', [TaskController::class, 'assignUsers'])->name('tasks.assign');
        Route::patch('organizations/{organization}/projects/{project}/user_stories/{user_story}/tasks/{task}/changeStatus', [TaskController::class, 'changeStatus'])->name('tasks.changeStatus');
        Route::apiResource('organizations.projects.user_stories.tasks', TaskController::class);

        Route::apiResource('organizations.projects.sprints', SprintController::class);


    });
});
