<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    public const TITLE_ONLY_NAME = 'Judul (Tanpa Jawaban)';

    public const TITLE_ONLY_ID = 10;

    protected $table = 'question_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function isTitleOnly(): bool
    {
        return (int) $this->getKey() === self::TITLE_ONLY_ID;
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'questiontype_id');
    }
}
