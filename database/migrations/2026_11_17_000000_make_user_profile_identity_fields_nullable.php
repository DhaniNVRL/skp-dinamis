<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Versi database lama masih dapat memiliki kolom fullname/email NOT NULL,
     * walaupun form dan import menerima kedua nilai tersebut sebagai opsional.
     */
    public function up(): void
    {
        if (! Schema::hasTable('user_profiles')) {
            return;
        }

        Schema::table('user_profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('user_profiles', 'fullname')) {
                $table->string('fullname')->nullable()->change();
            }

            if (Schema::hasColumn('user_profiles', 'email')) {
                $table->string('email')->nullable()->change();
            }
        });
    }

    /**
     * Tidak mengembalikan NOT NULL agar rollback tidak gagal saat sudah ada
     * profil tanpa nama lengkap atau email.
     */
    public function down(): void
    {
        // Intentionally left nullable to preserve imported user profiles.
    }
};
