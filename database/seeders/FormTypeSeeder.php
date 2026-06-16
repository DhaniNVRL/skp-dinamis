<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormTypeSeeder extends Seeder
{
    public function run(): void
    {
      
        DB::table('form_types')->delete();

        DB::table('form_types')->insert([
            [
                'id' => 1,
                'name' => 'Form Kuesioner Umum',
                'description' => 'Form Kuesioner Umum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-7',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}