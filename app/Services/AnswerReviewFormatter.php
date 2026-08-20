<?php

namespace App\Services;

use App\Models\Answer;

class AnswerReviewFormatter
{
    public function format(Answer $answer): array
    {
        $value = $answer->answer;
        $options = $answer->question?->options?->keyBy('id') ?? collect();

        if (! is_array($value)) {
            return [[
                'label' => 'Jawaban',
                'value' => $this->value($value, $options),
            ]];
        }

        if ($value === []) {
            return [['label' => 'Jawaban', 'value' => '-']];
        }

        $details = [];
        $labels = [
            'importance' => 'Kepentingan',
            'kepentingan' => 'Kepentingan',
            'performance' => 'Kinerja',
            'kinerja' => 'Kinerja',
            'reason' => 'Alasan',
            'alasan' => 'Alasan',
            'reasons' => 'Alasan',
            'complaint' => 'Keluhan',
            'keluhan' => 'Keluhan',
            'suggestion' => 'Saran/Harapan',
            'saran' => 'Saran/Harapan',
            'value' => 'Jawaban',
        ];

        foreach ($value as $key => $item) {
            if ($key === 'child' || $key === 'children') {
                foreach ((array) $item as $optionId => $childValue) {
                    if (! filled($childValue)) {
                        continue;
                    }

                    $optionName = $options->get((int) $optionId)?->answer_text;
                    $details[] = [
                        'label' => $optionName
                            ? 'Keterangan - '.$optionName
                            : 'Keterangan',
                        'value' => $this->value($childValue, $options, false),
                    ];
                }

                continue;
            }

            if (is_numeric($key) && is_array($item) && array_key_exists('value', $item)) {
                $details[] = [
                    'label' => 'Peringkat '.$key,
                    'value' => $this->value($item['value'], $options),
                ];

                if (filled($item['child'] ?? null)) {
                    $details[] = [
                        'label' => 'Keterangan Peringkat '.$key,
                        'value' => $this->value($item['child'], $options, false),
                    ];
                }

                continue;
            }

            if (! filled($item) && $item !== 0 && $item !== '0') {
                continue;
            }

            $details[] = [
                'label' => $labels[strtolower((string) $key)]
                    ?? str($key)->replace('_', ' ')->title()->toString(),
                'value' => $this->value($item, $options),
            ];
        }

        return $details !== []
            ? $details
            : [['label' => 'Jawaban', 'value' => '-']];
    }

    private function value($value, $options, bool $resolveOption = true): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (is_array($value)) {
            $values = collect($value)
                ->map(fn ($item) => $this->value($item, $options, $resolveOption))
                ->filter(fn ($item) => $item !== '-')
                ->values();

            return $values->isEmpty() ? '-' : $values->implode(', ');
        }

        if ($resolveOption && is_numeric($value)) {
            $option = $options->get((int) $value);

            if ($option) {
                return (string) $option->answer_text;
            }
        }

        return (string) $value;
    }
}
