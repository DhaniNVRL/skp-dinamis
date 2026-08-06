<?php

namespace App\Exports;

use App\Exports\QuestionTemplate\InputOptionsSheet;
use App\Exports\QuestionTemplate\InputQuestionsSheet;
use App\Exports\QuestionTemplate\InstructionsSheet;
use App\Exports\QuestionTemplate\MasterFormsSheet;
use App\Exports\QuestionTemplate\MasterQuestionTypesSheet;
use App\Models\Form;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuestionTemplateExport implements WithMultipleSheets
{
    /**
     * Form tujuan template.
     */
    protected Form $form;

    /**
     * Daftar tipe pertanyaan yang diperbolehkan.
     */
    protected Collection $questionTypes;

    /**
     * Constructor.
     */
    public function __construct(
        Form $form,
        Collection $questionTypes
    ) {
        $this->form = $form;
        $this->questionTypes = $questionTypes;
    }

    /**
     * Daftar sheet yang akan dibuat.
     */
    public function sheets(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Sheet input pertanyaan
            |--------------------------------------------------------------------------
            */
            new InputQuestionsSheet(
                $this->form,
                $this->questionTypes
            ),

            /*
            |--------------------------------------------------------------------------
            | Sheet input option
            |--------------------------------------------------------------------------
            */
            new InputOptionsSheet(
                $this->form
            ),

            /*
            |--------------------------------------------------------------------------
            | Sheet referensi form
            |--------------------------------------------------------------------------
            */
            new MasterFormsSheet(
                $this->form
            ),

            /*
            |--------------------------------------------------------------------------
            | Sheet referensi tipe pertanyaan
            |--------------------------------------------------------------------------
            */
            new MasterQuestionTypesSheet(
                $this->form,
                $this->questionTypes
            ),

            /*
            |--------------------------------------------------------------------------
            | Sheet petunjuk
            |--------------------------------------------------------------------------
            */
            new InstructionsSheet(
                $this->form
            ),
        ];
    }
}