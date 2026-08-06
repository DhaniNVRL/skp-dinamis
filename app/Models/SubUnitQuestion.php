<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubUnitQuestion extends Model
{
    protected $table = 'subunit_questions';

    protected $fillable = [
        'question_id',
        'subunit_id',
        'form_id',
    ];

    public function subunit()
    {
        return $this->belongsTo(
            SubUnit::class,
            'subunit_id',
            'id'
        );
    }

    public function question()
    {
        return $this->belongsTo(
            Question::class,
            'question_id',
            'id'
        );
    }

    public function form()
    {
        return $this->belongsTo(
            Form::class,
            'form_id',
            'id'
        );
    }
}