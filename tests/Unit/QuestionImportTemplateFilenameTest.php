<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuestionImportTemplateFilenameTest extends TestCase
{
    #[Test]
    public function import_modal_displays_the_exact_downloaded_template_filename(): void
    {
        $toolbar = file_get_contents(
            resource_path('views/admin/units/question/partials/forms/toolbar.blade.php')
        );
        $modal = file_get_contents(
            resource_path('views/admin/units/question/modals/import-question.blade.php')
        );

        $this->assertStringContainsString(
            'data-template-file-name="template-pertanyaan-form-{{ $form->id }}.xlsx"',
            $toolbar
        );
        $this->assertStringContainsString('Nama file template yang tepat', $modal);
        $this->assertStringContainsString('importQuestionTemplateFileName', $modal);
    }
}
