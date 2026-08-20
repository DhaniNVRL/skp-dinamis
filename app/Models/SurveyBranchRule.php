<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SurveyBranchRule extends Model
{
    protected $fillable = [
        'group_id',
        'parent_question_id',
        'affirmative_option_id',
        'skip_form_id',
    ];
    protected static function booted(): void
    {
        static::deleting(function (SurveyBranchRule $rule): void {
            // Tetap aman untuk database lama tanpa ON DELETE CASCADE/MyISAM.
            foreach ([
                'survey_branch_rule_questions',
                'survey_branch_rule_skipped_questions',
                'survey_branch_rule_skipped_forms',
            ] as $table) {
                DB::table($table)
                    ->where('survey_branch_rule_id', $rule->getKey())
                    ->delete();
            }
        });
    }

    public function parentQuestion()
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    public function affirmativeOption()
    {
        return $this->belongsTo(Option::class, 'affirmative_option_id');
    }

    public function skipForm()
    {
        return $this->belongsTo(Form::class, 'skip_form_id');
    }

    public function dependentQuestions()
    {
        return $this->belongsToMany(
            Question::class,
            'survey_branch_rule_questions',
            'survey_branch_rule_id',
            'question_id'
        );
    }
    public function skippedQuestions()
    {
        return $this->belongsToMany(
            Question::class,
            'survey_branch_rule_skipped_questions',
            'survey_branch_rule_id',
            'question_id'
        );
    }
    public function skippedForms()
    {
        return $this->belongsToMany(
            Form::class,
            'survey_branch_rule_skipped_forms',
            'survey_branch_rule_id',
            'form_id'
        );
    }
}