<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'user_id',
        'form_id',
        'question_id',
        'subunit_id',
        'competitor_id',
        'answer',
    ];

    protected function answer(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! is_string($value)) {
                    return $value;
                }

                $decoded = json_decode($value, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return $value;
                }

                // Kompatibilitas data lama yang sempat tersimpan sebagai JSON
                // berlapis karena json_encode manual digabung dengan cast array.
                if (is_string($decoded)) {
                    $nested = json_decode($decoded, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $nested;
                    }
                }

                return $decoded;
            },
            set: fn ($value) => is_string($value)
                ? $value
                : json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                ),
        );
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function subunit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function competitor()
    {
        return $this->belongsTo(Competitor::class);
    }
}
