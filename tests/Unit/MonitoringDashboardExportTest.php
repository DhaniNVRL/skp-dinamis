<?php

namespace Tests\Unit;

use Tests\TestCase;

class MonitoringDashboardExportTest extends TestCase
{
    public function test_monitoring_dashboard_has_respondent_percentage_and_export(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(
            app_path('Http/Controllers/MonitoringController.php')
        );
        $cards = file_get_contents(
            resource_path('views/monitoring/partials/cards.blade.php')
        );
        $table = file_get_contents(
            resource_path('views/monitoring/partials/table.blade.php')
        );

        $this->assertStringContainsString("name('monitoring.dashboard.export')", $routes);
        $this->assertStringContainsString('function exportDashboardRespondents(', $controller);
        $this->assertStringContainsString('daftar_responden_monitoring_', $controller);
        $this->assertStringContainsString('completionPercentage', $controller);
        $this->assertStringContainsString('Persentase Penyelesaian', $cards);
        $this->assertStringContainsString('Responden sudah mengisi', $cards);
        $this->assertStringContainsString("route('monitoring.dashboard.export'", $table);
    }
}
