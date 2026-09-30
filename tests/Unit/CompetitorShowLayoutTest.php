<?php

namespace Tests\Unit;

use App\Models\Competitor;
use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionType;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuestionController;
use ReflectionMethod;
use Tests\TestCase;

class CompetitorShowLayoutTest extends TestCase
{
    public function test_competitors_are_rendered_vertically_with_numbered_radio_controls(): void
    {
        $form = new Form(['formtype_id' => 11]);
        $form->id = 100;

        $question = new Question([
            'form_id' => $form->id,
            'no_header' => 'E',
            'no' => '1',
            'name' => 'Kesesuaian karakteristik operasi',
            'questiontype_id' => 2,
        ]);
        $question->id = 200;

        $firstCompetitor = new Competitor(['name' => 'IPP']);
        $firstCompetitor->id = 301;
        $secondCompetitor = new Competitor(['name' => 'PLN IP']);
        $secondCompetitor->id = 302;

        $html = view('admin.subunit.show-question.forms.partials.competitor-assessment', [
            'form' => $form,
            'questions' => collect([$question]),
            'competitors' => collect([$firstCompetitor, $secondCompetitor]),
            'maximum' => 5,
        ])->render();

        $this->assertStringContainsString('data-competitor-list', $html);
        $this->assertSame(2, substr_count($html, 'data-competitor-row'));
        $this->assertStringNotContainsString('<table', $html);
        $this->assertStringContainsString('name="competitor_200_301"', $html);
        $this->assertStringContainsString('value="5"', $html);
        $this->assertStringContainsString('value="0"', $html);
        $this->assertStringContainsString('IPP', $html);
        $this->assertStringContainsString('PLN IP', $html);
    }

    public function test_competitor_name_textarea_is_available_in_crud_preview_and_user_form(): void
    {
        $form = new Form(['formtype_id' => 11]);
        $form->id = 100;

        $question = new Question([
            'form_id' => $form->id,
            'no_header' => 'E',
            'no' => '5',
            'name' => 'Sebutkan nama kompetitor yang dimaksud',
            'questiontype_id' => 3,
        ]);
        $question->id = 205;

        $questionTypesMethod = new ReflectionMethod(QuestionController::class, 'getQuestionTypesByForm');
        $questionTypes = $questionTypesMethod->invoke(new QuestionController(), $form);

        $this->assertSame(
            'Nama Kompetitor (Textarea)',
            $questionTypes->firstWhere('id', 3)['name']
        );

        $adminHtml = view('admin.subunit.show-question.forms.partials.competitor-assessment', [
            'form' => $form,
            'questions' => collect([$question]),
            'competitors' => collect(),
            'maximum' => 5,
        ])->render();
        $this->assertStringContainsString('Sebutkan nama kompetitor...', $adminHtml);

        $userHtml = view('user.survey.forms.partials.competitor-fields', [
            'form' => $form,
            'questions' => collect([$question]),
            'competitors' => collect(),
            'answerMap' => [],
            'scaleValues' => [1, 2, 3, 4, 5, 0],
        ])->render();
        $this->assertStringContainsString('name="answers[205][value]"', $userHtml);
        $this->assertStringContainsString('maxlength="5000"', $userHtml);
    }

    public function test_competitor_name_textarea_is_validated_as_one_global_answer(): void
    {
        $form = new Form(['formtype_id' => 11]);
        $form->id = 100;

        $question = new Question([
            'form_id' => $form->id,
            'name' => 'Nama kompetitor',
            'questiontype_id' => 3,
        ]);
        $question->id = 205;
        $questionType = new QuestionType(['name' => 'Nama Kompetitor (Textarea)']);
        $questionType->id = 3;
        $question->setRelation('questiontype', $questionType);

        $method = new ReflectionMethod(AnswerController::class, 'validateAnswers');
        $controller = new AnswerController();

        $this->assertSame([], $method->invoke(
            $controller,
            $form,
            collect([$question]),
            collect(),
            collect([301, 302]),
            [205 => ['value' => 'Perusahaan Energi Contoh']]
        ));

        $errors = $method->invoke(
            $controller,
            $form,
            collect([$question]),
            collect(),
            collect([301, 302]),
            [205 => ['value' => '']]
        );

        $this->assertArrayHasKey('answers.205.value', $errors);
    }
}
