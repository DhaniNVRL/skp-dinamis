<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'id'
        );
    }

    public function profile()
    {
        return $this->hasOne(
            UserProfile::class,
            'user_id',
            'id'
        );
    }

    public function surveySessions()
    {
        return $this->hasMany(
            SurveySession::class,
            'user_id',
            'id'
        );
    }

    public function surveySession()
    {
        return $this->hasOne(
            SurveySession::class,
            'user_id',
            'id'
        )
            ->select([
                'survey_sessions.id',
                'survey_sessions.user_id',
                'survey_sessions.activity_id',
                'survey_sessions.group_id',
                'survey_sessions.unit_id',
                'survey_sessions.current_form_id',
                'survey_sessions.status',
                'survey_sessions.started_at',
                'survey_sessions.finished_at',
                'survey_sessions.created_at',
                'survey_sessions.updated_at',
            ])
            ->latestOfMany();
    }

    public function answers()
    {
        return $this->hasMany(
            Answer::class,
            'user_id',
            'id'
        );
    }

    public function hasRole($role)
    {
        return strtolower($this->role?->name ?? '')
            === strtolower($role);
    }
}