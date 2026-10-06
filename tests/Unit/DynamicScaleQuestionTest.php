<?php

namespace Tests\Unit;

use App\Models\Question;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DynamicScaleQuestionTest extends TestCase
{
    #[Test]
    public function dynamic_scale_configuration_is_cast_and_fillable(): void
    {
        $question = new Question([
            'questiontype_id' => 11,
            'scale_min' => '1',
            'scale_max' => '12',
            'scale_left_label' => 'Tidak Bersedia',
            'scale_right_label' => 'Bersedia',
        ]);

        $this->assertSame(11, (int) $question->questiontype_id);
        $this->assertSame(1, $question->scale_min);
        $this->assertSame(12, $question->scale_max);
        $this->assertSame('Tidak Bersedia', $question->scale_left_label);
        $this->assertSame('Bersedia', $question->scale_right_label);
    }

    #[Test]
    public function user_view_renders_a_horizontal_dynamic_scale(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/general-questionnaire.blade.php')
        );

        $this->assertStringContainsString('$questionTypeId === 11', $view);
        $this->assertStringContainsString('range($scaleMinimum, $scaleMaximum)', $view);
        $this->assertStringContainsString('peer-checked:bg-violet-600', $view);
    }
}
