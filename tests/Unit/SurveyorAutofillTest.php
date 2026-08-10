<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SurveyorAutofillTest extends TestCase
{
    public function test_autofill_button_is_limited_to_surveyor_view_guard(): void
    {
        $view = file_get_contents(
            dirname(__DIR__, 2).'/resources/views/user/survey/index.blade.php'
        );

        $guardPosition = strpos($view, "hasRole('surveyor')");
        $buttonPosition = strpos($view, 'id="surveyorAutofillButton"');
        $guardEndPosition = strpos($view, '@endif', $guardPosition);

        self::assertNotFalse($guardPosition);
        self::assertNotFalse($buttonPosition);
        self::assertNotFalse($guardEndPosition);
        self::assertGreaterThan($guardPosition, $buttonPosition);
        self::assertLessThan($guardEndPosition, $buttonPosition);
    }

    public function test_autofill_supports_common_and_complex_survey_fields(): void
    {
        $script = file_get_contents(
            dirname(__DIR__, 2).'/resources/js/user/survey.js'
        );

        foreach ([
            'fillRankingSelects',
            'fillRadioGroups',
            'fillRequiredCheckboxGroups',
            'fillRegularSelects',
            'fillDummyTextFields',
            'surveyor.dummy@example.com',
            'Contoh jawaban pengisian oleh Surveyor.',
        ] as $expected) {
            self::assertStringContainsString($expected, $script);
        }
    }
}
