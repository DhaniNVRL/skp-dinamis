<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_competitor_visibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('competitor_id')->constrained('competitors')->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->unique(['unit_id', 'competitor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_competitor_visibilities');
    }
};
