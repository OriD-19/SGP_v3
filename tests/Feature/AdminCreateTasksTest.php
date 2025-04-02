<?php

use App\Models\Organization;
use App\Models\TeamMember;
use App\Models\User;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserStorySeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Admin user can create Task associated with a User Story', function () {

    // Define the IDs for the project and user story
    $user = User::factory()->create();

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'This is a test organization.',
        'email' => "something@something.com",
    ]);

    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $user_story = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'This is a test user story.',
        'due_date' => now()->addDays(7)->toDateString(),
        'project_id' => $project->id,
    ]);

    $user->assignRole('administrator');
    $this->actingAs($user);

    $response = $this->postJson(route('organizations.projects.user_stories.tasks.store', [
        "organization" => $organization->id,
        "project" => $project->id,
        "user_story" => $user_story->id,
    ]), [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => now()->addDays(7)->toDateString(),
        'status_id' => 1,
        'priority_id' => 1,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'user_story_id' => 1,
    ]);
});

test('Admin user cannot create Task with invalid User Story ID', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 999; // Invalid User Story ID
    $organization_id = 1;

    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    $response = $this->postJson(route('organizations.projects.user_stories.tasks.store', [
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

    $response = $this->postJson(route('organizations.projects.user_stories.tasks.store', [
        "organization" => $organization_id,
        "project" => $project_id,
        "user_story" => $user_story_id,
    ]), [
        'name' => '', // Missing name
        'description' => 'This is a test task.',
    ]);

    $response->assertStatus(422);
});
