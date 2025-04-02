<?php

use App\Models\Organization;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;
<<<<<<< HEAD
use App\Models\UserStory;
=======
use App\Models\Task;
>>>>>>> 7a6b6fb (Update rol and permissions)
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

test('Scrum Master puede crear una Task', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);

    // Crear el rol de Scrum Master
    $scrumMasterRole = Role::factory()->create(['role' => 'scrum_master']);

    // Crear usuario y asignarle el rol de Scrum Master
    $scrumMaster = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $scrumMaster->id,
        'project_id' => $project->id,
        'role_id' => $scrumMasterRole->id,
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

    // Intentar crear una Task
    $taskData = [
        'title' => 'Nueva tarea',
        'description' => 'Descripción de la nueva tarea',
        'due_date' => now()->addDays(7)->toDateString(),
        'sprint_id' => $sprint->id,
    ];

    $createResponse = $this->postJson('api/SGP/v1/tasks', $taskData, [
        'Authorization' => "Bearer $token",
    ]);

    $createResponse->assertStatus(201)
        ->assertJson(['message' => 'Task creada correctamente']);

    // Verificar que la Task fue creada en la base de datos
    $this->assertDatabaseHas('tasks', [
        'title' => $taskData['title'],
        'description' => $taskData['description'],
        'due_date' => $taskData['due_date'],
        'sprint_id' => $sprint->id,
    ]);
});

test('Scrum Master puede editar tasks', function () {
    // Crear un usuario con rol Scrum Master
    $scrumMaster = User::factory()->create(['role' => 'Scrum Master']);
    
    // Crear una tarea existente
    $task = Task::factory()->create();
    
    // Simular que el Scrum Master intenta editar la tarea
    $response = actingAs($scrumMaster)->put(route('tasks.update', $task->id), [
        'title' => 'Nuevo título',
        'description' => 'Descripción actualizada',
        'due_date' => now()->addDays(5),
    ]);
    
    // Verificar que la respuesta es exitosa
    $response->assertStatus(200);
    
    // Verificar que la tarea se actualizó en la base de datos
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Nuevo título',
        'description' => 'Descripción actualizada',
    ]);
});

test('Scrum Master puede eliminar tasks', function () {
    // Crear un usuario con rol Scrum Master
    $scrumMaster = User::factory()->create(['role' => 'Scrum Master']);
    
    // Crear una tarea existente
    $task = Task::factory()->create();
    
    // Simular que el Scrum Master intenta eliminar la tarea
    $response = actingAs($scrumMaster)->delete(route('tasks.destroy', $task->id));
    
    // Verificar que la respuesta es exitosa
    $response->assertStatus(200);
    
    // Verificar que la tarea fue eliminada de la base de datos
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('Cliente solo puede visualizar proyectos, sprints, user stories y tareas', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);
    
    // Crear el rol de Cliente
    $clientRole = Role::factory()->create(['role' => 'cliente']);

    // Crear usuario y asignarle el rol de Cliente
    $client = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $client->id,
        'project_id' => $project->id,
        'role_id' => $clientRole->id,
    ]);

    // Crear un Sprint y una Task
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'title' => 'Task del cliente',
        'description' => 'Descripción de task del cliente',
        'sprint_id' => $sprint->id,
    ]);

    // Crear una User Story
    $userStory = UserStory::factory()->create([
        'project_id' => $project->id,
        'title' => 'User Story del cliente',
        'description' => 'Descripción de user story del cliente',
    ]);

    // Iniciar sesión como Cliente
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $client->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar obtener los proyectos (debería funcionar)
    $response = $this->getJson('api/SGP/v1/projects', [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener los sprints (debería funcionar)
    $response = $this->getJson("api/SGP/v1/projects/{$project->id}/sprints", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener las user stories (debería funcionar)
    $response = $this->getJson("api/SGP/v1/projects/{$project->id}/user-stories", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener las tareas (debería funcionar)
    $response = $this->getJson("api/SGP/v1/sprints/{$sprint->id}/tasks", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar editar un proyecto (debería fallar)
    $response = $this->putJson("api/SGP/v1/projects/{$project->id}", [
        'title' => 'Nuevo título de proyecto',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar un proyecto (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/projects/{$project->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar editar un sprint (debería fallar)
    $response = $this->putJson("api/SGP/v1/sprints/{$sprint->id}", [
        'title' => 'Nuevo título de sprint',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar una user story (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/user-stories/{$userStory->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar editar una tarea (debería fallar)
    $response = $this->putJson("api/SGP/v1/tasks/{$task->id}", [
        'title' => 'Nuevo título de tarea',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar una tarea (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/tasks/{$task->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);
});

test('Team Member puede visualizar proyectos, sprints, user stories y tareas', function () {
    $organization = Organization::factory()->create();
    $project = Project::factory()->create(['organization_id' => $organization->id]);
    
    // Crear el rol de Team Member
    $teamMemberRole = Role::factory()->create(['role' => 'team_member']);

    // Crear usuario y asignarle el rol de Team Member
    $teamMember = User::factory()->create(['organization_id' => $organization->id]);

    TeamMember::factory()->create([
        'user_id' => $teamMember->id,
        'project_id' => $project->id,
        'role_id' => $teamMemberRole->id,
    ]);

    // Crear un Sprint y una Task
    $sprint = Sprint::factory()->create(['project_id' => $project->id]);
    $task = Task::factory()->create([
        'title' => 'Tarea del miembro del equipo',
        'description' => 'Descripción de tarea del miembro del equipo',
        'sprint_id' => $sprint->id,
    ]);

    // Crear una User Story
    $userStory = UserStory::factory()->create([
        'project_id' => $project->id,
        'title' => 'User Story del miembro del equipo',
        'description' => 'Descripción de user story del miembro del equipo',
    ]);

    // Iniciar sesión como Team Member
    $loginResponse = $this->postJson('api/SGP/v1/login', [
        'email' => $teamMember->email,
        'password' => 'password',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    // Intentar obtener los proyectos (debería funcionar)
    $response = $this->getJson('api/SGP/v1/projects', [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener los sprints (debería funcionar)
    $response = $this->getJson("api/SGP/v1/projects/{$project->id}/sprints", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener las user stories (debería funcionar)
    $response = $this->getJson("api/SGP/v1/projects/{$project->id}/user-stories", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar obtener las tareas (debería funcionar)
    $response = $this->getJson("api/SGP/v1/sprints/{$sprint->id}/tasks", [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(200);

    // Intentar editar un proyecto (debería fallar)
    $response = $this->putJson("api/SGP/v1/projects/{$project->id}", [
        'title' => 'Nuevo título de proyecto',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar un proyecto (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/projects/{$project->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar editar un sprint (debería fallar)
    $response = $this->putJson("api/SGP/v1/sprints/{$sprint->id}", [
        'title' => 'Nuevo título de sprint',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar una user story (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/user-stories/{$userStory->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar editar una tarea (debería fallar)
    $response = $this->putJson("api/SGP/v1/tasks/{$task->id}", [
        'title' => 'Nuevo título de tarea',
    ], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);

    // Intentar eliminar una tarea (debería fallar)
    $response = $this->deleteJson("api/SGP/v1/tasks/{$task->id}", [], [
        'Authorization' => "Bearer $token",
    ]);
    $response->assertStatus(403);
});
