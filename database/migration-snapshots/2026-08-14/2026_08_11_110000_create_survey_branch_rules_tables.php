<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_branch_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('parent_question_id')->constrained('questions')->cascadeOnDelete();
            $table->foreignId('affirmative_option_id')->constrained('options')->cascadeOnDelete();
            $table->foreignId('skip_form_id')->nullable()->constrained('forms')->nullOnDelete();
            $table->timestamps();
            $table->unique(['group_id', 'parent_question_id', 'affirmative_option_id'], 'branch_rule_trigger_unique');
        });

        Schema::create('survey_branch_rule_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('survey_branch_rule_id')->constrained('survey_branch_rules')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['survey_branch_rule_id', 'question_id'], 'branch_rule_question_unique');
        });

        Schema::create('survey_branch_rule_skipped_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('survey_branch_rule_id')->constrained('survey_branch_rules')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['survey_branch_rule_id', 'question_id'], 'branch_rule_skipped_question_unique');
        });

        Schema::create('survey_branch_rule_skipped_forms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('survey_branch_rule_id')->constrained('survey_branch_rules')->cascadeOnDelete();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['survey_branch_rule_id', 'form_id'], 'branch_rule_skipped_form_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_branch_rule_skipped_forms');
        Schema::dropIfExists('survey_branch_rule_skipped_questions');
        Schema::dropIfExists('survey_branch_rule_questions');
        Schema::dropIfExists('survey_branch_rules');
    }
};