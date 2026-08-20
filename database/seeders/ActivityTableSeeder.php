<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('activities')->upsert([[
            'id' => 1,
            'name' => 'test',
            'description' => 'Activity test untuk mencoba seluruh tipe form dan pertanyaan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['id'], ['name', 'description', 'updated_at']);
    }
}