<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Admin can edit Tasks associated to a User Story (PUT)', function () {

    $admin = User::where('first_name', 'Admin')->first();
    $this->actingAs($admin);

    $response = $this->putJson(route('organizations.projects.user_stories.tasks.update', [
        'organization' => 1,
        'project' => 1,
        'user_story' => 1,
        'task' => 1,
    ]), [
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tasks', [
        'id' => 1,
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ]);

    $response->assertExactJsonStructure([
        'id',
        'title',
        'description',
        'user_story' => [
            'id',
            'title',
            'description',
            'project_id',
            'user_id',
        ],
        'status' => [
            'id',
            'status',
        ],
        'created_at',
        'updated_at',
    ])
    ->assertJsonFragment([
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ])->assertJsonMissing([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
    ]);
});

test('Admin can edit Tasks associated to a User Story (PATCH)', function () {

    $admin = User::where('first_name', 'Admin')->first();
    $this->actingAs($admin);

    $response = $this->patchJson(route('organizations.projects.user_stories.tasks.update', [
        'organization' => 1,
        'project' => 1,
        'user_story' => 1,
        'task' => 1,
    ]), [
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('tasks', [
        'id' => 1,
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ]);
    $response->assertExactJsonStructure([
        'id',
        'title',
        'description',
        'user_story' => [
            'id',
            'title',
            'description',
            'project_id',
            'user_id',
        ],
        'status' => [
            'id',
            'status',
        ],
        'created_at',
        'updated_at',
    ])
    ->assertJsonFragment([
        'title' => 'Updated Task Name',
        'description' => 'Updated Task Description',
    ])->assertJsonMissing([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
    ]);
});