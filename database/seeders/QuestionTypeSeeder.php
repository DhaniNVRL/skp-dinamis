<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_types')->truncate();
        DB::table('question_types')->insert([
            [
                'id'=>1,
                'name' => 'Short Text',
                'description' => 'Jawaban teks pendek',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>2,
                'name' => 'Long Text',
                'description' => 'Jawaban teks panjang',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>3,
                'name' => 'Single Choice',
                'description' => 'Pilih satu jawaban',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>4,
                'name' => 'Multiple Choice',
                'description' => 'Pilih lebih dari satu',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>5,
                'name' => 'Dropdown',
                'description' => 'Pilih dari list',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>6,
                'name' => 'Number',
                'description' => 'Input angka',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>7,
                'name' => 'Date',
                'description' => 'Input tanggal',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>8,
                'name' => 'Email',
                'description' => 'Input Email',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                 'id'=>9,
                'name' => 'Phone Number',
                'description' => 'Nomor telepon',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
