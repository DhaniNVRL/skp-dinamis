<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityTableSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('activities')->truncate();

        Schema::enableForeignKeyConstraints();

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