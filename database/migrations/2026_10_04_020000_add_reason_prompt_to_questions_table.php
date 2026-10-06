<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tambahkan narasi alasan yang dapat diatur per pertanyaan.
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->text('reason_prompt')->nullable()->after('name');
        });
    }

    // Hapus konfigurasi narasi alasan.
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropColumn('reason_prompt');
        });
    }
};
