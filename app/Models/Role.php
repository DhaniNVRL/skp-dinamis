<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    // Kalau tidak pakai timestamps
    public $timestamps = false;

    protected $fillable = [
        'id','name',
    ];

    // Opsional: relasi balik ke users
    public function users()
    {
        return $this->hasMany(User::class, 'id_roles', 'id');
    }
}
