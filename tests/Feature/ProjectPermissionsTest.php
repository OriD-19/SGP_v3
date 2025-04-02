<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create projects', function () {
    $admin = createAdminUser();
    $project = Project::factory()->make();

    $this->actingAs($admin)
        ->postJson(route('organizations.projects.store', [
            'organization' => 1,
        ]), $project->toArray())
        ->assertStatus(201);
});

test('admin can update projects', function () {
    $admin = createAdminUser();
    $project = Project::factory()->create();

    $updatedData = [
        'name' => 'Updated Project Name',
        'description' => 'Updated Project Description',
    ];

    $this->actingAs($admin)
        ->putJson(route('organizations.projects.update', [
            'organization' => 1,
            'project' => $project->id,
        ]), $updatedData)
        ->assertStatus(200);

    $project->refresh();
    expect($project->name)->toBe('Updated Project Name');
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
