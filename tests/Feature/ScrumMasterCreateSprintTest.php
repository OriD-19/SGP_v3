<?php

use App\Models\Organization;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Scrum Master can create a new Sprint with one or more User Stories', function () {

    // the user stories that will be attached to the newly created sprint

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
    ]);

    $project = Project::factory()->create([
        'project_name' => 'Test Project',
        'organization_id' => $organization->id,
    ]);

    $u1 = UserStory::factory()->create([
        'title' => 'User Story 1',
        'description' => 'Description for User Story 1',
        'project_id' => $project->id,
    ]);

    $u2 = UserStory::factory()->create([
        'title' => 'User Story 2',
        'description' => 'Description for User Story 2',
        'project_id' => $project->id,
    ]);

    $u3 = UserStory::factory()->create([
        'title' => 'User Story 3',
        'description' => 'Description for User Story 3',
        'project_id' => $project->id,
    ]);

    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $scrum_master = TeamMember::factory()
    ->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $scrum_master->assignRole('scrum_master');
    $this->actingAs($user);

    $response = $this->postJson(route('organizations.projects.sprints.store', [
        'organization' => 1,
        'project' => $project->id,
    ]), [
        'duration' => 3, // sprint duration in weeks
        'description' => 'This is a test sprint.',
        'user_stories' => [
            $u1->id,
            $u2->id,
            $u3->id,
        ],
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('sprints', [
        'description' => 'This is a test sprint.',
        'duration' => 3,
        'project_id' => $project->id,
    ]);

    $this->assertDatabaseHas('user_stories', [
        'id' => $u1->id,
        'sprint_id' => $response->json('sprint_id'),
    ]);

    $this->assertDatabaseHas('user_stories', [
        'id' => $u2->id,
        'sprint_id' => $response->json('sprint_id'),
    ]);

    $this->assertDatabaseHas('user_stories', [
        'id' => $u3->id,
        'sprint_id' => $response->json('sprint_id'),
    ]);

});
