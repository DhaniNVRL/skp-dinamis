<?php

namespace Tests\Unit;

use Tests\TestCase;

class MonitoringDashboardPageSizeTest extends TestCase
{
    public function test_dashboard_supports_configurable_page_size_and_all_rows(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/MonitoringDashboardController.php'));
        $filter = file_get_contents(resource_path('views/admin/dashboard/partials/filters.blade.php'));

        $this->assertStringContainsString("'per_page'       => ['nullable', 'in:10,20,50,100,all']", $controller);
        $this->assertStringContainsString("\$perPageOption === 'all'", $controller);
        $this->assertStringContainsString('->paginate($perPage)', $controller);
        $this->assertStringContainsString('name="per_page"', $filter);
        $this->assertStringContainsString('Semua data', $filter);
    }
}
