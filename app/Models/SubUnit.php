<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    protected $table = 'subunits';

    protected $fillable = [
        'unit_id',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo(
            Unit::class,
            'unit_id',
            'id'
        );
    }

    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'subunit_questions',
            'subunit_id',
            'question_id'
        )
            ->withPivot('form_id');
    }

    public function subUnitQuestions()
    {
        return $this->hasMany(
            SubUnitQuestion::class,
            'subunit_id'
        );
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'subunit_id');
    }
}
