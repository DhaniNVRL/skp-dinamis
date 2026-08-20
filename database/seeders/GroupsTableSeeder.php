<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('groups')->upsert([[
            'id' => 1,
            'activity_id' => 1,
            'name' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['id'], ['activity_id', 'name', 'updated_at']);
    }
}