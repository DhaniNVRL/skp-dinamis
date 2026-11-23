<?php

namespace Tests\Unit;

use Tests\TestCase;

class SubUnitHideShowSummaryTest extends TestCase
{
    public function test_hide_and_show_page_has_dynamic_question_summary(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/SubUnitController.php'));
        $view = file_get_contents(resource_path('views/admin/subunit/pages/hide-show.blade.php'));
        $script = file_get_contents(resource_path('js/admin/hide-and-show.js'));

        $this->assertStringContainsString('questionVisibilitySummary', $controller);
        $this->assertStringContainsString('Jumlah Pertanyaan Keseluruhan', $view);
        $this->assertStringContainsString('data-hide-show-shown', $view);
        $this->assertStringContainsString('data-hide-show-hidden', $view);
        $this->assertStringContainsString('function syncHideShowSummary', $script);
    }
}
