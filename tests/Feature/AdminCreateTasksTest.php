<?php

use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserStorySeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

//uses(RefreshDatabase::class);

test('Admin user can create Task associated with a User Story', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 1;
    $organization_id = 1;

    // Populate the database with test data
    //$this->seed();

    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    $response = $this->post(route('organizations.projects.user_stories.tasks.store', [
        "organization" => $organization_id,
        "project" => $project_id,
        "user_story" => $user_story_id,
    ]), [
        'name' => 'Test Task',
        'description' => 'This is a test task.',
    ]);

    $response->assertStatus(201);

    $this->databaseHas('tasks', [
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'user_story_id' => 1,
    ]);

    $response->assertExactJsonStructure([
        'id',
        'name',
        'description',
        'user_story' => [
            'id',
            'name',
            'description',
            'project_id',
            'user_id',
        ],
        'status' => [
            'id',
            'name',
            'description',
        ],
        'created_at',
        'updated_at',
    ]);
});

test('Admin user cannot create Task with invalid User Story ID', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 999; // Invalid User Story ID
    $organization_id = 1;

    // Populate the database with test data
    //$this->seed();

    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    $response = $this->post(route('organizations.projects.user_stories.tasks.store', [
        "organization" => $organization_id,
        "project" => $project_id,
        "user_story" => $user_story_id,
    ]), [
        'name' => 'Test Task',
        'description' => 'This is a test task.',
    ]);

    $response->assertStatus(422);
});

test('Admin user cannot create Task with missing required fields', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 1;
    $organization_id = 1;

    //$this->seed();

    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    $response = $this->post(route('organizations.projects.user_stories.tasks.store', [
        "organization" => $organization_id,
        "project" => $project_id,
        "user_story" => $user_story_id,
    ]), [
        'name' => '', // Missing name
        'description' => 'This is a test task.',
    ]);

    $response->assertStatus(422);
});
