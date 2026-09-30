<?php

namespace Tests\Unit;

use Tests\TestCase;

class EngagementAssessmentLayoutTest extends TestCase
{
    public function test_admin_scales_share_the_same_engagement_layout(): void
    {
        $five = file_get_contents(resource_path('views/admin/subunit/show-question/forms/engagement-assessment-1-5.blade.php'));
        $seven = file_get_contents(resource_path('views/admin/subunit/show-question/forms/engagement-assessment-1-7.blade.php'));
        $shared = file_get_contents(resource_path('views/admin/subunit/show-question/forms/partials/engagement-assessment.blade.php'));

        $this->assertStringContainsString('partials.engagement-assessment', $five);
        $this->assertStringContainsString("'maximumScale' => 5", $five);
        $this->assertStringContainsString('partials.engagement-assessment', $seven);
        $this->assertStringContainsString("'maximumScale' => 7", $seven);
        $this->assertStringContainsString('border-violet-200', $shared);
        $this->assertStringContainsString('peer-checked:bg-violet-600', $shared);
        $this->assertStringNotContainsString('disabled', $shared);
    }

    public function test_user_scales_share_the_indigo_assessment_layout(): void
    {
        $shared = file_get_contents(resource_path('views/user/survey/forms/partials/engagement-assessment.blade.php'));

        $this->assertStringContainsString('border-indigo-200', $shared);
        $this->assertStringContainsString('peer-checked:bg-indigo-600', $shared);
        $this->assertStringContainsString('h-10 w-10', $shared);
    }
}
