<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('units')->truncate();

        DB::table('units')->insert([
            [
                'id' => 1,
                'id_groups' => 1,
                'name' => 'Group 1 Unit 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'id_groups' => 1,
                'name' => 'Group 1 Unit 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'id_groups' => 1,
                'name' => 'Group 1 Unit 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'id_groups' => 2,
                'name' => 'Group 2 Unit 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'id_groups' => 2,
                'name' => 'Group 2 Unit 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'id_groups' => 2,
                'name' => 'Group 2 Unit 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'id_groups' => 3,
                'name' => 'Group 3 Unit 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'id_groups' => 3,
                'name' => 'Group 3 Unit 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'id_groups' => 3,
                'name' => 'Group 3 Unit 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
