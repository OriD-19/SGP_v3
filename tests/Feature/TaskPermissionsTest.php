<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('A user with permissions can create a task', function () {

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo('Create tasks');
    $this->actingAs($user);

    $user_story = $project->userStories()->create([
        'name' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $due_date = now()->addDays(7);

    $response = $this->postJson(route('organizations.projects.user_stories.tasks.store', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]), [
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => $due_date,
    ]);

    $response->assertStatus(201);
    $response->assertJson([
        'message' => 'task created successfully.',
    ]);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => $due_date,
        'user_story_id' => $user_story->id,
    ]);
});

test('A user with permissions can update a task', function () {

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo('Edit tasks');
    $this->actingAs($user);

    $user_story = $project->userStories()->create([
        'name' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $task = $user_story->tasks()->create([
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => now()->addDays(7),
    ]);

    $new_due_date = now()->addDays(14);

    $response = $this->putJson(route('organizations.projects.user_stories.tasks.update', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
        'task' => $task->id,
    ]), [
        'name' => 'Updated Task',
        'description' => 'This is an updated task.',
        'due_date' => $new_due_date,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'task updated successfully.',
    ]);

    $this->assertDatabaseHas('tasks', [
        'name' => 'Updated Task',
        'description' => 'This is an updated task.',
        'due_date' => $new_due_date,
    ]);
});

test('A user with permissions can delete a task', function () {

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo('Delete tasks');
    $this->actingAs($user);

    $user_story = $project->userStories()->create([
        'name' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $task = $user_story->tasks()->create([
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'due_date' => now()->addDays(7),
    ]);

    $response = $this->deleteJson(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
        'task' => $task->id,
    ]));

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'task deleted successfully.',
    ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});