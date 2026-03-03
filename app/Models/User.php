<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Tambahkan ini

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'id_roles',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_roles', 'id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity', 'id');
    }
    
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
