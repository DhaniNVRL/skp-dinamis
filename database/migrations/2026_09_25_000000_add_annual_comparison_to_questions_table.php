<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->boolean('comparison_enabled')->default(false)->after('questiontype_id');
            $table->text('comparison_prompt')->nullable()->after('comparison_enabled');
            $table->json('comparison_options')->nullable()->after('comparison_prompt');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropColumn(['comparison_enabled', 'comparison_prompt', 'comparison_options']);
        });
    }
};
