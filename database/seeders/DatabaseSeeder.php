<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            FormTypeSeeder::class,
            QuestionTypeSeeder::class,
            ActivityTableSeeder::class,
            GroupsTableSeeder::class,
            UnitsTableSeeder::class,
            SubUnitsTableSeeder::class,
            DemoSurveySeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}