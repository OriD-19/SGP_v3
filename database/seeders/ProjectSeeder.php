<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('projects')->insert([
            [
                'project_name' => 'Project A',
                'description' => 'Description for Project A',
                'status_id' => 1,
                'organization_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_name' => 'Project B',
                'description' => 'Description for Project B',
                'status_id' => 2,
                'organization_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_name' => 'Project C',
                'description' => 'Description for Project C',
                'status_id' => 3,
                'organization_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
