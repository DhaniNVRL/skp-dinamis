<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
     protected $fillable = [
        'id_groups',       
        'form_id',
        'no_header',
        'no',
        'name',
        'id_questiontypes',
    ];

    public function questiontype()
    {
        return $this->belongsTo(QuestionType::class, 'id_questiontypes', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'id_groups', 'id');
    }
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id'); // FK ke forms
    }
   public function options()
    {
        return $this->hasMany(Option::class, 'question_id')
                    ->orderBy('no', 'asc');
    }
    public function subUnits()
    {
        return $this->belongsToMany(
            SubUnit::class,
            'subunit_questions',
            'question_id',
            'subunit_id'
        );
    }
}
