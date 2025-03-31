<?php

use App\Models\Organization;
use App\Models\Project;
use App\Models\Role;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('create an user successfully', function () {
    $organization = Organization::factory()->create(); //El usuario tiene como obligatorio un ID de Organization
    $user = User::factory()->create([
        'organization_id' => $organization->id, //por eso le pongo este desde aqui. Aunque, creo que se puede definir en el model idk
    ]);

    $response = $this->postJson('api/SGP/v1/register', [
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

    //primero creo un login (incluye el token que se crea al usuario)
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // the actual logout
    $logoutResponse = $this->postJson('api/SGP/v1/logout', [], [
        'Authorization' => "Bearer $token",
    ]);

    $logoutResponse->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully']);

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

    //crear el rol
    $adminRole = Role::factory()->create([
        'role' => 'admin',
    ]);

    $admin = User::factory()->create([
        'organization_id' => $organization->id,
    ]);

    //primero asociar con teamMember ya que el rol se incluye dentro de un TeamMember no directamente al user
    $teamMember = TeamMember::factory()->create([
        'user_id' => $admin->id,
        'role_id' => $adminRole->id,
        'project_id' => $project->id,
    ]);

    //inicio de sesion con el token
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // rutas a las que tiene acceso
    $protectedRoutes = [
        'api/SGP/v1/organizations',
        'api/SGP/v1/projects',
        'api/SGP/v1/sprints',
        'api/SGP/v1/user-stories',
        'api/SGP/v1/tasks',
    ];

    //por cada ruta probarlo
    foreach ($protectedRoutes as $route) {
        $response = $this->getJson($route, [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
    }
});


