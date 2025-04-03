<?php

use App\Models\Organization;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserStory;

test('A user with permissions can assign a Task to a Team Member', function () {

    $user = User::factory()->create();

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'This is a test project.',
        'organization_id' => $organization->id,
    ]);

    $user_story = UserStory::factory()->create([
        'title' => 'Test User Story',
        'description' => 'This is a test user story.',
        'project_id' => $project->id,
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'user_story_id' => $user_story->id,
    ]);

    $team_member = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $team_member->givePermissionTo('Assign tasks to a team member');

    $colleague = TeamMember::factory()->create([
        'user_id' => User::factory()->create()->id,
        'project_id' => $project->id,
    ]);

    $this->actingAs($user);
    $response = $this->postJson(route(
        'tasks.assign',
        [
            'organization' => $organization->id,
            'project' => $project->id,
            'user_story' => $user_story->id,
            'task' => $task->id,
        ]
    ), [
        'users' => [$colleague->id],
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'users assigned to task successfully',
    ]);

    $team_member->refresh();
    $this->assertContains($task->id, $colleague->tasks()->pluck('id')->toArray());
});
