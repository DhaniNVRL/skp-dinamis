<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyDraft extends Model
{
    protected $fillable = ['user_id', 'form_id', 'payload'];

    protected $casts = ['payload' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
