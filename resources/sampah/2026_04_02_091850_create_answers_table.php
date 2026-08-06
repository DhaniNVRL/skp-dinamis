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

            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_form')->constrained('forms')->onDelete('cascade');
            $table->foreignId('id_question')->constrained('questions')->onDelete('cascade');

            $table->foreignId('id_subunit')->nullable()->constrained('subunits');

            // 🔥 inti fleksibilitas sistem
            $table->json('answer_json');

            // 🔥 tracking step form (NEXT system)
            $table->integer('step_index')->default(0);

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
