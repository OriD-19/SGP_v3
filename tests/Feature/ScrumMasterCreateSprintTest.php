<?php

use App\Models\Role;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Scrum Master can create a new Sprint with one or more User Stories', function () {

    // the user stories that will be attached to the newly created sprint
    $user_stories_ids = [1, 2, 3];

    $scrum_master_role = Role::where('role', 'scrum_master')->first();
    $scrum_master = TeamMember::factory()
    ->create([
        'user_id' => 1,
        'project_id' => 1,
        'role_id' => $scrum_master_role->id,
    ]);

    $this->actingAs($scrum_master->user);

    $response = $this->postJson(route('organizations.projects.sprints.store', [
        'organization' => 1,
        'project' => 1,
    ]), [
        'title' => 'New Sprint',
        'duration' => 3, // sprint duration in weeks
        'description' => 'This is a test sprint.',
        'user_stories' => $user_stories_ids,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('sprints', [
        'title' => 'New Sprint',
        'description' => 'This is a test sprint.',
        'duration' => 3,
        'project_id' => 1,
    ]);

    $this->assertDatabaseHas('user_story', [
        'id' => 1,
        'sprint_id' => $response->json('id'),
    ]);

    $this->assertDatabaseHas('user_story', [
        'id' => 2,
        'sprint_id' => $response->json('id'),
    ]);

    $this->assertDatabaseHas('user_story', [
        'id' => 3,
        'sprint_id' => $response->json('id'),
    ]);
    $response->assertExactJsonStructure([
        'id',
        'title',
        'description',
        'duration',
        'project_id',
        'user_stories' => [
            '*' => [
                'id',
                'title',
                'description',
                'project_id',
                'sprint_id',
            ],
        ],
        'created_at',
        'updated_at',
    ])->assertJsonFragment([
        'title' => 'New Sprint',
        'description' => 'This is a test sprint.',
    ]);
});
