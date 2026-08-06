<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';

    protected $primaryKey = 'id';

    protected $fillable = [
        'group_id',
        'name',
    ];
    
    public function group()
    {
        return $this->belongsTo(
            Group::class,
            'group_id',
            'id'
        );
    }

    public function subunits()
    {
        return $this->hasMany(
            SubUnit::class,
            'unit_id',
            'id'
        );
    }
}
