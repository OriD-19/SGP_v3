<?php

use App\Models\Organization;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('A user with permissions can delete a User Story', function () {

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $user = User::factory()->create();

    $user_story = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
        'due_date' => now()->addDays(7),
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Delete user_stories');
    $this->actingAs($user);

    $response = $this->deleteJson(route('organizations.projects.user_stories.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]));

    $response->assertStatus(204);

    $this->assertDatabaseMissing('user_stories', [
        'id' => $user_story->id,
    ]);
});

test("a user with permissions can edit a User Story", function() {

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $user = User::factory()->create();

    $user_story = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'due_date' => now()->addDays(7),
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Edit user_stories');
    $this->actingAs($user);

    $response = $this->putJson(route('organizations.projects.user_stories.update', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]), [
        'title' => 'Updated User Story',
        'description' => 'This is an updated test user story.',
        'due_date' => now()->addDays(10)->toDateString(),
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('user_stories', [
        'id' => $user_story->id,
        'title' => 'Updated User Story',
        'description' => 'This is an updated test user story.',
    ]);

});

test("a user with permissions can create a User Story", function() {
    $organization = Organization::factory()->create();
    
    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $user = User::factory()->create();

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Create user_stories');
    $this->actingAs($user);

    $response = $this->postJson(route('organizations.projects.user_stories.store', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]), [
        'title' => 'New User Story',
        'description' => 'This is a new test user story.',
        'due_date' => now()->addDays(7)->toDateString(),
        'priority_id' => 1,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('user_stories', [
        'title' => 'New User Story',
        'description' => 'This is a new test user story.',
    ]);
});