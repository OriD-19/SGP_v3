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
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //create permissions
        $permissions = [
            'Create projects',
            'Create sprints',
            'Create user_stories',
            'Edit projects',
            'Edit sprints',
            'Edit user_stories',
            'Delete projects',
            'Delete sprints',
            'Delete user_stories',
            'Assign team members in project',
            'Assign roles in project',
            'Edit team member in project',
            'Edit roles in project',
            'Assign tasks to a team member',
        ];

        foreach ($permissions as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        //create roles

        $adminrole = Role::create([
            'role' => 'administrator',
        ]);

        Role::create([
            'role' => 'scrum_master',
        ]);

        Role::create([
            'role' => 'product_owner',
        ]);

        Role::create([
            'role' => 'team_member',
        ]);

        Priority::create([
            'priority' => 'low',
        ]);

        Priority::create([
            'priority' => 'medium',
        ]);

        Priority::create([
            'priority' => 'high',
        ]);

        Status::create([
            'status' => 'backlog',
        ]);

        Status::create([
            'status' => 'in_progress',
        ]);

        Status::create([
            'status' => 'done',
        ]);

        Status::create([
            'status' => 'in_review',
        ]);

        Status::create([
            'status' => 'in_testing',
        ]);

        Status::create([
            'status' => 'to_do',
        ]);

        $mainOrg = Organization::create([
            'name' => 'Main Organization',
            'description' => 'This is the main organization.',
            'email' => "org@org.com",
        ]);

        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@org.com',
            'password' => Hash::make('admin123'),
            'organization_id' => $mainOrg->id,
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $project = Project::create([
            'project_name' => 'Propi',
            'description' => 'Proyecto de propi',
            'organization_id' => 1,
            'status_id' => 1,
        ]);

        $sprint = Sprint::create([
            'duration' => 2,
            'description' => 'Proyecto de propi',
            'start_date' => now(),
            'active' => false,
            'project_id' => 1,
        ]);


        //asignar permisos
        foreach ($permissions as $nombre_permiso) {
            $permission = Permission::where('name', $nombre_permiso)->first();
            if ($permission) {
                $adminrole->permissions()->attach($permission);
            }
        }

        DB::table('team_members')->insert([
            'user_id' => $user->id,
            'role_id' => $adminrole->id,
            'project_id' => $project->id,
        ]);
    }
}
