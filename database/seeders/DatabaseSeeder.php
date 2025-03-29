<?php

namespace Database\Seeders;

use App\Models\Priority;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
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

        Role::factory()->create([
            'role' => 'admin',
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

    }
}
