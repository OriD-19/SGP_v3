<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Admin can delete a task associated with a User Story', function () {
    $admin = User::factory()->create([
        'first_name' => 'User',
        'last_name' => 'Test',
        'email' => "something@something.com",
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'Test Description',
        'email' => "something@something.com",
    ]);
    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'Test Description', 
        'start_date' => now()->toDateString(),
        'organization_id' => $organization->id,
    ]);
    $userStory = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->toDateString(),
        'project_id' => $project->id,
        'created_by' => $admin->id,
    ]);

    $task = $userStory->tasks()->create([
        'title' => 'Test Task',
        'description' => 'Test Description',
        'user_story_id' => $userStory->id,
        'status_id' => 1,
        'priority_id' => 1,
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $admin->assignRole('administrator');
    $this->actingAs($admin);

    $response = $this->deleteJson(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $userStory->id,
        'task' => $task->id,
    ]));

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('Admin cannot delete a task associated with a User Story with invalid ID', function () {
    $admin = User::factory()->create([
        'first_name' => 'User',
        'last_name' => 'Test',
        'email' => "something@something.com",
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'Test Description',
        'email' => "something@something.com",
    ]);
    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'Test Description', 
        'start_date' => now()->toDateString(),
        'organization_id' => $organization->id,
    ]);
    $userStory = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->toDateString(),
        'project_id' => $project->id,
        'created_by' => $admin->id,
    ]);

    $admin->assignRole('administrator');
    $this->actingAs($admin);

    $response = $this->deleteJson(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $userStory->id,
        'task' => 999,
    ]));

    $response->assertStatus(404);
});