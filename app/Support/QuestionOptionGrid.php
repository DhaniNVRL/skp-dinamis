<?php

namespace App\Support;

final class QuestionOptionGrid
{
    /**
     * @return array{rows: int, columns: int}
     */
    public static function layout(int $optionCount): array
    {
        $optionCount = max(0, $optionCount);
        $columns = match (true) {
            $optionCount < 10 => 1,
            $optionCount < 15 => 2,
            $optionCount < 20 => 3,
            $optionCount === 20 => 4,
            default => 5,
        };

        return [
            'rows' => max(1, (int) ceil($optionCount / $columns)),
            'columns' => $columns,
        ];
    }
}
