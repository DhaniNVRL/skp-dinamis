<?php

namespace Tests\Unit;

use Tests\TestCase;

class MonitoringUnitRecapTest extends TestCase
{
    public function test_monitoring_unit_recap_has_chart_and_completion_percentage_table(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(
            app_path('Http/Controllers/MonitoringController.php')
        );
        $layout = file_get_contents(
            resource_path('views/layouts/app-role-modern.blade.php')
        );
        $view = file_get_contents(
            resource_path('views/monitoring/unit-recap.blade.php')
        );

        $this->assertStringContainsString("name('monitoring.unit-recap')", $routes);
        $this->assertStringContainsString("name('monitoring.unit-recap.export')", $routes);
        $this->assertStringContainsString('function unitRecap()', $controller);
        $this->assertStringContainsString('function exportUnitRecap()', $controller);
        $this->assertStringContainsString('function unitRecapFilters(', $controller);
        $this->assertStringContainsString('Belum & Sedang Mengisi', $controller);
        $this->assertStringContainsString('completion_percentage', $controller);
        $this->assertStringContainsString('Grafik Rekap Unit', $layout);
        $this->assertStringContainsString('unitRecapChart', $view);
        $this->assertStringContainsString("route('monitoring.unit-recap.export', request()->only", $view);
        $this->assertStringContainsString('id="recapGroup"', $view);
        $this->assertStringContainsString('id="recapUnit"', $view);
        $this->assertStringContainsString('bg-red-50 text-red-700', $view);
        $this->assertStringContainsString('bg-amber-50 text-amber-700', $view);
        $this->assertStringContainsString('bg-emerald-50 text-emerald-700', $view);
        $this->assertStringContainsString("indexAxis: 'y'", $view);
        $this->assertStringContainsString("legend: { position: 'top' }", $view);
        $this->assertStringContainsString('Total Responden (100%)', $view);
        $this->assertStringContainsString('max: 100', $view);
        $this->assertStringContainsString("return value + '%';", $view);
        $this->assertStringContainsString('xl:grid-cols-2', $view);
    }
}
