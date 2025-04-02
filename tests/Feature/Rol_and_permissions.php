<?php

use App\Models\Organization;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Scrum Master puede editar un sprint', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['name' => 'scrum_master']);

    // Crear un usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    // Asociar al usuario como Scrum Master en el proyecto
    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint en el proyecto
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como Scrum Master
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $scrumMaster->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar actualizar el sprint
    $updateResponse = $this->putJson("api/SGP/v1/sprints/{$sprint->id}", [
        'description' => 'Sprint actualizado',
    ], [
        'Authorization' => "Bearer $token",
    ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['message' => 'Sprint actualizado correctamente']);
});

test('Usuario sin permisos no puede editar un sprint', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear un usuario sin rol de Scrum Master
    $regularUser = User::factory()->create(['organization_id' => $organization->id]);

    // NO asociamos al usuario como Scrum Master
    TeamMember::factory()->create([
        'user_id' => $regularUser->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como usuario normal
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $regularUser->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar actualizar el sprint sin ser Scrum Master
    $updateResponse = $this->putJson("api/SGP/v1/sprints/{$sprint->id}", [
        'description' => 'Intento de actualización',
    ], [
        'Authorization' => "Bearer $token",
    ]);

    $updateResponse->assertStatus(403)
        ->assertJson(['message' => 'No tienes permisos para editar este sprint.']);
});

test('Scrum Master puede eliminar un sprint', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['name' => 'scrum_master']);

    // Crear usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como Scrum Master
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $scrumMaster->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar eliminar el sprint
    $deleteResponse = $this->deleteJson("api/SGP/v1/sprints/{$sprint->id}", [], [
        'Authorization' => "Bearer $token",
    ]);

    $deleteResponse->assertStatus(200)
        ->assertJson(['message' => 'Sprint eliminado correctamente']);

    // Verificar que el sprint fue eliminado de la base de datos
    $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
});

test('Usuario sin permisos no puede eliminar un sprint', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear usuario sin rol de Scrum Master
    $regularUser = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $regularUser->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como usuario normal
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $regularUser->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar eliminar el sprint sin ser Scrum Master
    $deleteResponse = $this->deleteJson("api/SGP/v1/sprints/{$sprint->id}", [], [
        'Authorization' => "Bearer $token",
    ]);

    $deleteResponse->assertStatus(403)
        ->assertJson(['message' => 'No tienes permisos para eliminar este sprint.']);

    // Verificar que el sprint sigue existiendo en la base de datos
    $this->assertDatabaseHas('sprints', ['id' => $sprint->id]);
});

test('Scrum Master puede crear una User Story', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['name' => 'scrum_master']);

    // Crear usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como Scrum Master
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $scrumMaster->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar crear una User Story
    $userStoryData = [
        'title' => 'Como usuario, quiero registrar mis tareas diarias',
        'description' => 'Para poder gestionar mejor mi tiempo',
        'sprint_id' => $sprint->id,
    ];

    $createResponse = $this->postJson('api/SGP/v1/user-stories', $userStoryData, [
        'Authorization' => "Bearer $token",
    ]);

    $createResponse->assertStatus(201)
        ->assertJson(['message' => 'User Story creada correctamente']);

    // Verificar que la User Story fue creada en la base de datos
    $this->assertDatabaseHas('user_stories', [
        'title' => $userStoryData['title'],
        'description' => $userStoryData['description'],
        'sprint_id' => $sprint->id,
    ]);
});

test('Usuario sin permisos no puede crear una User Story', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear usuario sin rol de Scrum Master
    $regularUser = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $regularUser->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Iniciar sesión como usuario normal
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $regularUser->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar crear una User Story sin permisos
    $userStoryData = [
        'title' => 'Como usuario, quiero registrar mis tareas diarias',
        'description' => 'Para poder gestionar mejor mi tiempo',
        'sprint_id' => $sprint->id,
    ];

    $createResponse = $this->postJson('api/SGP/v1/user-stories', $userStoryData, [
        'Authorization' => "Bearer $token",
    ]);

    $createResponse->assertStatus(403)
        ->assertJson(['message' => 'No tienes permisos para crear User Stories.']);

    // Verificar que la User Story NO fue creada en la base de datos
    $this->assertDatabaseMissing('user_stories', [
        'title' => $userStoryData['title'],
        'description' => $userStoryData['description'],
        'sprint_id' => $sprint->id,
    ]);
});

test('Scrum Master puede editar una User Story', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['name' => 'scrum_master']);

    // Crear usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Crear una User Story
    $userStory = UserStory::factory()->create([
        'title' => 'Historia original',
        'description' => 'Descripción original',
        'sprint_id' => $sprint->id,
    ]);

    // Iniciar sesión como Scrum Master
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $scrumMaster->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar actualizar la User Story
    $updateResponse = $this->putJson("api/SGP/v1/user-stories/{$userStory->id}", [
        'title' => 'Historia editada',
        'description' => 'Descripción editada',
    ], [
        'Authorization' => "Bearer $token",
    ]);

    $updateResponse->assertStatus(200)
        ->assertJson(['message' => 'User Story actualizada correctamente']);

    // Verificar que los cambios fueron aplicados en la base de datos
    $this->assertDatabaseHas('user_stories', [
        'id' => $userStory->id,
        'title' => 'Historia editada',
        'description' => 'Descripción editada',
    ]);
});

test('Scrum Master puede eliminar una User Story', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['name' => 'scrum_master']);

    // Crear usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
    ]);

    // Crear un Sprint
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);

    // Crear una User Story
    $userStory = UserStory::factory()->create([
        'title' => 'Historia a eliminar',
        'description' => 'Descripción a eliminar',
        'sprint_id' => $sprint->id,
    ]);

    // Iniciar sesión como Scrum Master
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $scrumMaster->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar eliminar la User Story
    $deleteResponse = $this->deleteJson("api/SGP/v1/user-stories/{$userStory->id}", [], [
        'Authorization' => "Bearer $token",
    ]);

    $deleteResponse->assertStatus(200)
        ->assertJson(['message' => 'User Story eliminada correctamente']);

    // Verificar que la User Story fue eliminada de la base de datos
    $this->assertDatabaseMissing('user_stories', ['id' => $userStory->id]);
});
