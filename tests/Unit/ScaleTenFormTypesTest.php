<?php

namespace Tests\Unit;

use App\Http\Controllers\AnswerController;
use App\Models\Form;
use App\Models\Question;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class ScaleTenFormTypesTest extends TestCase
{
    #[Test]
    public function customer_scale_ten_uses_shared_customer_assessment_with_zero_and_reasons(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/customer-assessment-1-10.blade.php')
        );

        $this->assertStringContainsString("'maximumScale' => 10", $view);
        $this->assertStringContainsString("'reasonMaximum' => 5", $view);
        $this->assertStringContainsString("'includeZero' => true", $view);
    }

    #[Test]
    public function engagement_scale_ten_uses_shared_engagement_assessment(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/engagement-assessment-1-10.blade.php')
        );

        $this->assertStringContainsString("'maximumScale' => 10", $view);
    }

    #[Test]
    public function customer_scale_ten_without_zero_uses_only_values_one_through_ten(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/customer-assessment-1-10-without-zero.blade.php')
        );

        $this->assertStringContainsString("'maximumScale' => 10", $view);
        $this->assertStringContainsString("'reasonMaximum' => 5", $view);
        $this->assertStringContainsString("'includeZero' => false", $view);
    }

    #[Test]
    public function survey_router_supports_both_scale_ten_form_types(): void
    {
        $view = file_get_contents(resource_path('views/user/survey/partials/merged-form-section.blade.php'));

        $this->assertStringContainsString('@case(17)', $view);
        $this->assertStringContainsString('customer-assessment-1-10', $view);
        $this->assertStringContainsString('@case(19)', $view);
        $this->assertStringContainsString('customer-assessment-1-10-without-zero', $view);
        $this->assertStringContainsString('@case(18)', $view);
        $this->assertStringContainsString('engagement-assessment-1-10', $view);
    }

    #[Test]
    public function customer_scale_ten_without_zero_rejects_zero_and_accepts_one_through_ten(): void
    {
        $form = new Form(['formtype_id' => 19]);
        $question = new Question(['questiontype_id' => 2, 'name' => 'Pelayanan']);
        $question->id = 99;
        $method = new ReflectionMethod(AnswerController::class, 'validateCustomerAnswer');

        $validErrors = [];
        $validArguments = [$form, $question, 1, ['importance' => 1, 'performance' => 10], &$validErrors];
        $method->invokeArgs(app(AnswerController::class), $validArguments);
        $this->assertSame([], $validErrors);

        $invalidErrors = [];
        $invalidArguments = [$form, $question, 1, ['importance' => 0, 'performance' => 0], &$invalidErrors];
        $method->invokeArgs(app(AnswerController::class), $invalidArguments);
        $this->assertArrayHasKey('answers.99.1.importance', $invalidErrors);
        $this->assertArrayHasKey('answers.99.1.performance', $invalidErrors);
    }
}
