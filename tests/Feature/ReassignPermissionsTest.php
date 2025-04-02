<?php

use App\Models\Organization;
use App\Models\TeamMember;
use App\Models\User;

test('A user with permissions can assign a Task to a Team Member', function () {

    $user = User::factory()->create();
    $user->givePermissionTo('Assign tasks to team members');

    $this->actingAs($user);

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'name' => 'Test Project',
        'description' => 'This is a test project.',
    ]);
    $user_story = $project->userStories()->create([
        'name' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $task = $user_story->tasks()->create([
        'name' => 'Test Task',
        'description' => 'This is a test task.',
        'user_story_id' => $user_story->id,
    ]);

    $team_member = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $url = "api/SGP/v1/organizations/{$organization->id}/projects/{$project->id}/user_stories/{$user_story->id}/tasks/{$task->id}/assign";

    $response = $this->postJson($url, [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
        'task' => $task->id,
    ], [
        'user_id' => $team_member->id,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Task assigned successfully.',
    ]);

    $team_member->refresh();
    $this->assertContains($task->id(), $team_member->tasks());
});
