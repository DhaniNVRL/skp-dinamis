<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerTitleComparisonTest extends TestCase
{
    #[Test]
    public function customer_title_does_not_render_annual_comparison_controls(): void
    {
        $view = file_get_contents(
            resource_path('views/admin/units/question/partials/forms/question-card.blade.php')
        );

        $this->assertStringContainsString(
            '(int) $question->questiontype_id !== 1',
            $view
        );
    }

    #[Test]
    public function backend_rejects_comparison_configuration_for_titles(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/QuestionController.php'));

        $this->assertStringContainsString(
            'Judul Pertanyaan tidak menggunakan Pembanding Tahun.',
            $controller
        );
    }
}
