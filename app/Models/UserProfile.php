<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'activity_id',
        'group_id',
        'unit_id',
        'fullname',
        'email',
        'no_handphone',
    ];
    public function surveySessions()
    {
        return $this->hasMany(
            SurveySession::class,
            'user_id',
            'user_id'
        );
    }
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
}
