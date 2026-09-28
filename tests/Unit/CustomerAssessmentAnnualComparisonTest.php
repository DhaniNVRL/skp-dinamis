<?php

namespace Tests\Unit;

use App\Http\Controllers\AnswerController;
use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use App\Models\SubUnit;
use Illuminate\Support\ViewErrorBag;
use ReflectionMethod;
use Tests\TestCase;

class CustomerAssessmentAnnualComparisonTest extends TestCase
{
    public function test_comparison_is_optional_configuration_not_a_question_type(): void
    {
        foreach ([
            'admin.units.question.templates.forms.customer-assessment-1-5',
            'admin.units.question.templates.forms.customer-assessment-1-7',
            'admin.units.question.templates.forms.customer-assessment-without-zero',
        ] as $view) {
            $this->assertStringNotContainsString('value="9"', view($view)->render());
        }

        $row = view('admin.units.question.templates.create-question-row')->render();
        $this->assertStringNotContainsString('data-question-field="comparison_enabled"', $row);

        $modal = view('admin.units.question.modals.comparison')->render();
        $this->assertStringContainsString('Input Pembanding', $modal);
        $this->assertStringContainsString('name="comparison_prompt"', $modal);
        $this->assertStringContainsString('name="comparison_options"', $modal);
        $this->assertStringContainsString('id="comparisonOptionRows"', $modal);
        $this->assertStringContainsString('id="addComparisonOption"', $modal);
        $this->assertStringContainsString('id="comparisonOptionRowTemplate"', $modal);
    }

    public function test_user_renderer_displays_comparison_alongside_existing_reason_options(): void
    {
        $form = new Form(['formtype_id' => 2]);
        $form->id = 20;
        $question = new Question([
            'form_id' => 20,
            'no_header' => 'A',
            'no' => '1',
            'name' => 'Kualitas layanan',
            'questiontype_id' => 4,
            'comparison_enabled' => true,
            'comparison_prompt' => 'Bagaimana kondisi tahun ini dibanding tahun lalu?',
            'comparison_options' => ['Lebih baik tahun 2025', 'Tahun ini lebih baik daripada 2025', 'Sama saja'],
        ]);
        $question->id = 90;
        $reason = new Option(['answer_text' => 'Kurang cepat', 'has_child' => 0]);
        $reason->id = 901;
        $question->setRelation('options', collect([$reason]));
        $subunit = new SubUnit(['name' => 'Unit Uji']);
        $subunit->id = 30;

        $html = view('user.survey.forms.partials.customer-assessment', [
            'form' => $form,
            'questions' => collect([$question]),
            'subunits' => collect([$subunit]),
            'activeMapSubUnit' => ['20-90' => [30]],
            'answerMap' => [90 => [30 => [[
                'importance' => '5', 'performance' => '4',
                'comparison' => 'Sama saja',
            ]]]],
            'maximumScale' => 5,
            'reasonMaximum' => 3,
            'includeZero' => true,
            'errors' => new ViewErrorBag(),
        ])->render();

        $this->assertStringContainsString('Kurang cepat', $html);
        $this->assertStringContainsString('Bagaimana kondisi tahun ini dibanding tahun lalu?', $html);
        $this->assertStringContainsString('Tahun ini lebih baik daripada 2025', $html);
        $this->assertStringContainsString('answers[90][30][comparison]', $html);
        $this->assertMatchesRegularExpression('/value="Sama saja"[^>]*checked/', $html);
    }

    public function test_server_requires_configured_comparison_only_when_enabled(): void
    {
        $form = new Form(['formtype_id' => 16]);
        $question = new Question([
            'name' => 'Kualitas layanan',
            'questiontype_id' => 2,
            'comparison_enabled' => true,
            'comparison_prompt' => 'Bandingkan tahun ini',
            'comparison_options' => ['Lebih baik', 'Sama saja'],
        ]);
        $question->id = 91;
        $question->setRelation('options', collect());
        $controller = new AnswerController();
        $method = new ReflectionMethod($controller, 'validateCustomerAnswer');

        $errors = [];
        $arguments = [$form, $question, 31, [
            'importance' => '5', 'performance' => '5', 'comparison' => 'Tidak valid',
        ], &$errors];
        $method->invokeArgs($controller, $arguments);
        $this->assertArrayHasKey('answers.91.31.comparison', $errors);

        $errors = [];
        $arguments[3]['comparison'] = 'Sama saja';
        $arguments[4] = &$errors;
        $method->invokeArgs($controller, $arguments);
        $this->assertSame([], $errors);

        $question->comparison_enabled = false;
        $errors = [];
        $arguments[3] = ['importance' => '5', 'performance' => '5'];
        $arguments[4] = &$errors;
        $method->invokeArgs($controller, $arguments);
        $this->assertSame([], $errors);
    }
}
