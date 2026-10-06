<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormVisibilityToggleUiTest extends TestCase
{
    #[Test]
    public function form_toggle_updates_without_page_reload_and_rolls_back_on_failure(): void
    {
        $script = file_get_contents(resource_path('js/admin/hide-and-show.js'));
        $formToggleStart = strpos($script, 'data-form-visibility-toggle');
        $formToggleEnd = strpos($script, 'function updateToggleState', $formToggleStart);
        $formToggle = substr($script, $formToggleStart, $formToggleEnd - $formToggleStart);

        $this->assertStringNotContainsString('window.location.reload', $formToggle);
        $this->assertStringContainsString('updateToggleState(button, nextVisible)', $formToggle);
        $this->assertStringContainsString('updateToggleState(button, previousVisible)', $formToggle);
        $this->assertStringContainsString('syncFormQuestionToggles(card, nextVisible)', $formToggle);
    }
}
