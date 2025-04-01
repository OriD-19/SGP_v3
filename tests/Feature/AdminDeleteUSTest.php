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

test('Test admin permissions for deleteing a User Story', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 1;
    $organization_id = 1;

    // Login as the admin user
    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    $organization = Organization::where('id', $organization_id)->first();
    $project = Project::where('id', $project_id)->first();
    $user_story = UserStory::where('id', $user_story_id)->first();

    $this->assertNotNull($organization);
    $this->assertNotNull($project);
    $this->assertNotNull($user_story);

    $this->assertEquals($organization->id, $user->organization_id);
    $this->assertEquals($project->id, $user_story->project_id);

    // Attempt to delete the user story
    $response = $this->deleteJson(route(
        'organizations.projects.user_stories.destroy',
        [
            'organization' => $organization_id,
            'project' => $project_id,
            'user_story' => $user_story_id
        ]
    ));

    // Assert that the user story was deleted successfully
    $this->assertDatabaseMissing('user_stories', [
        'id' => $user_story_id,
    ]);

    $response->assertStatus(204);
});


test('Test admin permissions for deleting a User Story with invalid ID', function () {

    // Define the IDs for the project and user story
    $project_id = 1;
    $user_story_id = 999; // Invalid user story ID
    $organization_id = 1;

    // Login as the admin user
    $user = User::where('first_name', 'Admin')->first();
    $this->actingAs($user);

    // Attempt to delete the user story
    $response = $this->deleteJson(route(
        'organizations.projects.user_stories.destroy',
        [
            'organization' => $organization_id,
            'project' => $project_id,
            'user_story' => $user_story_id
        ]
    ));

    // Assert that the user story was not found
    $response->assertStatus(404);
});