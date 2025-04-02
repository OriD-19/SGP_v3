<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create an user successfully', function () {
    $organization = Organization::factory()->create(); //El usuario tiene como obligatorio un ID de Organization
    $user = User::factory()->make();

    $response = $this->postJson('/api/SGP/v1/register', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => 'password1',
        'password_confirmation' => 'password1',
        'organization_id' => $organization->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'first_name', 'last_name', 'email', 'created_at']);

    $this->assertDatabaseHas('users', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
    ]);

});

test('log in with valid credentials', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id, //Lo mismo de arriba
        'password' => bcrypt('password1'), //El password tiene que estar encriptado
    ]);

    $response = $this->postJson('api/SGP/v1/login', [
        'email' => $user->email,
        'password' => 'password1',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

});

test('log in with invalid credentials', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
        'password' => bcrypt('password1'),
    ]);

    $response = $this->postJson('api/SGP/v1/login', [
        'email' => $user->email,
        'password' => 'wrong_password',
    ]);

    $response->assertStatus(401)
        ->assertjson(['message' => 'invalid credentials']);
});

test('log out successfully', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
        'password' => bcrypt('password1'),
    ]);

    $response = $this->postJson('api/SGP/v1/login', [
        'email' => $user->email,
        'password' => 'password1',
    ]);

    $token = $response->json('token');

    // the actual logout
    $logoutResponse = $this->withToken($token)->postJson('api/SGP/v1/logout', []);

    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'logout successful']);

    $this->assertDatabaseEmpty('personal_access_tokens');
    $this->assertAuthenticated('sanctum'); //ver si esto es necesario
});

test('unauthenticated user cannot access protected content', function () {
    $response = $this->getJson(route('organizations.index'));

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated.']);
});

test('admin user can access protected resources', function () {
    $organization = Organization::factory()->create();

    $project = Project::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $admin = User::factory()->create([
        'is_admin' => true,
        'organization_id' => $organization->id,
    ]);

    $user_story = UserStory::factory()->create([
        'project_id' => $project->id,
    ]);

    $this->actingAs($admin);
    // rutas a las que tiene acceso
    $protectedRoutes = [
        route('organizations.index'),
        route('organizations.show', $organization->id),
        route('organizations.projects.index', $organization->id),
        route('organizations.projects.show', [$organization->id, $project->id]),
        route('organizations.projects.user_stories.index', [$organization->id, $project->id]),
        route('organizations.projects.user_stories.show', [$organization->id, $project->id, $user_story->id]),
        route('organizations.projects.user_stories.tasks.index', [$organization->id, $project->id, $user_story->id]),
    ];

    //por cada ruta probarlo
    foreach ($protectedRoutes as $route) {
        $response = $this->getJson($route);

        $response->assertStatus(200);
    }
});


