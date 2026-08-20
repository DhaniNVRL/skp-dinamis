<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSurveySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $formNames = [
                1 => 'Demo Kuesioner Umum',
                2 => 'Demo Penilaian Pelanggan 1-5',
                3 => 'Demo Penilaian Pelanggan 1-7',
                4 => 'Demo Keterikatan 1-5',
                5 => 'Demo Keterikatan 1-7',
                6 => 'Demo Ranking 3 Besar',
                7 => 'Demo Ranking 5 Besar',
                8 => 'Demo Keunggulan, Keluhan, Saran',
                9 => 'Demo Keluhan dan Saran',
                10 => 'Demo Saran',
                11 => 'Demo Pembanding 1-5',
                12 => 'Demo Description',
                13 => 'Demo Pembanding 1-7',
                14 => 'Demo Pembanding Tanpa Nama Competitor',
            ];

            $forms = [];
            foreach ($formNames as $formTypeId => $name) {
                $forms[] = [
                    'id' => $formTypeId,
                    'group_id' => 1,
                    'no_urut' => $formTypeId,
                    'name' => $name,
                    'formtype_id' => $formTypeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('forms')->upsert(
                $forms,
                ['id'],
                ['group_id', 'no_urut', 'name', 'formtype_id', 'updated_at']
            );

            $questions = [
                ['id' => 1, 'form_id' => 1, 'no_header' => 'A', 'no' => '1', 'name' => 'Contoh jawaban singkat', 'questiontype_id' => 1],
                ['id' => 2, 'form_id' => 1, 'no_header' => 'A', 'no' => '2', 'name' => 'Contoh jawaban panjang', 'questiontype_id' => 2],
                ['id' => 3, 'form_id' => 1, 'no_header' => 'A', 'no' => '3', 'name' => 'Contoh satu pilihan', 'questiontype_id' => 3],
                ['id' => 4, 'form_id' => 1, 'no_header' => 'A', 'no' => '4', 'name' => 'Contoh beberapa pilihan', 'questiontype_id' => 4],
                ['id' => 5, 'form_id' => 1, 'no_header' => 'A', 'no' => '5', 'name' => 'Contoh dropdown', 'questiontype_id' => 5],
                ['id' => 6, 'form_id' => 1, 'no_header' => 'A', 'no' => '6', 'name' => 'Contoh input angka', 'questiontype_id' => 6],
                ['id' => 7, 'form_id' => 1, 'no_header' => 'A', 'no' => '7', 'name' => 'Contoh input tanggal', 'questiontype_id' => 7],
                ['id' => 8, 'form_id' => 1, 'no_header' => 'A', 'no' => '8', 'name' => 'Contoh input email', 'questiontype_id' => 8],
                ['id' => 9, 'form_id' => 1, 'no_header' => 'A', 'no' => '9', 'name' => 'Contoh input nomor telepon', 'questiontype_id' => 9],
                ['id' => 10, 'form_id' => 1, 'no_header' => 'B', 'no' => '1', 'name' => 'Contoh Header Tanpa Jawaban', 'questiontype_id' => 10],
                ['id' => 11, 'form_id' => 2, 'no_header' => 'B', 'no' => '1', 'name' => 'Contoh penilaian pelanggan skala 1-5', 'questiontype_id' => 2],
                ['id' => 12, 'form_id' => 3, 'no_header' => 'C', 'no' => '1', 'name' => 'Contoh penilaian pelanggan skala 1-7', 'questiontype_id' => 2],
                ['id' => 13, 'form_id' => 4, 'no_header' => 'D', 'no' => '1', 'name' => 'Contoh keterikatan skala 1-5', 'questiontype_id' => 2],
                ['id' => 14, 'form_id' => 5, 'no_header' => 'E', 'no' => '1', 'name' => 'Contoh keterikatan skala 1-7', 'questiontype_id' => 2],
                ['id' => 15, 'form_id' => 6, 'no_header' => 'F', 'no' => '1', 'name' => 'Pilih tiga jawaban yang paling penting', 'questiontype_id' => 2],
                ['id' => 16, 'form_id' => 7, 'no_header' => 'G', 'no' => '1', 'name' => 'Pilih lima jawaban yang paling penting', 'questiontype_id' => 2],
                ['id' => 17, 'form_id' => 8, 'no_header' => 'H', 'no' => '1', 'name' => 'Tuliskan keunggulan, keluhan, dan saran', 'questiontype_id' => 2],
                ['id' => 18, 'form_id' => 9, 'no_header' => 'I', 'no' => '1', 'name' => 'Tuliskan keluhan dan saran', 'questiontype_id' => 2],
                ['id' => 19, 'form_id' => 10, 'no_header' => 'J', 'no' => '1', 'name' => 'Tuliskan saran Anda', 'questiontype_id' => 2],
                ['id' => 20, 'form_id' => 11, 'no_header' => 'K', 'no' => '1', 'name' => 'Contoh penilaian kompetitor skala 1-5', 'questiontype_id' => 2],
                ['id' => 21, 'form_id' => 13, 'no_header' => 'L', 'no' => '1', 'name' => 'Contoh penilaian kompetitor skala 1-7', 'questiontype_id' => 2],
                ['id' => 22, 'form_id' => 14, 'no_header' => 'M', 'no' => '1', 'name' => 'Contoh penilaian kompetitor yang ditentukan responden', 'questiontype_id' => 2],
            ];

            foreach ($questions as &$question) {
                $question['group_id'] = 1;
                $question['created_at'] = $now;
                $question['updated_at'] = $now;
            }
            unset($question);

            DB::table('questions')->upsert(
                $questions,
                ['id'],
                ['group_id', 'form_id', 'no_header', 'no', 'name', 'questiontype_id', 'updated_at']
            );

            $options = [];
            $optionId = 1;
            foreach ([3, 4, 5] as $questionId) {
                foreach (['Pilihan A', 'Pilihan B', 'Pilihan C'] as $index => $answer) {
                    $options[] = [
                        'id' => $optionId++,
                        'question_id' => $questionId,
                        'no' => (string) ($index + 1),
                        'answer_text' => $answer,
                        'answer_text2' => null,
                        'has_child' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            foreach ([15, 16] as $questionId) {
                foreach (range(1, 7) as $number) {
                    $options[] = [
                        'id' => $optionId++,
                        'question_id' => $questionId,
                        'no' => (string) $number,
                        'answer_text' => 'Pilihan ranking '.$number,
                        'answer_text2' => null,
                        'has_child' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            DB::table('options')->upsert(
                $options,
                ['id'],
                ['question_id', 'no', 'answer_text', 'answer_text2', 'has_child', 'updated_at']
            );

            DB::table('descriptions')->upsert([[
                'id' => 1,
                'group_id' => 1,
                'form_id' => 12,
                'content' => '<p>Ini adalah contoh Form Description tanpa pertanyaan dan tanpa jawaban.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ]], ['id'], ['group_id', 'form_id', 'content', 'updated_at']);

            DB::table('competitors')->upsert([
                ['id' => 1, 'group_id' => 1, 'form_id' => 11, 'name' => 'Kompetitor A', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 2, 'group_id' => 1, 'form_id' => 11, 'name' => 'Kompetitor B', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 3, 'group_id' => 1, 'form_id' => 13, 'name' => 'Kompetitor A', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 4, 'group_id' => 1, 'form_id' => 13, 'name' => 'Kompetitor B', 'created_at' => $now, 'updated_at' => $now],
            ], ['id'], ['group_id', 'form_id', 'name', 'updated_at']);
        });
    }
}
