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
                'name' => 'SKP TJB Power Services 2026',
                'description' => 'SKP TJB Power Services 2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'SKP PLN UIP2B JAMALI 2026',
                'description' => 'SKP PLN UIP2B JAMALI 2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}