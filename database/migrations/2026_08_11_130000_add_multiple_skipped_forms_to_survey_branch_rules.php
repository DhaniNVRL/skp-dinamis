<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_branch_rule_skipped_forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('survey_branch_rule_id');
            $table->unsignedBigInteger('form_id');
            $table->timestamps();
            $table->unique(['survey_branch_rule_id', 'form_id'], 'branch_rule_skipped_form_unique');
            $table->foreign('survey_branch_rule_id', 'branch_skip_form_rule_fk')
                ->references('id')->on('survey_branch_rules')->cascadeOnDelete();
            $table->foreign('form_id', 'branch_skip_form_fk')
                ->references('id')->on('forms')->cascadeOnDelete();
        });

        DB::table('survey_branch_rules')
            ->whereNotNull('skip_form_id')
            ->orderBy('id')
            ->each(function (object $rule): void {
                DB::table('survey_branch_rule_skipped_forms')->insertOrIgnore([
                    'survey_branch_rule_id' => $rule->id,
                    'form_id' => $rule->skip_form_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_branch_rule_skipped_forms');
    }
};
