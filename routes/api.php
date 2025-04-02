<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'SGP/v1'], function () {
    // Rutas públicas
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);


    // Rutas protegidas **
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        Route::apiResource('organizations', OrganizationController::class);

        Route::apiResource('organizations.projects', ProjectController::class);

        Route::apiResource('organizations.projects.user_stories', UserStoryController::class);

        Route::post('organizations/{organization}/projects/{project}/user_stories/{user_story}/tasks/{task}/assign', [TaskController::class, 'assignUser']);
        Route::post('organizations/{organization}/projects/{project}/user_stories/{user_story}/tasks/{task}/changeState', [TaskController::class, 'unassignUser']);
        Route::apiResource('organizations.projects.user_stories.tasks', TaskController::class);

        Route::apiResource('organizations.projects.sprints', SprintController::class);


    });
});
