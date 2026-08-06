<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerValue extends Model
{
    protected $fillable = [
        'answer_id',
        'order',
        'value',
    ];
}
