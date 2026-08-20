<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubUnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subunits')->upsert([[
            'id' => 1,
            'unit_id' => 1,
            'name' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['id'], ['unit_id', 'name', 'updated_at']);
    }
}