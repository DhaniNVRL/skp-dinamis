<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('form_types')->updateOrInsert(
            ['id' => 15],
            [
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-5 Tanpa Nilai 0',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('form_types')->updateOrInsert(
            ['id' => 16],
            [
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-7 Tanpa Nilai 0',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('form_types')
            ->whereIn('id', [15, 16])
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('forms')
                    ->whereColumn('forms.formtype_id', 'form_types.id');
            })
            ->delete();
    }
};
