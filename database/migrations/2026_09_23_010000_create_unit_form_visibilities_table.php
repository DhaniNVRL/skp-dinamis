<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_form_visibilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['unit_id', 'form_id'], 'unit_form_visibility_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_form_visibilities');
    }
};
