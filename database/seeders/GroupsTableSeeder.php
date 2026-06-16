<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        // MATIKAN foreign key check sementara
        Schema::disableForeignKeyConstraints();

        DB::table('groups')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('groups')->insert([
            [
                'id' => 1,
                'id_activities' => 1,
                'name' => 'Activity 1 Group 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'id_activities' => 1,
                'name' => 'Activity 1 Group 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'id_activities' => 1,
                'name' => 'Activity 1 Group 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'id_activities' => 2,
                'name' => 'Activity 2 Group 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'id_activities' => 2,
                'name' => 'Activity 2 Group 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'id_activities' => 2,
                'name' => 'Activity 2 Group 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'id_activities' => 3,
                'name' => 'Activity 3 Group 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'id_activities' => 3,
                'name' => 'Activity 3 Group 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'id_activities' => 3,
                'name' => 'Activity 3 Group 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}