<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user with permissions can create a sprint', function () {

    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $sprint = Sprint::factory()->make([
        'project_id' => $project->id,
        'start_date' => now()->toDateString(),
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Create sprints');

    $this->actingAs($user)
        ->postJson(route('organizations.projects.sprints.store', [
            'organization' => 1,
            'project' => $project->id,
        ]), [
            'duration' => $sprint->duration,
            'description' => $sprint->description,
            'start_date' => $sprint->start_date,
        ])
        ->assertStatus(201);

    $this->assertDatabaseHas('sprints', [
        'description' => $sprint->description,
        'duration' => $sprint->duration,
        'start_date' => $sprint->start_date,
        'project_id' => $project->id,
    ]);

});

test("a user with permissions can edit a sprint", function() {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $sprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'start_date' => now()->toDateString(),
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Edit sprints');

    $this->actingAs($user)
        ->putJson(route('organizations.projects.sprints.update', [
            'organization' => 1,
            'project' => $project->id,
            'sprint' => $sprint->id,
        ]), [
            'duration' => 10,
            'description' => 'Updated description',
            'start_date' => now()->addDays(5)->toDateString(),
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('sprints', [
        'description' => 'Updated description',
        'duration' => 10,
        'start_date' => now()->addDays(5)->toDateString(),
        'project_id' => $project->id,
    ]);
});

test("a user with permissions can delete a sprint", function() {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $sprint = Sprint::factory()->create([
        'project_id' => $project->id,
        'start_date' => now()->toDateString(),
    ]);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $teamMember->givePermissionTo('Delete sprints');

    $this->actingAs($user)
        ->deleteJson(route('organizations.projects.sprints.destroy', [
            'organization' => 1,
            'project' => $project->id,
            'sprint' => $sprint->id,
        ]))
        ->assertStatus(200);

    $this->assertDatabaseMissing('sprints', [
        'id' => $sprint->id,
    ]);
});