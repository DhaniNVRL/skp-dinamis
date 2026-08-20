<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('form_types')->updateOrInsert(
            ['id' => 14],
            [
                'name' => 'Form Pembanding Dinamis',
                'description' => 'Kompetitor ditentukan responden (Skala 1-7)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (! Schema::hasTable('respondent_competitors')) {
            Schema::create('respondent_competitors', function (Blueprint $table): void {
                $table->engine = 'InnoDB';
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('activity_id')->nullable()->constrained('activities')->cascadeOnDelete();
                $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
                $table->unsignedInteger('position');
                $table->string('name');
                $table->timestamps();
                $table->unique(['user_id', 'form_id', 'position'], 'respondent_competitor_position_unique');
            });
        }

        if (! Schema::hasColumn('answers', 'respondent_competitor_id')) {
            Schema::table('answers', function (Blueprint $table): void {
                $table->foreignId('respondent_competitor_id')
                    ->nullable()
                    ->after('competitor_id')
                    ->constrained('respondent_competitors')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('answers', 'respondent_competitor_id')) {
            Schema::table('answers', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('respondent_competitor_id');
            });
        }

        Schema::dropIfExists('respondent_competitors');
        DB::table('form_types')->where('id', 14)->delete();
    }
};

