<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductionCapacityConfigurationTest extends TestCase
{
    public function test_session_cleanup_and_dashboard_reference_cache_are_configurable(): void
    {
        $sessionConfig = file_get_contents(config_path('session.php'));
        $dashboard = file_get_contents(
            app_path('Http/Controllers/UserDashboardController.php')
        );
        $completeProfile = file_get_contents(
            app_path('Http/Controllers/CompleteProfileController.php')
        );

        $this->assertStringContainsString('SESSION_LOTTERY_NUMERATOR', $sessionConfig);
        $this->assertStringContainsString('SESSION_LOTTERY_DENOMINATOR', $sessionConfig);
        $this->assertStringContainsString('Cache::remember(', $dashboard);
        $this->assertStringContainsString('respondent-dashboard:complete-profile:activity:', $dashboard);
        $this->assertStringContainsString('Cache::forget(', $completeProfile);
    }
}
