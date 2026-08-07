<?php

namespace Tests\Unit;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
use App\Services\AnswerReviewFormatter;
use PHPUnit\Framework\TestCase;

class AnswerReviewFormattingTest extends TestCase
{
    public function test_ranking_option_ids_are_rendered_as_readable_option_text(): void
    {
        $answer = $this->answerWithOptions([
            '1' => ['value' => '231', 'child' => null],
            '2' => ['value' => '225', 'child' => null],
        ]);

        $details = $this->format($answer);

        $this->assertSame('Peringkat 1', $details[0]['label']);
        $this->assertSame('Kecepatan pelayanan', $details[0]['value']);
        $this->assertSame('Peringkat 2', $details[1]['label']);
        $this->assertSame('Kejelasan informasi', $details[1]['value']);
    }

    public function test_assessment_and_complaint_keys_are_rendered_in_indonesian(): void
    {
        $answer = $this->answerWithOptions([
            'kepentingan' => '5',
            'kinerja' => '4',
            'alasan' => null,
            'keluhan' => 'Waktu respons perlu diperbaiki',
            'saran' => 'Tambahkan petugas',
        ]);

        $details = $this->format($answer);

        $this->assertSame(
            ['Kepentingan', 'Kinerja', 'Keluhan', 'Saran/Harapan'],
            array_column($details, 'label')
        );
        $this->assertSame('Waktu respons perlu diperbaiki', $details[2]['value']);
    }

    private function answerWithOptions(array $value): Answer
    {
        $question = new Question();
        $question->setRelation('options', collect([
            $this->option(231, 'Kecepatan pelayanan'),
            $this->option(225, 'Kejelasan informasi'),
        ]));

        $answer = new Answer();
        $answer->answer = $value;
        $answer->setRelation('question', $question);

        return $answer;
    }

    private function option(int $id, string $text): Option
    {
        $option = new Option(['answer_text' => $text]);
        $option->id = $id;

        return $option;
    }

    private function format(Answer $answer): array
    {
        return (new AnswerReviewFormatter())->format($answer);
    }
}
