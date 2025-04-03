<?php

use App\Models\Sprint;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAdminUser()
{
    $user = User::factory()->create([
        'first_name' => 'Admin User',
        'last_name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => bcrypt('password'),
        'organization_id' => 1,
        'is_admin' => true,
    ]);

    $user->assignRole('administrator');
    return $user;
}

test('admin can create sprints', function () {
    $admin = User::factory()->create();
    $sprint = Sprint::factory()->make([
        'start_date' => now()->toDateString(),
    ]);

    $admin->assignRole('administrator');
    $this->actingAs($admin)
        ->postJson(route('organizations.projects.sprints.store', [
            'organization' => 1,
            'project' => 1,
        ]), $sprint->toArray())
        ->assertStatus(201);

    $this->assertDatabaseHas('sprints', [
            'description' => $sprint->description,
            'duration' => $sprint->duration,
        ]);
});

test('admin can update sprints', function () {
    $admin = createAdminUser();
    $sprint = Sprint::factory()->create();

    $updatedData = [
        'description' => 'Updated Sprint Description',
        'duration' => 4,
    ];

    $this->actingAs($admin)
        ->patchJson(route('organizations.projects.sprints.update', [
            'organization' => 1,
            'project' => 1,
            'sprint' => $sprint->id,
        ]), $updatedData)
        ->assertStatus(200);

    $sprint->refresh();

    $this->assertEquals('Updated Sprint Description', $sprint->description);
    $this->assertEquals(4, $sprint->duration);
});

test('admin can delete sprints', function () {
    $admin = createAdminUser();
    $sprint = Sprint::factory()->create();

    $this->actingAs($admin)
        ->deleteJson(route('organizations.projects.sprints.destroy', [
            'organization' => 1,
            'project' => 1,
            'sprint' => $sprint->id,
        ]))
        ->assertStatus(200);

    $this->assertDatabaseMissing('sprints', [
        'id' => $sprint->id
    ]);
});

test('admin can create user stories', function () {
    $admin = createAdminUser();
    $userStory = UserStory::factory()->make([
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $this->actingAs($admin)
        ->postJson(route('organizations.projects.user_stories.store', [
            'organization' => 1,
            'project' => 1,
        ]), $userStory->toArray())
        ->assertStatus(201);

    $this->assertDatabaseHas('user_stories', [
        'title' => $userStory->title,
        'description' => $userStory->description,
    ]);
});

test('admin can update user stories', function () {
    $admin = createAdminUser();
    $userStory = UserStory::factory()->create();

    $updatedData = [
        'title' => 'Updated User Story Title',
        'description' => 'Updated User Story Description',
    ];

    $this->actingAs($admin)
        ->putJson(route('organizations.projects.user_stories.update', [
            'organization' => 1,
            'project' => 1,
            'user_story' => $userStory->id,
        ]), $updatedData)
        ->assertStatus(200);

    $userStory->refresh();
    $this->assertEquals('Updated User Story Title', $userStory->title);
    $this->assertEquals('Updated User Story Description', $userStory->description);
});

test("admin can visualize organizations", function () {
    $admin = createAdminUser();
    $this->actingAs($admin)
        ->getJson(route('organizations.index'))
        ->assertStatus(200);
});

test("admin can visualize projects", function () {
    $admin = createAdminUser();
    $this->actingAs($admin)
        ->getJson(route('organizations.projects.index', [
            'organization' => 1,
        ]))
        ->assertStatus(200);
});

test("admin can visualize sprints", function () {
    $admin = createAdminUser();
    $this->actingAs($admin)
        ->getJson(route('organizations.projects.sprints.index', [
            'organization' => 1,
            'project' => 1,
        ]))
        ->assertStatus(200);
});

test("admin can visualize user stories", function () {
    $admin = createAdminUser();
    $this->actingAs($admin)
        ->getJson(route('organizations.projects.user_stories.index', [
            'organization' => 1,
            'project' => 1,
        ]))
        ->assertStatus(200);
});

test("admin can visualize tasks", function () {
    $admin = createAdminUser();
    $this->actingAs($admin)
        ->getJson(route('organizations.projects.user_stories.tasks.index', [
            'organization' => 1,
            'project' => 1,
            'user_story' => 1,
        ]))
        ->assertStatus(200);
});