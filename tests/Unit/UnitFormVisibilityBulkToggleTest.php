<?php

namespace Tests\Unit;

use Tests\TestCase;

class UnitFormVisibilityBulkToggleTest extends TestCase
{
    public function test_hide_and_show_has_master_form_and_question_toggle(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/UnitFormVisibilityController.php'));
        $view = file_get_contents(resource_path('views/admin/subunit/pages/hide-show.blade.php'));
        $script = file_get_contents(resource_path('js/admin/hide-and-show.js'));

        $this->assertStringContainsString("'toggleAll'", $routes);
        $this->assertStringContainsString('public function toggleAll', $controller);
        $this->assertStringContainsString('data-show-all-unit', $view);
        $this->assertStringContainsString('data-show-all-unit-url', $view);
        $this->assertStringContainsString('function syncShowAllUnitState', $script);
    }
}
