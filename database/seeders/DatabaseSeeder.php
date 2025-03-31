<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Priority;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
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
        // User::factory(10)->create();

        Role::create([
            'role' => 'scrum_master',
        ]);

        Role::create([
            'role' => 'product_owner',
        ]);

        Role::create([
            'role' => 'team_member',
        ]);

        $admin = Role::create([
            'role' => 'admin',
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

        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            UserStorySeeder::class,
        ]);    
    }
}
