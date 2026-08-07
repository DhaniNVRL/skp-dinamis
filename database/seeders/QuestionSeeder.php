<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('questions')->upsert([
            [
                'id'=>1,
                'group_id'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'1',
                'name'=>'Nama Responden :',
                'questiontype_id'=>1,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>2,
                'group_id'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'2',
                'name'=>'Nomor Telepon :',
                'questiontype_id'=>1,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'id'=>3,
                'group_id'=>9,
                'form_id'=>1,
                'no_header'=>'A',
                'no'=>'3',
                'name'=>'Jenis Kelamin Responden :',
                'questiontype_id'=>3,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ], ['id'], ['group_id', 'form_id', 'no_header', 'no', 'name', 'questiontype_id', 'updated_at']);
    }
}
?>
