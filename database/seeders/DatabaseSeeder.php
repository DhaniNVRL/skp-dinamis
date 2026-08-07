<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            ActivityTableSeeder::class,
            GroupsTableSeeder::class,
            UnitsTableSeeder::class,
            FormTypeSeeder::class,
            QuestionTypeSeeder::class,
        ]);
    }
}
