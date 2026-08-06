<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompleteProfile extends Model
{
    protected $fillable = [
        'activity_id',
        'group_question',
        'unit_question',
    ];


    public function activity()
    {
        return $this->belongsTo(
            Activity::class,
            'activity_id',
            'id'
        );
    }
}
