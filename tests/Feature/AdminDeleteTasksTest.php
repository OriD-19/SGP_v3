<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Admin can delete a task associated with a User Story', function () {
    $admin = User::where('first_name', 'Admin')->first();
    $this->actingAs($admin);

    $response = $this->delete(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => 1,
        'project' => 1,
        'user_story' => 1,
        'task' => 1,
    ]));

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', [
        'id' => 1,
    ]);
});

test('Admin cannot delete a task associated with a User Story with invalid ID', function () {
    $admin = User::where('first_name', 'Admin')->first();
    $this->actingAs($admin);

    $response = $this->delete(route('organizations.projects.user_stories.tasks.destroy', [
        'organization' => 1,
        'project' => 1,
        'user_story' => 1,
        'task' => 999, // Invalid task ID
    ]));

    // Assert that the task was not found
    $response->assertStatus(404);
});