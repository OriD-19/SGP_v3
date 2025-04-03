<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create projects', function () {
    $admin = createAdminUser();

    $project = Project::factory()->make();

    $this->actingAs($admin)
        ->postJson(route('organizations.projects.store', [
            'organization' => 1,
        ]), [
            'project_name' => $project->project_name,
            'description' => $project->description,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
        ])
        ->assertStatus(201);
});

test('admin can update projects', function () {
    $admin = createAdminUser();
    $project = Project::factory()->create();

    $updatedData = [
        'project_name' => 'Updated Project Name',
        'description' => 'Updated Project Description',
    ];

    $this->actingAs($admin)
        ->putJson(route('organizations.projects.update', [
            'organization' => 1,
            'project' => $project->id,
        ]), $updatedData)
        ->assertStatus(200);

    $project->refresh();
    expect($project->project_name)->toBe('Updated Project Name');
    expect($project->description)->toBe('Updated Project Description');
});

test('admin can delete projects', function () {
    $admin = createAdminUser();
    $project = Project::factory()->create();

    $this->actingAs($admin)
        ->deleteJson(route('organizations.projects.destroy', [
            'organization' => 1,
            'project' => $project->id,
        ]))
        ->assertStatus(200);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id
    ]);
});

test("admin can assign users to projects", function () {
    $admin = createAdminUser();
    $project = Project::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($admin)
        ->postJson(route('organizations.projects.team_members.store', [
            'organization' => 1,
            'project' => $project->id,
        ]), [
            'user_id' => $user->id,
            'role' => 'team_member',
        ])
        ->assertStatus(201);

    $this->assertDatabaseHas('team_members', [
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
});

test("admin can reassign roles in projects", function () {
    $admin = createAdminUser();
    $project = Project::factory()->create();

    $user = User::factory()->create();

    $this->actingAs($admin);

    $teamMember = TeamMember::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
    $teamMember->assignRole('team_member');

    $this->actingAs($admin)
        ->patchJson(route('organizations.projects.team_members.update', [
            'organization' => 1,
            'project' => $project->id,
            'team_member' => $teamMember->id,
        ]), [
            'role' => 'scrum_master', // changing to scrum_master
        ])
        ->assertStatus(200);

    $teamMember = TeamMember::where('user_id', $user->id)
        ->where('project_id', $project->id)
        ->firstOrFail();
    
    $this->assertDatabaseHas('team_members', [
        'id' => $teamMember->id,
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);

    $this->assertTrue($teamMember->hasRole('scrum_master'));
});

test("user with permissions can create projects", function () {
    $organization = Organization::factory()->create();

    $project = Project::factory()->make([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user->givePermissionTo('Create projects');
    $this->actingAs($user);

    $response = $this->post(route('organizations.projects.store', [
        'organization' => $organization->id,
    ]), [
        'project_name' => $project->project_name,
        'description' => $project->description,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'project created successfully',
        ]);
});

test('user with permissions can edit projects', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user->givePermissionTo('Edit projects');
    $this->actingAs($user);

    $response = $this->patch(route('organizations.projects.update', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]), [
        'project_name' => 'Updated Project Name',
        'description' => 'Updated Project Description',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(30)->toDateString(),
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'project updated successfully',
        ]);
});

test('user with permissions can delete projects', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $user->givePermissionTo('Delete projects');
    $this->actingAs($user);

    $response = $this->delete(route('organizations.projects.destroy', [
        'organization' => $organization->id,
        'project' => $project->id,
    ]));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'project deleted successfully',
        ]);
    
    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});