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
    ];

    /**
     * Urutan baku pertanyaan pada seluruh halaman admin dan survei.
     *
     * Kolom no_header dan no bertipe varchar, tetapi harus ditampilkan
     * secara natural (C1, C2, ..., C10), bukan secara leksikografis
     * (C1, C10, C2). Apabila keduanya sama, tipe judul ditempatkan sebelum
     * pertanyaan biasa, kemudian ID menjadi penentu urutan terakhir.
     */
    public function scopeInDisplayOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw('LENGTH(COALESCE(questions.no_header, \'\'))')
            ->orderBy('questions.no_header')
            ->orderByRaw('LENGTH(COALESCE(questions.no, \'\'))')
            ->orderBy('questions.no')
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
            ->orderBy('questions.id');
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
