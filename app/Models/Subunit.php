<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subunit extends Model
{
    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'subunit_questions',
            'subunit_id',
            'question_id'
        );
    }
}
