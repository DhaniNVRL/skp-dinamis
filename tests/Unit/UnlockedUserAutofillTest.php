<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UnlockedUserAutofillTest extends TestCase
{
    public function test_button_is_only_rendered_for_unlocked_user_accounts(): void
    {
        $controller = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/SurveyController.php');
        $view = file_get_contents(dirname(__DIR__, 2).'/resources/views/user/survey/index.blade.php');

        self::assertStringContainsString("Auth::user()?->hasRole('user')", $controller);
        self::assertStringContainsString('$session?->reopened_at !== null', $controller);
        self::assertStringContainsString('$canUseUnlockedAutofill ?? false', $view);
        self::assertStringContainsString('id="unlockedUserAutofillButton"', $view);
    }

    public function test_autofill_preserves_existing_answers_and_uses_requested_scores(): void
    {
        $script = file_get_contents(dirname(__DIR__, 2).'/resources/js/user/survey.js');

        self::assertStringContainsString('if (radios.some(function (radio) { return radio.checked; })) return;', $script);
        self::assertStringContainsString('/\\[importance\\]$/.test(name)', $script);
        self::assertStringContainsString('/\\[performance\\]$/.test(name)', $script);
        self::assertStringContainsString('const highestValue = numeric[0];', $script);
        self::assertStringContainsString('const secondHighestValue = numeric[1] || highestValue;', $script);
        self::assertStringContainsString('Math.random() < 0.7 ? highestValue : secondHighestValue', $script);
        self::assertStringContainsString('if (select.value !== "" || select.closest(\'.hidden\')) return;', $script);
        self::assertStringContainsString('String(field.value || "").trim() !== ""', $script);
    }
}
