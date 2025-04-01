<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Role;
use App\Models\UserStory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionAdminTest extends TestCase {
    use RefreshDatabase;

    public function test_administrator_can_create_sprints()
    {
        $admin = $this->createAdminUser();
        $sprint = Sprint::factory()->make();

        $this->actingAs($admin)
            ->post(route('organizations.projects.sprints.store'), $sprint->toArray())
            ->assertStatus(201);
    }

    public function test_administrator_can_update_sprints()
    {
        $admin = $this->createAdminUser();
        $sprint = Sprint::factory()->create();

        $updatedData = [
            'start_date' => 'Updated Sprint start date',
            'description' => 'Updated sprint Description',
        ];

        $this->actingAs($admin)
            ->put(route('organizations.projects.sprints.update', $sprint->id), $updatedData)
            ->assertStatus(200);

        $sprint->refresh();
        $this->assertEquals('Updated sprint Start date', $sprint->start_date);
        $this->assertEquals('Updated sprint Description', $sprint->description);
    }

    public function test_administrator_can_delete_sprint()
    {   
        $admin = $this->createAdminUser();
        $sprint = Sprint::factory()->create();

        $this->actingAs($admin)
            ->delete(route('organizations.projects.sprints.destroy', $sprint->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('sprints', [
            'id' => $sprint->id
        ]);
    }


    public function test_administrator_can_create_user_stories()
    {
        $admin = $this->createAdminUser();
        $userStory = UserStory::factory()->make();

        $this->actingAs($admin)
            ->post(route('organizations.projects.user_stories.store'), $userStory->toArray())
            ->assertStatus(201);
    }

    public function test_administrator_can_update_user_stories()
    {
        $admin = $this->createAdminUser();
        $userStory = UserStory::factory()->create();

        $updatedData = [
            'title' => 'Updated userStory Title',
            'description' => 'Updated userStory Description',
            'due_date' => 'Update userStory Due date',
            'sprint_id' => 'Update userStory Sprint Id'
        ];

        $this->actingAs($admin)
            ->put(route('organizations.projects.user_stories.update', $userStory->id), $updatedData)
            ->assertStatus(200);

        $userStory->refresh();
        $this->assertEquals('Updated userStory Title', $userStory->title);
        $this->assertEquals('Update userStory Description', $userStory->description);
        $this->assertEquals('Update userStory Due date', $userStory->due_date);
        $this->assertEquals('Update userStory Sprint id', $userStory->sprint_id);
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
            'organization_id'=>$organization->id
        ]);

        $role = Role::where('role', 'administrator')->first();

        if (!$role) {
            $role = Role::create(['role' => 'administrator']);
        }

        $sprint = Sprint::factory()->create();  
        $userStory = UserStory::factory()->create(['sprint_id' => $sprint->id]);
        $project = Project::factory()->create();

        TeamMember::create([
            'user_id' => $user->id,
            'sprint_id' => $project->id,
            'role_id' => $role->id,
        ]);

        return $user;
    }

}
