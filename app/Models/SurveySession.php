<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveySession extends Model
{
    protected $fillable = [
        'user_id',
        'activity_id',
        'group_id',
        'unit_id',
        'current_form_id',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function activity()
    {
        return $this->belongsTo(
            Activity::class,
            'activity_id',
            'id'
        );
    }

    public function group()
    {
        return $this->belongsTo(
            Group::class,
            'group_id',
            'id'
        );
    }

     public function unit()
    {
        return $this->belongsTo(
            Unit::class,
            'unit_id',
            'id'
        );
    }

    public function currentForm()
    {
        return $this->belongsTo(
            Form::class,
            'current_form_id',
            'id'
        );
    }
}