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
            ],
            [
                'id' => 4,
                'name' => 'Form Keterikatan',
                'description' => 'Skala 1-5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Form Keterikatan',
                'description' => 'Skala 1-7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Form Rangking',
                'description' => '3 Besar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Form Rangking',
                'description' => '5 Besar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Form Keunggulan, Keluhan, Saran',
                'description' => 'Keunggulan, Keluhan, Saran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'Form Keluhan, Saran',
                'description' => 'Keluhan, Saran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'Form Saran',
                'description' => ' Saran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'Form Pembanding',
                'description' => 'Pembanding',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'name' => 'Form Descrition',
                'description' => 'Descrition',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}