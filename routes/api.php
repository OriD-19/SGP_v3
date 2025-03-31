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

    // Rutas protegidas **
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('organizations', function () {
            return response()->json(['message' => 'Organizations accessed successfully'], 200);
        });

        Route::get('projects', function () {
            return response()->json(['message' => 'Projects accessed successfully'], 200);
        });

        Route::get('sprints', function () {
            return response()->json(['message' => 'Sprints accessed successfully'], 200);
        });

        Route::get('user-stories', function () {
            return response()->json(['message' => 'User stories accessed successfully'], 200);
        });

        Route::get('tasks', function () {
            return response()->json(['message' => 'Tasks accessed successfully'], 200);
        });

        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('organizations.projects', ProjectController::class);
        Route::apiResource('organizations.projects.user_stories', UserStoryController::class);
        Route::apiResource('organizations.projects.user_stories.tasks', TaskController::class);
        Route::apiResource('organizations.projects.sprints', SprintController::class);
    });
});
