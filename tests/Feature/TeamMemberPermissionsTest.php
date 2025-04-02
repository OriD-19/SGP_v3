<?php

use App\Models\Organization;
use App\Models\Status;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Team Member has permissions to change the status of assigned task', function () {

    $user = User::factory()->create();
    $user->assignRole('team_member');

    $organization = Organization::factory()->create();
    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);
    $userStory = $project->userStories()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'due_date' => now()->addDays(7),
    ]);
    $task = $userStory->tasks()->create([
        'title' => 'Task 1',
        'description' => 'Description of Task 1',
        'due_date' => now()->addDays(7),
    ]);

    $status = Status::where('status', 'done')->first();

    $this->actingAs($user);

    $url = "/organizations/{$organization->id}/projects/{$project->id}/user_stories/{$userStory->id}/tasks/{$task->id}/changeState";

    $response = $this->postJson($url, [
        'state_id' => $status->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'task status updated successfully',
        ]);

    $task->refresh();
    $this->assertEquals($status->id, $task->status_id);
});
