<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Organization;
use App\Models\Priority;
use App\Models\Sprint;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            'Create tasks',
            'Edit tasks',
            'Edit projects',
            'Edit sprints',
            'Edit user_stories',
            'Edit user_story priority',
            'Edit user_story sprint',
            'Delete projects',
            'Delete sprints',
            'Delete user_stories',
            'Delete tasks',
            'Assign team members in project',
            'Assign roles in project',
            'Edit team member in project',
            'Delete team member in project',
            'Edit roles in project',
            'Assign tasks to a team member',
            'Get user_stories',
            'Get team members',
            'Get roles',
            'Get tasks',
            'Get project by id',
            'Get sprint by id',
            'Get user_story by id',
            'Get team_member by id',
            'Get role by id',
            'Get task by id',
            'Get all projects',
            'Get all sprints',
            'Get all user_stories',
            'Change status of assigned task'
        ];

        foreach ($permissions as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        //create roles

        $adminrole = Role::create([
            'name' => 'administrator',
        ]);

        $scrum_master = Role::create([
            'name' => 'scrum_master',
        ]);

        $product_owner = Role::create([
            'name' => 'product_owner',
        ]);

        $team_member = Role::create([
            'name' => 'team_member',
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
        ]);

        $sprint = Sprint::create([
            'duration' => 2,
            'description' => 'Proyecto de propi',
            'start_date' => now(),
            'active' => false,
            'project_id' => 1,
        ]);


        //asignar permisos
        $adminrole->syncPermissions($permissions);

        DB::table('team_members')->insert([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'organization_id' => $mainOrg->id,
        ]);

        // assigning permissions to roles
        /*
            'name' => 'scrum_master',
            'name' => 'product_owner',
            'name' => 'team_member',
        */


        $team_member->syncPermissions([
            'Change status of assigned task',
            'Get all user_stories',
            'Get team members',
            'Get tasks',
            'Get project by id',
            'Get sprint by id',
            'Get user_story by id',
            'Get team_member by id', 
        ]);

        $scrum_master->syncPermissions([
            'Create sprints',
            'Create user_stories',
            'Create tasks',
            'Edit tasks',
            'Delete tasks',
            'Edit projects',
            'Edit sprints',
            'Edit user_stories',
            'Delete sprints',
            'Delete user_stories',
            'Assign tasks to a team member',
        ]);

        $product_owner->syncPermissions([
            'Get project by id',
            'Get all sprints',
            'Get all user_stories',
            'Get all projects',
            'Edit user_story priority',
            'Edit user_story sprint',
            'Get roles',
            'Get tasks',
            'Get task by id',
            'Get role by id',
        ]);
    }
}
