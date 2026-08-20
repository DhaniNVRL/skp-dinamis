<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespondentCompetitor extends Model
{
    protected $fillable = ['user_id', 'activity_id', 'form_id', 'position', 'name'];

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('question_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
