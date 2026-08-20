<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained();
            $table->foreignId('form_id')
                ->constrained('forms');
            $table->foreignId('question_id')
                ->constrained('questions');
            $table->foreignId('competitor_id')
                ->nullable()
                ->constrained('competitors');
            $table->foreignId('respondent_competitor_id')
                ->nullable()
                ->constrained('respondent_competitors')
                ->cascadeOnDelete();
            $table->foreignId('subunit_id')
                ->nullable()
                ->constrained('subunits')
                ->nullOnDelete();
            $table->longText('answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
