<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'no',
        'answer_text',
        'answer_text2',
        'has_child',
    ];

    protected $casts = [
        'has_child' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(
            Question::class,
            'question_id'
        );
    }
}