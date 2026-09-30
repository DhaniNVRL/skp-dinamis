<?php

namespace Tests\Unit;

use App\Support\QuestionOptionGrid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuestionOptionGridTest extends TestCase
{
    #[DataProvider('layoutProvider')]
    public function test_it_calculates_the_requested_option_rows(
        int $optionCount,
        int $expectedRows,
        int $expectedColumns
    ): void {
        $this->assertSame(
            [
                'rows' => $expectedRows,
                'columns' => $expectedColumns,
            ],
            QuestionOptionGrid::layout($optionCount)
        );
    }

    public static function layoutProvider(): array
    {
        return [
            'empty' => [0, 1, 1],
            'five options remain vertical' => [5, 5, 1],
            'nine options remain vertical' => [9, 9, 1],
            'ten options use two columns' => [10, 5, 2],
            'fourteen options use two columns' => [14, 7, 2],
            'fifteen options use three columns' => [15, 5, 3],
            'nineteen options use three columns' => [19, 7, 3],
            'twenty options use four columns' => [20, 5, 4],
            'more than twenty options use five columns' => [21, 5, 5],
            'larger collections keep five columns' => [26, 6, 5],
        ];
    }
}
