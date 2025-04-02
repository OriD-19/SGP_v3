<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create an user successfully', function () {
    $organization = Organization::factory()->create(); //El usuario tiene como obligatorio un ID de Organization
    $user = User::factory()->create([
        'organization_id' => $organization->id, //por eso le pongo este desde aqui. Aunque, creo que se puede definir en el model idk
    ]);

    $response = $this->postJson('/api/SGP/v1/register', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => $user->password,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['first_name', 'last_name', 'email', 'created_at']);

    $this->assertDatabaseHas('users', [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'password' => $user->password,
    ]);

});

test('log in with valid credentials', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id, //Lo mismo de arriba
    ]);

    $response = $this->postJson('api/SGP/v1/login', [
        'email' => $user->email,
        'password' => bcrypt('password1'),
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);

});

test('log out successfully', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    $this->actingAs($user);
    // the actual logout
    $logoutResponse = $this->postJson('api/SGP/v1/logout', []);

    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'logout successful']);

});

test('unauthorized user cannot access protected content', function () {
    $response = $this->getJson('api/SGP/v1/projects'); //esta ruta de ejemplito por ahora

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized access, you need to log in']);
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

    $this->actingAs($admin);
    // rutas a las que tiene acceso
    $protectedRoutes = [
        'api/SGP/v1/organizations',
        'api/SGP/v1/organizations/1/projects',
        'api/SGP/v1/organizations/1/sprints',
        'api/SGP/v1/organizations/1/projects/1/user-stories',
        'api/SGP/v1/organizations/1/projects/1/user-stories/1/tasks',
    ];

    //por cada ruta probarlo
    foreach ($protectedRoutes as $route) {
        $response = $this->getJson($route);

        $response->assertStatus(200);
    }
});


