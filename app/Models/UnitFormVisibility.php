<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitFormVisibility extends Model
{
    protected $fillable = [
        'unit_id',
        'form_id',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
