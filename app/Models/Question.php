<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'group_id',
        'form_id',
        'no_header',
        'no',
        'name',
        'questiontype_id',
        'comparison_enabled',
        'comparison_prompt',
        'comparison_options',
    ];

    protected $casts = [
        'comparison_enabled' => 'boolean',
        'comparison_options' => 'array',
    ];

    // Urutan baku pertanyaan pada seluruh halaman admin dan survei.
    // Kolom no_header dan no bertipe varchar, tetapi harus ditampilkan
    // secara natural (C1, C2, ..., C10), bukan secara leksikografis
    // (C1, C10, C2). Apabila keduanya sama, tipe judul ditempatkan sebelum
    // pertanyaan biasa. Khusus form Penilaian Pelanggan, pertanyaan dengan
    // dua indikator ditampilkan sebelum satu indikator, lalu textarea.
    // ID menjadi penentu urutan terakhir.
    public function scopeInDisplayOrder(Builder $query): Builder
    {
        $driver = $query->getConnection()->getDriverName();

        $query
            ->orderByRaw('LENGTH(COALESCE(questions.no_header, \'\'))')
            ->orderBy('questions.no_header')
            ->orderByRaw(
                'CASE
                    WHEN questions.questiontype_id = 10
                        AND EXISTS (
                            SELECT 1 FROM forms
                            WHERE forms.id = questions.form_id
                              AND forms.formtype_id = 1
                        ) THEN 0
                    WHEN questions.questiontype_id = 1
                        AND EXISTS (
                            SELECT 1 FROM forms
                            WHERE forms.id = questions.form_id
                              AND forms.formtype_id <> 1
                        ) THEN 0
                    ELSE 1
                END'
            )
            ->orderByRaw(
                'CASE
                    WHEN EXISTS (
                        SELECT 1 FROM forms
                        WHERE forms.id = questions.form_id
                          AND forms.formtype_id IN (2, 3, 15, 16)
                    ) AND questions.questiontype_id IN (2, 3, 4, 7, 8) THEN 0
                    WHEN EXISTS (
                        SELECT 1 FROM forms
                        WHERE forms.id = questions.form_id
                          AND forms.formtype_id IN (2, 3, 15, 16)
                    ) AND questions.questiontype_id = 5 THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM forms
                        WHERE forms.id = questions.form_id
                          AND forms.formtype_id IN (2, 3, 15, 16)
                    ) AND questions.questiontype_id = 6 THEN 2
                    ELSE 0
                END'
            );

        if ($driver === 'sqlite') {
            $query
                ->orderByRaw('CAST(COALESCE(questions.no, \'0\') AS INTEGER)')
                ->orderByRaw(
                    'CASE
                        WHEN INSTR(COALESCE(questions.no, \'\'), \'.\') > 0
                        THEN CAST(SUBSTR(questions.no, INSTR(questions.no, \'.\') + 1) AS INTEGER)
                        ELSE 0
                    END'
                );
        } else {
            $query
                ->orderByRaw('CAST(COALESCE(questions.no, \'0\') AS UNSIGNED)')
                ->orderByRaw(
                    'CASE
                        WHEN LOCATE(\'.\', COALESCE(questions.no, \'\')) > 0
                        THEN CAST(SUBSTRING_INDEX(questions.no, \'.\', -1) AS UNSIGNED)
                        ELSE 0
                    END'
                );
        }

        return $query
            ->orderByRaw('LOWER(COALESCE(questions.no, \'\'))')
            ->orderBy('questions.id');
    }

    public static function displayTypePriority(
        int $formTypeId,
        int $questionTypeId
    ): int {
        if (! in_array($formTypeId, [2, 3, 15, 16], true)) {
            return 0;
        }

        return match (true) {
            $questionTypeId === 1 => 0,
            in_array($questionTypeId, [2, 3, 4, 7, 8], true) => 1,
            $questionTypeId === 5 => 2,
            $questionTypeId === 6 => 3,
            default => 1,
        };
    }

    public function questiontype()
    {
        return $this->belongsTo(QuestionType::class, 'questiontype_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function form()
    {
        return $this->belongsTo(
            Form::class,
            'form_id'
        );
    }

    public function options()
    {
        return $this->hasMany(
            Option::class,
            'question_id'
        )->orderBy('no');
    }

    public function subunits()
    {
        return $this->belongsToMany(
            SubUnit::class,
            'subunit_questions',
            'question_id',
            'subunit_id'
        );
    }

    public function subUnitQuestions()
    {
        return $this->hasMany(SubUnitQuestion::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
