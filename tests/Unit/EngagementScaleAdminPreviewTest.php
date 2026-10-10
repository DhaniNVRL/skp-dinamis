<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EngagementScaleAdminPreviewTest extends TestCase
{
    #[Test]
    public function engagement_scale_seven_uses_seven_values_in_admin_preview(): void
    {
        $card = file_get_contents(resource_path(
            'views/admin/units/question/partials/forms/question-card.blade.php'
        ));
        $indicator = file_get_contents(resource_path(
            'views/admin/units/question/partials/forms/engagement-assessment-1-7/options/single-indicator.blade.php'
        ));

        $this->assertStringContainsString('3, 5, 16 => 7', $card);
        $this->assertStringContainsString('range(1, 7)', $indicator);
    }
}
