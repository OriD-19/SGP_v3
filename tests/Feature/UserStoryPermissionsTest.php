<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('A user with permissions can delete a User Story', function () {

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);

    $user = User::factory()->create();

    $user->givePermissionTo('Delete user_stories');
    $this->actingAs($user);

    $user_story = $project->userStories()->create([
        'name' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response = $this->deleteJson(route('organizations.projects.user_stories.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]));

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'User Story deleted successfully.',
    ]);

    $this->assertDatabaseMissing('user_stories', [
        'id' => $user_story->id,
    ]);
});
