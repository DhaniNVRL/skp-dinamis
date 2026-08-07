<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('activities')->upsert([
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
        ], ['id'], ['name', 'description', 'updated_at']);
    }
}
