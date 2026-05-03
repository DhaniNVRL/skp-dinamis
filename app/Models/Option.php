<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'id_groups',
        'question_id',
        'no',
        'answer_text',
        'has_child',
    ];
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
