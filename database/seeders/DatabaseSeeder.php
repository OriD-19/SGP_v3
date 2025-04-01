<?php

namespace Database\Seeders;
use App\Models\Project;
use App\Models\Organization;
use App\Models\Priority;
use App\Models\Role;
use App\Models\Sprint;
use App\Models\Status;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //create permissions
        $permissions =[
            'Create projects',
            'Create sprints',
            'Create user_stories',
            'Edit projects',
            'Edit sprints',
            'Edit user_stories',
            'Delete projects',
            'Delete sprints',
            'Assign team members in project',
            'Assign roles in project',
            'Edit team member in project',
            'Edit roles in project',         
        ];

        foreach ($permissions as $permiso){
            Permission::firstOrCreate(['name' => $permiso]);
        }

        //create roles

        $adminrole = Role::factory()->create([
            'role' => 'administrator',
        ]);

        Role::factory()->create([
            'role' => 'scrum_master',
        ]);

        Role::factory()->create([
            'role' => 'product_owner',
        ]);

        Role::factory()->create([
            'role' => 'team_member',
        ]);

        Priority::factory()->create([
            'priority' => 'low',
        ]);

        Priority::factory()->create([
            'priority' => 'medium',
        ]);

        Priority::factory()->create([
            'priority' => 'high',
        ]);

        Status::factory()->create([
            'status' => 'backlog',
        ]);

        Status::factory()->create([
            'status' => 'in_progress',
        ]);

        Status::factory()->create([
            'status' => 'done',
        ]);

        Status::factory()->create([
            'status' => 'in_review',
        ]);

        Status::factory()->create([
            'status' => 'in_testing',
        ]);

        Status::factory()->create([
            'status' => 'to_do',
        ]);

        $mainOrg = Organization::factory()->create([
            'name' => 'Test Organization',
            'description' => 'Test Description',
            'email' => 'org@org.com',
        ]);

        $user = User::factory()->for($mainOrg)->create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'adminorg@org.com',
            'password' => bcrypt('admin123'), // encrypt test password
            'is_admin' => true,
        ]);


        $project = Project::factory()->create([
            'project_name' => 'Propi',
            'description' => 'Proyecto de propi',
            'organization_id' => 1,
            'status_id' =>1,
        ]);

        $sprint = Sprint::factory()->create([
            'duration' => 2,
            'description' => 'Proyecto de propi',
            'start_date' => now(),
            'active' => false,
            'project_id' => 1,
        ]);


        //asignar permisos
        foreach ($permissions as $nombre_permiso){
            $permission = Permission::where('name', $nombre_permiso) -> first();
            if($permission){
                $adminrole -> permissions() -> attach($permission);
            }
        }

        DB::table('team_members')->insert([
            'user_id' => $user->id,
            'role_id' => $adminrole->id,
            'project_id' => $project->id,
        ]);
    }
}
