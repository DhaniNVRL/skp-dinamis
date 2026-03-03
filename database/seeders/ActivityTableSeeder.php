<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        // Kosongkan tabel dulu
        DB::table('activities')->truncate();

        // Insert data baru
       DB::table('activities')->insert([
            [
                'id' => 1,
                'name' => 'Activity 1',
                'description' => 'Activity 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Activity 2',
                'description' => 'Activity 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Activity 3',
                'description' => 'Activity 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
