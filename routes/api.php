<?php

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserStoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('organizations', OrganizationController::class);

    Route::apiResource('organizations.projects', ProjectController::class);

    Route::apiResource('organizations.projects.user_stories', UserStoryController::class);

    Route::apiResource('organizations.projects.user_stories.tasks', TaskController::class);
});
