<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Team Member has permissions to change the status of assigned task', function () {

    $user = User::factory()->create();

    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
        'organization_id' => $organization->id,
    ]);
    $userStory = UserStory::factory()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'due_date' => now()->addDays(7),
        'project_id' => $project->id,
    ]);

    $task = Task::factory()->create([
        'title' => 'Task 1',
        'description' => 'Description of Task 1',
        'due_date' => now()->addDays(7),
        'user_story_id' => $userStory->id,
    ]);

    $status = Status::where('status', 'done')->first();

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->tasks()->attach($task->id);

    $teamMember->assignRole('team_member');
    $this->actingAs($user);

    $response = $this->patch(route('tasks.changeStatus', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $userStory->id,
        'task' => $task->id,
    ]), [
        'status_id' => $status->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'task status updated successfully',
        ]);

    $task->refresh();
    $this->assertEquals($status->id, $task->status_id);
});

test('Team member has permissions to visualize all user stories from a project', function() {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create();

    $userStory = UserStory::factory()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'due_date' => now()->addDays(7),
        'project_id' => $project->id,
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->assignRole('team_member');
    $this->actingAs($user);

    $response = $this->get(route('organizations.projects.user_stories.index', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]));

    $response->assertStatus(200);
});