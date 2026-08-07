<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('survey_sessions')
            && ! Schema::hasColumn('survey_sessions', 'reopened_at')) {
            Schema::table('survey_sessions', function (Blueprint $table): void {
                $table->timestamp('reopened_at')->nullable()->after('finished_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('survey_sessions')
            && Schema::hasColumn('survey_sessions', 'reopened_at')) {
            Schema::table('survey_sessions', function (Blueprint $table): void {
                $table->dropColumn('reopened_at');
            });
        }
    }
};
