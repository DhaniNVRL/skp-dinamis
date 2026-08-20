<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_branch_rules', function (Blueprint $table): void {
            $table->dropUnique(['group_id', 'parent_question_id']);
            $table->unique(
                ['group_id', 'parent_question_id', 'affirmative_option_id'],
                'branch_rule_trigger_unique'
            );
        });

        Schema::create('survey_branch_rule_skipped_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('survey_branch_rule_id');
            $table->unsignedBigInteger('question_id');
            $table->timestamps();
            $table->unique(
                ['survey_branch_rule_id', 'question_id'],
                'branch_rule_skipped_question_unique'
            );
            $table->foreign('survey_branch_rule_id', 'branch_skip_rule_fk')
                ->references('id')->on('survey_branch_rules')->cascadeOnDelete();
            $table->foreign('question_id', 'branch_skip_question_fk')
                ->references('id')->on('questions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_branch_rule_skipped_questions');

        Schema::table('survey_branch_rules', function (Blueprint $table): void {
            $table->dropUnique('branch_rule_trigger_unique');
            $table->unique(['group_id', 'parent_question_id']);
        });
    }
};
