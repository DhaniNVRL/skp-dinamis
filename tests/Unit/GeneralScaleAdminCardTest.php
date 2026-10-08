<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeneralScaleAdminCardTest extends TestCase
{
    #[Test]
    public function scale_card_displays_question_identity_and_edit_actions_above_scale(): void
    {
        $view = file_get_contents(
            resource_path('views/admin/units/question/partials/forms/general-questionnaire/options/scale.blade.php')
        );

        $questionPosition = strpos($view, '{{ $question->name }}');
        $scalePosition = strpos($view, '@foreach (range($scaleMinimum, $scaleMaximum) as $value)');

        $this->assertNotFalse($questionPosition);
        $this->assertNotFalse($scalePosition);
        $this->assertLessThan($scalePosition, $questionPosition);
        $this->assertStringContainsString('question-action', $view);
        $this->assertStringContainsString('{{ $question->no_header }}{{ $question->no }}', $view);
    }
}
