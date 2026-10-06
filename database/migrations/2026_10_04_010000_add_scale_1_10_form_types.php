<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Tambahkan form Penilaian Pelanggan dan Keterikatan skala 1-10.
    public function up(): void
    {
        DB::table('form_types')->upsert([
            [
                'id' => 17,
                'name' => 'Form Penilaian Pelanggan',
                'description' => 'Skala 1-10 dengan Nilai 0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 18,
                'name' => 'Form Keterikatan',
                'description' => 'Skala 1-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id'], ['name', 'description', 'updated_at']);
    }

    // Hapus tipe form baru apabila belum digunakan.
    public function down(): void
    {
        DB::table('form_types')
            ->whereIn('id', [17, 18])
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('forms')
                    ->whereColumn('forms.formtype_id', 'form_types.id');
            })
            ->delete();
    }
};
