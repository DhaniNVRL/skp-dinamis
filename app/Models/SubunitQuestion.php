<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubunitQuestion extends Model
{
    protected $fillable = [
        'subunit_id',
        'question_id'
    ];
}
