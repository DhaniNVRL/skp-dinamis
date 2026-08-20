<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respondent_competitors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('activities')->cascadeOnDelete();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('name');
            $table->timestamps();
            $table->unique(['user_id', 'form_id', 'position'], 'respondent_competitor_position_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respondent_competitors');
    }
};