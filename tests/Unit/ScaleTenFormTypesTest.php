<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScaleTenFormTypesTest extends TestCase
{
    #[Test]
    public function customer_scale_ten_uses_shared_customer_assessment_with_zero_and_reasons(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/customer-assessment-1-10.blade.php')
        );

        $this->assertStringContainsString("'maximumScale' => 10", $view);
        $this->assertStringContainsString("'reasonMaximum' => 5", $view);
        $this->assertStringContainsString("'includeZero' => true", $view);
    }

    #[Test]
    public function engagement_scale_ten_uses_shared_engagement_assessment(): void
    {
        $view = file_get_contents(
            resource_path('views/user/survey/forms/engagement-assessment-1-10.blade.php')
        );

        $this->assertStringContainsString("'maximumScale' => 10", $view);
    }

    #[Test]
    public function survey_router_supports_both_scale_ten_form_types(): void
    {
        $view = file_get_contents(resource_path('views/user/survey/index.blade.php'));

        $this->assertStringContainsString('@case(17)', $view);
        $this->assertStringContainsString('customer-assessment-1-10', $view);
        $this->assertStringContainsString('@case(18)', $view);
        $this->assertStringContainsString('engagement-assessment-1-10', $view);
    }
}
