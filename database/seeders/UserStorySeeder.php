<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_stories')->insert([
            [
                'title' => 'As a user, I want to be able to create an account so that I can access the system.',
                'description' => 'This user story describes the need for a user to create an account in order to access the system.',
                "due_date" => now(),
                'project_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'As a user, I want to be able to log in so that I can access my account.',
                'description' => 'This user story describes the need for a user to log in to their account to access the system.',
                "due_date" => now(),
                'project_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'As a user, I want to be able to reset my password so that I can regain access to my account.',
                'description' => 'This user story describes the need for a user to reset their password in case they forget it.',
                "due_date" => now(),
                'project_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
