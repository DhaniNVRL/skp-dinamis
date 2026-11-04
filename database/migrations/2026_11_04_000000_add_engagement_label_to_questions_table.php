<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('questions', 'importance_label')) {
            Schema::table('questions', function (Blueprint $table): void {
                $table->string('importance_label', 100)->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('questions', 'performance_label')) {
            Schema::table('questions', function (Blueprint $table): void {
                $table->string('performance_label', 100)->nullable()->after('importance_label');
            });
        }

        Schema::table('questions', function (Blueprint $table): void {
            if (! Schema::hasColumn('questions', 'engagement_label')) {
                $table->string('engagement_label', 100)
                    ->nullable()
                    ->after('performance_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            if (Schema::hasColumn('questions', 'engagement_label')) {
                $table->dropColumn('engagement_label');
            }
        });
    }
};
