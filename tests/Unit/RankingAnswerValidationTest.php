<?php

namespace Tests\Unit;

use App\Http\Controllers\AnswerController;
use App\Models\Form;
use App\Models\Option;
use App\Models\Question;
use ReflectionMethod;
use Tests\TestCase;

class RankingAnswerValidationTest extends TestCase
{
    public function test_ranking_one_to_three_accepts_unique_valid_options_and_required_child(): void
    {
        [$form, $question] = $this->rankingContext(6);

        $errors = $this->validateRanking($form, $question, [
            'value' => [
                1 => ['option_id' => 11, 'child' => 'Keluhan atau saran contoh'],
                2 => ['option_id' => 12],
                3 => ['option_id' => 13],
            ],
        ]);

        $this->assertSame([], $errors);
    }

    public function test_ranking_rejects_missing_duplicate_invalid_and_empty_child_answers(): void
    {
        [$form, $question] = $this->rankingContext(7);

        $errors = $this->validateRanking($form, $question, [
            'value' => [
                1 => ['option_id' => 11, 'child' => ''],
                2 => ['option_id' => 12],
                3 => ['option_id' => 12],
                4 => ['option_id' => 999],
            ],
        ]);

        $this->assertArrayHasKey('answers.100.value.1.child', $errors);
        $this->assertArrayHasKey('answers.100.value.3.option_id', $errors);
        $this->assertArrayHasKey('answers.100.value.4.option_id', $errors);
        $this->assertArrayHasKey('answers.100.value.5.option_id', $errors);
    }

    private function rankingContext(int $formTypeId): array
    {
        $form = new Form(['formtype_id' => $formTypeId]);
        $question = new Question([
            'name' => 'Faktor kepuasan',
            'questiontype_id' => 2,
        ]);
        $question->id = 100;

        $question->setRelation('options', collect([
            $this->option(11, 'Lain-lain', true),
            $this->option(12, 'Kecepatan layanan'),
            $this->option(13, 'Kualitas layanan'),
            $this->option(14, 'Komunikasi'),
            $this->option(15, 'Kemudahan'),
        ]));

        return [$form, $question];
    }

    private function option(int $id, string $text, bool $hasChild = false): Option
    {
        $option = new Option([
            'answer_text' => $text,
            'has_child' => $hasChild ? 1 : 0,
        ]);
        $option->id = $id;

        return $option;
    }

    private function validateRanking(Form $form, Question $question, array $payload): array
    {
        $errors = [];
        $method = new ReflectionMethod(AnswerController::class, 'validateRankingAnswer');
        $controller = app(AnswerController::class);
        $arguments = [$form, $question, $payload, &$errors];
        $method->invokeArgs($controller, $arguments);

        return $errors;
    }
}