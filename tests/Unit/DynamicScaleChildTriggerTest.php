<?php

namespace Tests\Unit;

use App\Http\Controllers\QuestionController;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class DynamicScaleChildTriggerTest extends TestCase
{
    #[Test]
    public function trigger_values_are_normalized_and_deduplicated(): void
    {
        $method = new ReflectionMethod(QuestionController::class, 'parseScaleChildValues');
        $values = $method->invoke(new QuestionController(), '3, 1, 3, 2');

        $this->assertSame([1, 2, 3], $values);
    }

    #[Test]
    public function survey_script_controls_the_dynamic_scale_child(): void
    {
        $script = file_get_contents(resource_path('js/user/survey.js'));
        $controller = file_get_contents(app_path('Http/Controllers/AnswerController.php'));
        $exporter = file_get_contents(app_path('Services/RawDataExportService.php'));

        $this->assertStringContainsString('initializeDynamicScaleChildren(page)', $script);
        $this->assertStringContainsString('triggers.includes(String(selected.value))', $script);
        $this->assertStringContainsString("Arr::get(\$payload, 'child.scale')", $controller);
        $this->assertStringContainsString('scale_child_values', $exporter);
    }
}
