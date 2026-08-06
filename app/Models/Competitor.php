<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $fillable = [
        'group_id',
        'form_id',
        'name',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}