<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';
    
    protected $fillable = [
        'group_id',
        'no_urut',
        'name',
        'formtype_id',
    ];

    public function formtype()
    {
        return $this->belongsTo(
            FormType::class,
            'formtype_id',
            'id'
        );
    }

    public function questions()
    {
        return $this->hasMany(
            Question::class,
            'form_id',
            'id'
        )->inDisplayOrder();
    }

    public function group()
    {
        return $this->belongsTo(
            Group::class,
            'group_id',
            'id'
        );
    }

    public function description()
    {
        return $this->hasOne(
            Description::class,
            'form_id',
            'id'
        );
    }

    public function competitors()
    {
        return $this->hasMany(Competitor::class, 'form_id');
    }

    public function respondentCompetitors()
    {
        return $this->hasMany(RespondentCompetitor::class);
    }

    public function subUnitQuestions()
    {
        return $this->hasMany(SubUnitQuestion::class, 'form_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'form_id');
    }
}
