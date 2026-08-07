<?php

namespace Tests\Unit;

use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionType;
use Tests\TestCase;

class HasChildPlaceholderTest extends TestCase
{
    public function test_answer_text2_is_used_as_has_child_placeholder_on_survey_and_preview(): void
    {
        $type = new QuestionType(['name' => 'Single Choice']);
        $type->id = 3;

        $option = new Option([
            'answer_text' => 'Lainnya',
            'answer_text2' => 'Tuliskan alasan lainnya',
            'has_child' => 1,
        ]);
        $option->id = 501;

        $question = new Question([
            'form_id' => 100,
            'no_header' => 'A',
            'no' => '1',
            'name' => 'Pilih jawaban',
            'questiontype_id' => 3,
        ]);
        $question->id = 401;
        $question->setRelation('questiontype', $type);
        $question->setRelation('options', collect([$option]));

        $surveyHtml = view('user.survey.forms.general-questionnaire', [
            'questions' => collect([$question]),
            'answerMap' => [],
        ])->render();

        $form = new Form(['formtype_id' => 1]);
        $form->id = 100;

        $previewHtml = view('admin.subunit.show-question.forms.general-questionnaire', [
            'form' => $form,
            'questions' => collect([$question]),
        ])->render();

        $this->assertStringContainsString(
            'placeholder="Tuliskan alasan lainnya"',
            $surveyHtml
        );
        $this->assertStringContainsString(
            'placeholder="Tuliskan alasan lainnya"',
            $previewHtml
        );
    }
}
