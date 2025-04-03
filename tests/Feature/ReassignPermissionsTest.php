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

test("a user with permissions can reassign the tasks to more users", function() {
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

    $task = $user_story->tasks()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'user_story_id' => $user_story->id,
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Assign tasks to a team member');
    $this->actingAs($user);

    $colleague = TeamMember::factory()->create([
        'user_id' => User::factory()->create()->id,
        'project_id' => $project->id,
    ]);

    $task->team_members()->sync([$teamMember->id]);

    $response = $this->postJson(route(
        'tasks.assign',
        [
            'organization' => $organization->id,
            'project' => $project->id,
            'user_story' => $user_story->id,
            'task' => $task->id,
        ]
    ), [
        'users' => [...$task->team_members->pluck('id')->toArray(), $colleague->id],
    ]);

    $response->assertStatus(200);

    $response->assertJson([
        'message' => 'users assigned to task successfully',
    ]);
    $this->assertDatabaseHas('task_team_member', [
        'task_id' => $task->id,
        'team_member_id' => $colleague->id,
    ]);

    $this->assertDatabaseHas('task_team_member', [
        'task_id' => $task->id,
        'team_member_id' => $teamMember->id,
    ]);

    $this->assertDatabaseCount('task_team_member', 2);
});
