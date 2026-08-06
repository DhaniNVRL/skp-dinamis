<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'activity_id',
        'name',
    ];

    public function activity()
    {
        return $this->belongsTo(
            Activity::class,
            'activity_id',
            'id'
        );
    }

    public function units()
    {
        return $this->hasMany(
            Unit::class,
            'group_id',
            'id'
        );
    }
    
    public function forms()
    {
        return $this->hasMany(Form::class, 'group_id');
    }

    public function completeProfiles()
    {
        return $this->hasManyThrough(
            CompleteProfile::class,
            Activity::class,
            'id',
            'activity_id',
            'activity_id',
            'id'
        );
    }
}
