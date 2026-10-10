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
            'scale_include_zero' => true,
            'scale_child_values' => [1, 2, 3],
            'scale_left_label' => 'Tidak Bersedia',
            'scale_right_label' => 'Bersedia',
        ]);

        $this->assertSame(11, (int) $question->questiontype_id);
        $this->assertSame(1, $question->scale_min);
        $this->assertSame(12, $question->scale_max);
        $this->assertTrue($question->scale_include_zero);
        $this->assertSame([1, 2, 3], $question->scale_child_values);
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
        $this->assertStringContainsString('$question->scale_include_zero', $view);
        $this->assertStringContainsString('aria-hidden="true">|</span>', $view);
        $this->assertStringContainsString('value="0"', $view);
        $this->assertStringContainsString('data-dynamic-scale', $view);
        $this->assertStringContainsString('[child][scale]', $view);
        $this->assertStringContainsString('peer-checked:bg-violet-600', $view);
    }

    #[Test]
    public function admin_create_and_edit_forms_expose_the_zero_option(): void
    {
        $create = file_get_contents(resource_path(
            'views/admin/units/question/templates/create-question-row.blade.php'
        ));
        $edit = file_get_contents(resource_path(
            'views/admin/units/question/modals/edit-question.blade.php'
        ));
        $action = file_get_contents(resource_path(
            'views/admin/units/question/partials/forms/question-action.blade.php'
        ));

        $this->assertStringContainsString('data-question-field="scale_include_zero"', $create);
        $this->assertStringContainsString('data-question-field="scale_child_values"', $create);
        $this->assertStringContainsString('id="edit_scale_include_zero"', $edit);
        $this->assertStringContainsString('id="edit_scale_child_values"', $edit);
        $this->assertStringContainsString('data-scale-include-zero=', $action);
        $this->assertStringContainsString('data-scale-child-values=', $action);
    }
}
