<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['id' => 1, 'name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Surveyor', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Monitoring', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'User', 'created_at' => now(), 'updated_at' => now()],
        ], ['id'], ['name', 'updated_at']);
    }
}