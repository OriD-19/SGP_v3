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
