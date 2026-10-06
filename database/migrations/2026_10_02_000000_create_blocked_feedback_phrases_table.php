<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_feedback_phrases', function (Blueprint $table): void {
            $table->id();
            $table->string('phrase', 500);
            $table->string('normalized_phrase', 500);
            $table->char('normalized_hash', 64)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_feedback_phrases');
    }
};
