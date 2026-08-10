<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_profiles') || ! Schema::hasColumn('user_profiles', 'activity_id')) {
            return;
        }

        Schema::table('user_profiles', function (Blueprint $table): void {
            $table->unsignedBigInteger('activity_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_profiles') || ! Schema::hasColumn('user_profiles', 'activity_id')) {
            return;
        }

        if (DB::table('user_profiles')->whereNull('activity_id')->exists()) {
            throw new \RuntimeException(
                'Rollback dibatalkan karena terdapat akun Admin atau Surveyor tanpa Activity.'
            );
        }

        Schema::table('user_profiles', function (Blueprint $table): void {
            $table->unsignedBigInteger('activity_id')->nullable(false)->change();
        });
    }
};
