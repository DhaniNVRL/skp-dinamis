<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormMergeConfigurationTest extends TestCase
{
    #[Test]
    public function form_model_and_admin_modal_expose_merge_configuration(): void
    {
        $model = file_get_contents(app_path('Models/Form.php'));
        $modal = file_get_contents(resource_path('views/admin/units/question/modals/edit-form.blade.php'));

        $this->assertStringContainsString("'merge_parent_id'", $model);
        $this->assertStringContainsString('function mergedForms()', $model);
        $this->assertStringContainsString('name="merge_parent_id"', $modal);
    }

    #[Test]
    public function user_page_renders_and_saves_all_members_as_one_stage(): void
    {
        $view = file_get_contents(resource_path('views/user/survey/index.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/AnswerController.php'));

        $this->assertStringContainsString('@foreach ($surveyForms as $formContext)', $view);
        $this->assertStringContainsString('FormMergeService::class', $controller);
        $this->assertStringContainsString('saveSingleFormAnswers($request, $member, false)', $controller);
    }
}
