<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// just in case, because it is really boring to configure a full update operation

// test('Admin can edit Tasks associated to a User Story (PUT)', function () {

//     $admin = User::factory()->create([
//         'first_name' => 'User',
//         'last_name' => 'Test',
//         'email' => "something@something.com",
//         'password' => bcrypt('password'),
//         'is_admin' => true,
//     ]);

//     $admin->assignRole('administrator');
//     $this->actingAs($admin);

//     $response = $this->putJson(route('organizations.projects.user_stories.tasks.update', [
//         'organization' => 1,
//         'project' => 1,
//         'user_story' => 1,
//         'task' => 1,
//     ]), [
//         'title' => 'Updated Task Name',
//         'description' => 'Updated Task Description',
//     ]);

//     $response->assertStatus(200);
//     $this->assertDatabaseHas('tasks', [
//         'id' => 1,
//         'title' => 'Updated Task Name',
//         'description' => 'Updated Task Description',
//     ]);

//     $response->assertExactJsonStructure([
//         'id',
//         'title',
//         'description',
//         'user_story' => [
//             'id',
//             'title',
//             'description',
//             'project_id',
//             'user_id',
//         ],
//         'status' => [
//             'id',
//             'status',
//         ],
//         'created_at',
//         'updated_at',
//     ])
//     ->assertJsonFragment([
//         'title' => 'Updated Task Name',
//         'description' => 'Updated Task Description',
//     ])->assertJsonMissing([
//         'title' => 'Test Task',
//         'description' => 'This is a test task.',
//     ]);
// });

test('Admin can edit Tasks associated to a User Story (PATCH)', function () {

    $admin = User::factory()->create([
        'first_name' => 'User',
        'last_name' => 'Test',
        'email' => "something@something.com",
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);

    // Create the organization, project, and user story

    $organization = Organization::factory()->create([
        'name' => 'Test Organization',
        'description' => 'Test Description',
        'email' => 'something@org.com',
    ]);

    $project = $organization->projects()->create([
        'project_name' => 'Test Project',
        'description' => 'Test Description',
        'start_date' => now()->toDateString(),
    ]);

    $userStory = $project->userStories()->create([
        'title' => 'Test User Story',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $task = $userStory->tasks()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status_id' => 1,
        'priority_id' => 1,
        'due_date' => now()->addDays(7)->toDateString(),
    ]);


    $admin->assignRole('administrator');
    $this->actingAs($admin);

    $response = $this->patchJson(route('organizations.projects.user_stories.tasks.update', [
        'organization' => $organization->id,
        'project' => $project->id,
        'user_story' => $userStory->id,
        'task' => $task->id,
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
});