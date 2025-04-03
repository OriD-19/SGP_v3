<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('A user with permissions can create a task', function () {

    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create();

    $user_story = UserStory::factory()->create([
        'title' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $team_member = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $team_member->givePermissionTo('Create tasks');
    $due_date = now()->addDays(7)->toDateString();

    $this->actingAs($user);
    $response = $this->postJson(route('organizations.projects.user_stories.tasks.store', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]), [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => $due_date,
        'status_id' => 1, // Assuming you have a status with ID 1
        'priority_id' => 1, // Assuming you have a priority with ID 1
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment([
        'message' => 'task created successfully.',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => $due_date,
        'user_story_id' => $user_story->id,
    ]);
});

test('A user with permissions can update a task', function () {

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
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $task = $user_story->tasks()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => now()->addDays(7),
    ]);

    $new_due_date = now()->addDays(14)->toDateString();

    $team_member = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $team_member->givePermissionTo('Edit tasks');
    $this->actingAs($user);
    $response = $this->patchJson(route('organizations.projects.user_stories.tasks.update', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
        'task' => $task->id,
    ]), [
        'title' => 'Updated Task',
        'due_date' => $new_due_date,
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'message' => 'task updated successfully.',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Updated Task',
        'due_date' => $new_due_date,
    ]);
});

test('A user with permissions can delete a task', function () {

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
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $task = $user_story->tasks()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => now()->addDays(7),
    ]);

    $team_member = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $team_member->givePermissionTo('Delete tasks');
    $this->actingAs($user);
    $response = $this->deleteJson(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
        'task' => $task->id,
    ]));

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});