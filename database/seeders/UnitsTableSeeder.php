<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('units')->upsert([[
            'id' => 1,
            'group_id' => 1,
            'name' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['id'], ['group_id', 'name', 'updated_at']);
    }
}