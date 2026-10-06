<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->foreignId('merge_parent_id')
                ->nullable()
                ->after('formtype_id')
                ->constrained('forms')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('merge_parent_id');
        });
    }
};
