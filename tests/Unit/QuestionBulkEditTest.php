<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuestionBulkEditTest extends TestCase
{
    #[Test]
    public function shared_question_list_provides_bulk_edit_for_selected_questions(): void
    {
        $list = file_get_contents(resource_path('views/admin/units/question/partials/forms/question-list.blade.php'));
        $action = file_get_contents(resource_path('views/admin/units/question/partials/forms/question-action.blade.php'));
        $modal = file_get_contents(resource_path('views/admin/units/question/modals/bulk-edit-question.blade.php'));

        $this->assertStringContainsString('data-question-bulk-edit', $list);
        $this->assertStringContainsString("route('questions.bulk-update')", $list);
        $this->assertStringContainsString('data-question-name=', $action);
        $this->assertStringContainsString('id="bulkEditQuestionForm"', $modal);
        $this->assertStringContainsString('name="form_id"', $modal);
    }

    #[Test]
    public function bulk_update_only_changes_basic_question_fields(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/QuestionController.php'));
        $methodStart = strpos($controller, 'public function bulkUpdate');
        $methodEnd = strpos($controller, 'private function getQuestionTypesByForm', $methodStart);
        $method = substr($controller, $methodStart, $methodEnd - $methodStart);

        $this->assertStringContainsString("->where('form_id', \$form->id)", $method);
        $this->assertStringContainsString("'no_header' =>", $method);
        $this->assertStringContainsString("'no' =>", $method);
        $this->assertStringContainsString("'name' =>", $method);
        $this->assertStringNotContainsString("'questiontype_id' =>", $method);
    }
}
