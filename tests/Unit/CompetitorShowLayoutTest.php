<?php

namespace Tests\Unit;

use App\Models\Competitor;
use App\Models\Form;
use App\Models\Question;
use Tests\TestCase;

class CompetitorShowLayoutTest extends TestCase
{
    public function test_competitors_are_rendered_vertically_with_numbered_radio_controls(): void
    {
        $form = new Form(['formtype_id' => 11]);
        $form->id = 100;

        $question = new Question([
            'form_id' => $form->id,
            'no_header' => 'E',
            'no' => '1',
            'name' => 'Kesesuaian karakteristik operasi',
            'questiontype_id' => 2,
        ]);
        $question->id = 200;

        $firstCompetitor = new Competitor(['name' => 'IPP']);
        $firstCompetitor->id = 301;
        $secondCompetitor = new Competitor(['name' => 'PLN IP']);
        $secondCompetitor->id = 302;

        $html = view('admin.subunit.show-question.forms.partials.competitor-assessment', [
            'form' => $form,
            'questions' => collect([$question]),
            'competitors' => collect([$firstCompetitor, $secondCompetitor]),
            'maximum' => 5,
        ])->render();

        $this->assertStringContainsString('data-competitor-list', $html);
        $this->assertSame(2, substr_count($html, 'data-competitor-row'));
        $this->assertStringNotContainsString('<table', $html);
        $this->assertStringContainsString('name="competitor_200_301"', $html);
        $this->assertStringContainsString('value="5"', $html);
        $this->assertStringContainsString('value="0"', $html);
        $this->assertStringContainsString('IPP', $html);
        $this->assertStringContainsString('PLN IP', $html);
    }
}
