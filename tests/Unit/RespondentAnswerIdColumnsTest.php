<?php

namespace Tests\Unit;

use Tests\TestCase;

class RespondentAnswerIdColumnsTest extends TestCase
{
    public function test_answer_detail_displays_form_and_question_ids_on_the_left(): void
    {
        $view = file_get_contents(resource_path('views/admin/shared/respondent-answer-detail.blade.php'));

        $this->assertMatchesRegularExpression(
            '/>No<\/th>\s*<th[^>]*>ID Form<\/th>\s*<th[^>]*>ID Question<\/th>/',
            $view
        );
        $this->assertStringContainsString('{{ $answer->form_id ?: \'-\' }}', $view);
        $this->assertStringContainsString('{{ $answer->question_id ?: \'-\' }}', $view);
        $this->assertStringContainsString('colspan="8"', $view);
    }
}
