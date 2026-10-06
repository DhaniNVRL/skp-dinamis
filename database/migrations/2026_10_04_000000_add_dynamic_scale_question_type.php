<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tambahkan konfigurasi Skala Dinamis khusus Kuesioner Umum.
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->unsignedInteger('scale_min')->nullable()->after('questiontype_id');
            $table->unsignedInteger('scale_max')->nullable()->after('scale_min');
            $table->string('scale_left_label', 255)->nullable()->after('scale_max');
            $table->string('scale_right_label', 255)->nullable()->after('scale_left_label');
        });

        DB::table('question_types')->updateOrInsert(
            ['id' => 11],
            [
                'name' => 'Skala Dinamis',
                'description' => 'Pilihan skala horizontal dengan nilai akhir dan label kiri/kanan yang dapat diatur.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    // Hapus tipe dan konfigurasi Skala Dinamis.
    public function down(): void
    {
        DB::table('question_types')->where('id', 11)->delete();

        Schema::table('questions', function (Blueprint $table): void {
            $table->dropColumn([
                'scale_min',
                'scale_max',
                'scale_left_label',
                'scale_right_label',
            ]);
        });
    }
};
