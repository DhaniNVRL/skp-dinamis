<?php

namespace Tests\Unit;

use App\Http\Controllers\AnswerController;
use App\Models\Form;
use App\Models\Question;
use App\Models\SubUnit;
use ReflectionMethod;
use Tests\TestCase;

class CustomerAssessmentWithoutZeroTest extends TestCase
{
    public function test_user_renderer_excludes_zero_for_the_new_form_types(): void
    {
        foreach ([15 => [5, 3], 16 => [7, 4]] as $formTypeId => [$maximum, $reasonMaximum]) {
            $form = new Form(['formtype_id' => $formTypeId]);
            $form->id = 10 + $formTypeId;

            $question = new Question([
                'form_id' => $form->id,
                'no_header' => 'A',
                'no' => '1',
                'name' => 'Kualitas layanan',
                'questiontype_id' => 2,
            ]);
            $question->id = 100 + $formTypeId;
            $question->setRelation('options', collect());

            $subunit = new SubUnit(['name' => 'Unit Uji']);
            $subunit->id = 200 + $formTypeId;

            $html = view('user.survey.forms.partials.customer-assessment', [
                'form' => $form,
                'questions' => collect([$question]),
                'subunits' => collect([$subunit]),
                'activeMapSubUnit' => [
                    $form->id.'-'.$question->id => [$subunit->id],
                ],
                'answerMap' => [],
                'maximumScale' => $maximum,
                'reasonMaximum' => $reasonMaximum,
                'includeZero' => false,
            ])->render();

            $this->assertStringContainsString('value="1"', $html);
            $this->assertStringContainsString('value="'.$maximum.'"', $html);
            $this->assertStringNotContainsString('value="0"', $html);
        }
    }

    public function test_server_validation_rejects_zero_for_the_new_form_types(): void
    {
        $controller = new AnswerController();
        $method = new ReflectionMethod($controller, 'validateCustomerAnswer');

        foreach ([15, 16] as $formTypeId) {
            $form = new Form(['formtype_id' => $formTypeId]);
            $question = new Question([
                'name' => 'Kualitas layanan',
                'questiontype_id' => 2,
            ]);
            $question->id = 300 + $formTypeId;

            $errors = [];
            $arguments = [
                $form,
                $question,
                99,
                ['importance' => '0', 'performance' => '0'],
                &$errors,
            ];
            $method->invokeArgs($controller, $arguments);

            $this->assertArrayHasKey("answers.{$question->id}.99.importance", $errors);
            $this->assertArrayHasKey("answers.{$question->id}.99.performance", $errors);
        }
    }

    public function test_original_customer_types_still_accept_zero(): void
    {
        $controller = new AnswerController();
        $method = new ReflectionMethod($controller, 'validateCustomerAnswer');

        foreach ([2, 3] as $formTypeId) {
            $form = new Form(['formtype_id' => $formTypeId]);
            $question = new Question([
                'name' => 'Kualitas layanan',
                'questiontype_id' => 2,
            ]);
            $question->id = 400 + $formTypeId;

            $errors = [];
            $arguments = [
                $form,
                $question,
                100,
                ['importance' => '0', 'performance' => '0'],
                &$errors,
            ];
            $method->invokeArgs($controller, $arguments);

            $this->assertSame([], $errors);
        }
    }
}
