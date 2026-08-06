<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = [
        'name',
        'description',
    ];

    public function groups()
    {
        return $this->hasMany(
            Group::class,
            'activity_id',
            'id'
        );
    }

    public function completeProfile()
    {
        return $this->hasOne(
            CompleteProfile::class,
            'activity_id',
            'id'
        );
    }
}
