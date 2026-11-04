<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            if (! Schema::hasColumn('questions', 'importance_label')) {
                $table->string('importance_label', 100)->nullable()->after('name');
            }

            if (! Schema::hasColumn('questions', 'performance_label')) {
                $table->string('performance_label', 100)->nullable()->after('importance_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $columns = array_values(array_filter([
                Schema::hasColumn('questions', 'importance_label') ? 'importance_label' : null,
                Schema::hasColumn('questions', 'performance_label') ? 'performance_label' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
