<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('units')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('units')->insert([
            [
                'id' => 1,
                'group_id' => 1,
                'name' => 'General Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'group_id' => 1,
                'name' => 'SRM (Senior Manager)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'group_id' => 1,
                'name' => 'MSB (Manager Sub Bidang)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'group_id' => 1,
                'name' => 'Asman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'group_id' => 1,
                'name' => 'Staf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'group_id' => 8,
                'name' => 'Jamali',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}