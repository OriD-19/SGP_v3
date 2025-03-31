<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = DB::table('organizations')->first();

        if (!$org) {
            echo "Please help me";
            $org = DB::table('organizations')->insertGetId([
                'name' => 'Test Organization',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password123'),
                'organization_id' => $org->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'janesmith@example.com',
                'password' => Hash::make('securepassword'),
                'organization_id' => $org->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alicejohnson@example.com',
                'password' => Hash::make('alicepassword'),
                'organization_id' => $org->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
