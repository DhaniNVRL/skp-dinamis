<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MonitoringFilterPersistenceTest extends TestCase
{
    #[Test]
    public function detail_link_carries_the_complete_dashboard_query(): void
    {
        $table = file_get_contents(
            resource_path('views/admin/dashboard/partials/respondent-table.blade.php')
        );

        $this->assertStringContainsString("'return_to' => request()->getRequestUri()", $table);
    }

    #[Test]
    public function detail_controller_only_accepts_a_local_dashboard_return_url(): void
    {
        $controller = file_get_contents(
            app_path('Http/Controllers/MonitoringDashboardController.php')
        );
        $detail = file_get_contents(
            resource_path('views/admin/dashboard/respondent-detail.blade.php')
        );

        $this->assertStringContainsString("request->query('return_to'", $controller);
        $this->assertStringContainsString("! isset(\$returnParts['host'])", $controller);
        $this->assertStringContainsString("(\$returnParts['path'] ?? null) === \$expectedPath", $controller);
        $this->assertStringContainsString("'backUrl' => \$dashboardReturnUrl", $detail);
    }
}
