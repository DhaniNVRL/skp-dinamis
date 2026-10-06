<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedFeedbackPhrase extends Model
{
    protected $fillable = [
        'phrase',
        'normalized_phrase',
        'normalized_hash',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
