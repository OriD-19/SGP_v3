<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Models\UserStory;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserStorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('Test admin permissions for deleting a User Story', function () {

    // Login as the admin user
    $user = User::factory()->create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'something@somthing.com',
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);

    // Create the organization, project, and user story
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'Test Description',
        'email' => 'something@org.com',
    ]);

    $project = Project::factory()->create([
        'project_name' => 'Test Project',
        'description' => 'Test Description',
        'start_date' => now()->toDateString(),
        'organization_id' => $organization->id,
    ]);

    $user_story = UserStory::factory()->create([
        'title' => 'Test User Story',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->toDateString(),
        'project_id' => $project->id,
    ]);

    $user->assignRole('administrator');
    $this->actingAs($user);

    // Attempt to delete the user story
    $response = $this->deleteJson(route(
        'organizations.projects.user_stories.destroy',
        [
            'organization' => $organization->id,
            'project' => $project->id,
            'user_story' => $user_story->id
        ]
    ));

    // Assert that the user story was deleted successfully
    $this->assertDatabaseMissing('user_stories', [
        'id' => $user_story->id,
    ]);

    $response->assertStatus(204);
});


test('Test admin permissions for deleting a User Story with invalid ID', function () {

    // Login as the admin user
    $user = User::factory()->create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'something@somthing.com',
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);

    // Create the organization, project, and user story
    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'Test Description',
        'email' => 'something@org.com',
    ]);

    $project = Project::factory()->create([
        'project_name' => 'Test Project',
        'description' => 'Test Description',
        'start_date' => now()->toDateString(),
        'organization_id' => $organization->id,
    ]);

    $user_story = UserStory::factory()->create([
        'title' => 'Test User Story',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->toDateString(),
        'project_id' => $project->id,
    ]);

    $user->assignRole('administrator');
    $this->actingAs($user);

    // Attempt to delete the user story
    $response = $this->deleteJson(route(
        'organizations.projects.user_stories.destroy',
        [
            'organization' => $organization->id,
            'project' => $project->id,
            'user_story' => 999 //invalid ID
        ]
    ));

    $response->assertStatus(404);
});