<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('question_types')->delete();

        DB::table('question_types')->insert([
            [
                'id'=>1,
                'id_group'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'1',
                'name'=>'Nama Responden :',
                'id_questiontypes'=>1,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>2,
                'id_group'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'2',
                'name'=>'Nomor Telepon :',
                'id_questiontypes'=>1,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>3,
                'id_group'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'3',
                'name'=>'Jenis Kelamin Responden :',
                'id_questiontypes'=>3,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
?>