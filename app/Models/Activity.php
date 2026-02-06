<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Activity extends Model
{
    protected $table = 'activities';

    // Kalau kamu pakai $timestamps = false, pastikan tabel memang tidak pakai created_at/updated_at
    // public $timestamps = false;

    protected $fillable = ['name', 'description'];

    public function groups()
    {
        return $this->hasMany(Group::class, 'activity_id');
    }
}
