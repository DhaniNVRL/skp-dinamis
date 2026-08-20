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
        Schema::create('survey_sessions', function (Blueprint $table) {

            $table->id();

            // User yang mengisi survey
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Lokasi user saat survey
            $table->foreignId('activity_id')
                ->constrained('activities');

            $table->foreignId('group_id')
                ->constrained('groups');

            $table->foreignId('unit_id')
                ->constrained('units');

            // Form terakhir yang sedang dikerjakan
            $table->foreignId('current_form_id')
                ->nullable()
                ->constrained('forms');

            // Status survey
            $table->enum('status', [
                'not_started',
                'in_progress',
                'completed'
            ])->default('not_started');

            // Waktu mulai
            $table->timestamp('started_at')->nullable();

            // Waktu selesai
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('reopened_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_sessions');
    }
};