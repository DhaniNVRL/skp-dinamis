<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('form_types')->upsert([[
            'id' => 19,
            'name' => 'Form Penilaian Pelanggan',
            'description' => 'Skala 1-10 Tanpa Nilai 0',
            'created_at' => now(),
            'updated_at' => now(),
        ]], ['id'], ['name', 'description', 'updated_at']);
    }

    public function down(): void
    {
        DB::table('form_types')
            ->where('id', 19)
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')->from('forms')
                    ->whereColumn('forms.formtype_id', 'form_types.id');
            })
            ->delete();
    }
};
