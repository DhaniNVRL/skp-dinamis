<?php

namespace Tests\Unit;

use Tests\TestCase;

class MonitoringUserSortingTest extends TestCase
{
    public function test_monitoring_and_surveyor_table_can_sort_user_id_and_username(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/MonitoringController.php'));
        $table = file_get_contents(resource_path('views/monitoring/partials/table.blade.php'));

        $this->assertStringContainsString("Rule::in(['id', 'username'])", $controller);
        $this->assertStringContainsString("Rule::in(['asc', 'desc'])", $controller);
        $this->assertStringContainsString("\$sortUrl('id')", $table);
        $this->assertStringContainsString("\$sortUrl('username')", $table);
        $this->assertStringContainsString('{{ $respondent->user_id }}', $table);
    }
}
