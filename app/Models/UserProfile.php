<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
     use Notifiable, HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'group_id',
        'unit_id',
        'fullname',
        'no_handphone',
        'email',
    ];
        public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
        
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
