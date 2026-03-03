<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'id_activities',
        'name',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
