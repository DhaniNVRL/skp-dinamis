<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCompetitorVisibility extends Model
{
    protected $fillable = ['unit_id', 'competitor_id', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }
}
