<?php

use App\Models\Organization;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Product Owner has permission to get projects', function () {

    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $response = $this->getJson(route('organizations.projects.index', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]));

    $response->assertStatus(200);
    $response->assertJsonCount(1);
});

test('Product Owner has permission to get sprints', function () {

    $user = User::factory()->create();

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $secondary_project = $organization->projects()->create([
        'project_name' => 'Project 2',
        'description' => 'Description of Project 2',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $sprint = $project->sprints()->create([
        'duration' => 2,
        'description' => 'Sprint 1',
        'start_date' => now(),
        'active' => false,
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $response = $this->getJson(route('organizations.projects.sprints.index', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => $sprint->id,
                    'duration' => 2,
                    'description' => 'Sprint 1',
                    // Add other sprint attributes as needed
                ],
            ],
        ]);

    // return only the projects of which the product owner is a team member
    $response->assertJsonCount(1);
});

test('Product Owner has permission to get user stories', function () {

    $user = User::factory()->create();


    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $user_story = $project->userStories()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'due_date' => now()->addDays(7),
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $response = $this->getJson(route('organizations.projects.user_stories.index', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'id' => $user_story->id,
                    'title' => 'User Story 1',
                    'description' => 'Description of User Story 1',
                ],
            ],
        ]);

    $response->assertJsonCount(1);
});

test('Product Owner has permission to get tasks', function () {

    $user = User::factory()->create();

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $user_story = $project->userStories()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'due_date' => now()->addDays(7),
    ]);

    $task = $user_story->tasks()->create([
        'title' => 'Task 1',
        'description' => 'Description of Task 1',
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $response = $this->getJson(route('organizations.projects.user_stories.tasks.index', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $user_story->id,
    ]));

    $response->assertStatus(200);
    $response->assertJsonCount(1);
});

test('Product owner can change a user story priority', function() {
    $user = User::factory()->create();

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $user_story = $project->userStories()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'priority_id' => 1,
        'due_date' => now()->addDays(7),
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $url = "api/SGP/v1/organizations/{$organization->id}/projects/{$project->id}/user_stories/{$user_story->id}/changePriority";
    $response = $this->patchJson($url, [
        'priority_id' => 3, // Assuming you have a priority with ID 1
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment([
            'message' => 'user story priority updated successfully',
        ]);  

    $user_story->refresh();
    $this->assertEquals(3, $user_story->priority_id);
});

test('Product Owner has permissions to change user story from sprint', function() {
    $user = User::factory()->create();

    $organization = Organization::factory()->create();

    $project = $organization->projects()->create([
        'project_name' => 'Project 1',
        'description' => 'Description of Project 1',
    ]);

    $team_member = TeamMember::factory()->create([
        'project_id' => $project->id,
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $sprint = $project->sprints()->create([
        'duration' => 2,
        'description' => 'Sprint 1',
        'start_date' => now(),
        'active' => false,
    ]);

    $sprint2 = $project->sprints()->create([
        'duration' => 2,
        'description' => 'Sprint 2',
        'start_date' => now(),
        'active' => false,
    ]);

    $user_story = $project->userStories()->create([
        'title' => 'User Story 1',
        'description' => 'Description of User Story 1',
        'sprint_id' => 2,
        'due_date' => now()->addDays(7),
    ]);

    $team_member->assignRole('product_owner');
    $this->actingAs($user);

    $url = "api/SGP/v1/organizations/{$organization->id}/projects/{$project->id}/user_stories/{$user_story->id}/changeSprint";
    $response = $this->patchJson($url, [
        'sprint_id' => $sprint->id, // Assuming you have a sprint with ID 1
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'user story sprint updated successfully',
        ]);  

    $user_story->refresh();
    $this->assertEquals($sprint->id, $user_story->sprint_id);
});