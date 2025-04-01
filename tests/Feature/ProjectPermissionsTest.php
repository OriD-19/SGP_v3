<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPermissionsTest extends TestCase
{
    public function test_administrator_can_create_projects()
    {
        $admin = $this->createAdminUser();
        $project = Project::factory()->make();

        $this->actingAs($admin)
            ->postJson(route('organizations.projects.store', [
                'organization' => 1,
                'project' => 1,
            ]), $project->toArray())
            ->assertStatus(201);
    }

    public function test_administrator_can_update_projects()
    {
        $admin = $this->createAdminUser();
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
        $this->assertEquals('Updated Project Name', $project->name);
        $this->assertEquals('Updated Project Description', $project->description);
    }

    public function test_administrator_can_delete_projects()
    {
        $admin = $this->createAdminUser();
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
    }

    private function createAdminUser()
    {

        $organization = \App\Models\Organization::firstOrCreate(
            ['name' => 'Hugo'],
            [
                'description' => 'compañía de Hugo',
                'email' => 'hugo@empresa.com',
                'created_at' => now(),
            ]
        );

        if (!$organization) {
            throw new \Exception("La organización no se pudo crear.");
        }

        $user = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'adminorg@org.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id
        ]);

        $role = Role::where('role', 'administrator')->first();

        if (!$role) {
            $role = Role::create(['role' => 'administrator']);
        }

        $project = Project::factory()->create();
        TeamMember::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'role_id' => $role->id,
        ]);

        $user->assignRole($role->role);
        return $user;
    }
}
