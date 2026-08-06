<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'group_id',
        'form_id',
        'no_header',
        'no',
        'name',
        'questiontype_id',
    ];

    public function questiontype()
    {
        return $this->belongsTo(QuestionType::class, 'questiontype_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function form()
    {
        return $this->belongsTo(
            Form::class,
            'form_id'
        );
    }

    public function options()
    {
        return $this->hasMany(
            Option::class,
            'question_id'
        )->orderBy('no');
    }

    public function subunits()
    {
        return $this->belongsToMany(
            Subunit::class,
            'subunit_questions',
            'question_id',
            'subunit_id'
        );
    }

    public function subUnitQuestions()
    {
        return $this->hasMany(SubunitQuestion::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
