<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Bersihkan Pembanding Tahun dari seluruh Judul Penilaian Pelanggan.
    public function up(): void
    {
        DB::table('questions')
            ->where('questiontype_id', 1)
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('forms')
                    ->whereColumn('forms.id', 'questions.form_id')
                    ->whereIn('forms.formtype_id', [2, 3, 15, 16, 17]);
            })
            ->update([
                'comparison_enabled' => false,
                'comparison_prompt' => null,
                'comparison_options' => null,
                'updated_at' => now(),
            ]);
    }

    // Pembersihan data tidak dikembalikan saat rollback.
    public function down(): void
    {
        // Tidak ada operasi rollback karena konfigurasi lama tidak dapat dipulihkan.
    }
};
