<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Group;
use App\Models\Question;
use App\Models\UserProfile;
use App\Models\Unit;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RawDataExportService
{
    public function download(Activity $activity, Group $group): StreamedResponse
    {
        abort_unless((int) $group->activity_id === (int) $activity->id, 422);

        [$headers, $rows] = $this->build($activity, $group);
        $spreadsheet = $this->spreadsheet($headers, $rows, $activity, $group);
        $writer = new Xlsx($spreadsheet);
        $filename = 'raw_data_'.str($activity->name.'_'.$group->name)->slug('_').'_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($writer, $spreadsheet): void {
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function build(Activity $activity, Group $group): array
    {
        $forms = Form::query()
            ->where('group_id', $group->id)
            ->with([
                'questions.questiontype:id,name',
                'questions.options:id,question_id,no,answer_text,answer_text2,has_child',
                'competitors:id,group_id,form_id,name',
            ])
            ->orderBy('no_urut')
            ->orderBy('id')
            ->get();

        $formIds = $forms->pluck('id');
        $profiles = UserProfile::query()
            ->where('activity_id', $activity->id)
            ->where('group_id', $group->id)
            ->whereHas('user', function ($query) use ($formIds): void {
                $query
                    ->whereHas('role', fn ($roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['user']))
                    ->whereHas('answers', fn ($answerQuery) => $answerQuery->whereIn('form_id', $formIds));
            })
            ->with(['user:id,username', 'unit:id,name'])
            ->orderBy('user_id')
            ->get();

        $userIds = $profiles->pluck('user_id');
        $answers = $userIds->isEmpty()
            ? collect()
            : Answer::query()
                ->whereIn('user_id', $userIds)
                ->whereIn('form_id', $formIds)
                ->with(['subunit:id,name', 'competitor:id,name'])
                ->orderBy('id')
                ->get();

        $answersByUser = $answers->groupBy('user_id');
        $answeredSlotCount = max(0, (int) $answersByUser->map(
            fn (Collection $items) => $items->pluck('subunit_id')->filter()->unique()->count()
        )->max());
        $configuredSlotCount = (int) Unit::query()
            ->where('group_id', $group->id)
            ->withCount('subunits')
            ->get()
            ->max('subunits_count');
        $slotCount = max($answeredSlotCount, $configuredSlotCount);
        $descriptors = $this->descriptors($forms, $slotCount);
        $headers = collect($descriptors)->pluck('header')->all();

        $rows = $profiles->values()->map(function (UserProfile $profile, int $index) use (
            $answersByUser,
            $descriptors
        ): array {
            $userAnswers = $answersByUser->get($profile->user_id, collect());
            $subunitIds = $userAnswers->pluck('subunit_id')->filter()->unique()->values();
            $subunitSlots = $subunitIds->flip()->map(fn ($position) => $position + 1);
            $answerLookup = $userAnswers->keyBy(fn (Answer $answer) => implode(':', [
                $answer->question_id,
                $answer->subunit_id ?: 0,
                $answer->competitor_id ?: 0,
            ]));

            return collect($descriptors)->map(fn (array $descriptor) => $this->cellValue(
                $descriptor,
                $profile,
                $index,
                $userAnswers,
                $answerLookup,
                $subunitIds,
                $subunitSlots
            ))->all();
        })->all();

        return [$headers, $rows];
    }

    private function descriptors(Collection $forms, int $slotCount): array
    {
        $descriptors = [
            ['header' => 'id_user', 'type' => 'profile', 'field' => 'user_id'],
            ['header' => 'No', 'type' => 'profile', 'field' => 'number'],
            ['header' => 'fullname', 'type' => 'profile', 'field' => 'username'],
            ['header' => 'id_unit', 'type' => 'profile', 'field' => 'unit'],
        ];

        for ($slot = 1; $slot <= $slotCount; $slot++) {
            $descriptors[] = [
                'header' => 'UNIT_YANG_DINILAI_'.$slot,
                'type' => 'subunit_name',
                'slot' => $slot,
            ];
        }

        $firstGeneralFormId = $forms->firstWhere('formtype_id', 1)?->id;
        $slots = $slotCount > 0 ? range(1, $slotCount) : [];

        foreach ($forms as $form) {
            if ((int) $form->formtype_id === 12) {
                continue;
            }

            $numberCounts = $form->questions
                ->reject(fn (Question $question) => $this->isTitle($form, $question))
                ->countBy(fn (Question $question) => $question->no_header.'|'.$question->no);
            $numberPositions = [];

            foreach ($form->questions as $question) {
                if ($this->isTitle($form, $question)) {
                    continue;
                }

                $numberKey = $question->no_header.'|'.$question->no;
                $numberPositions[$numberKey] = ($numberPositions[$numberKey] ?? 0) + 1;
                $code = $this->questionCode($form, $question, $firstGeneralFormId);
                $type = (int) $form->formtype_id;

                if (in_array($type, [2, 3], true)
                    && ($numberCounts[$numberKey] ?? 0) > 1) {
                    preg_match('/^\s*([A-Z])\s*[\.\)]/iu', $question->name, $match);
                    $suffix = isset($match[1])
                        ? str($match[1])->upper()->toString()
                        : chr(64 + $numberPositions[$numberKey]);
                    $code .= '.'.$suffix;
                }

                if (in_array($type, [2, 3], true)) {
                    foreach ($slots as $slot) {
                        foreach (['importance', 'performance', 'reason', 'children'] as $field) {
                            $descriptors[] = $this->answerDescriptor(
                                $this->assessmentHeader($field, $code, $slot),
                                $question,
                                $field,
                                $slot
                            );
                        }

                        if ((int) $question->questiontype_id === 4) {
                            $descriptors[] = $this->answerDescriptor(
                                'PEMBANDING.'.$code.'_'.$slot,
                                $question,
                                'comparison',
                                $slot
                            );
                        }
                    }
                    continue;
                }

                if (in_array($type, [8, 9, 10], true)) {
                    $fields = match ($type) {
                        8 => ['strength' => 'KEUNGGULAN', 'complaint' => 'KELUHAN', 'suggestion' => 'SARAN'],
                        9 => ['complaint' => 'KELUHAN', 'suggestion' => 'SARAN'],
                        default => ['suggestion' => 'SARAN'],
                    };
                    foreach ($slots as $slot) {
                        foreach ($fields as $field => $prefix) {
                            $descriptors[] = $this->answerDescriptor(
                                $prefix.'.'.$code.'_'.$slot,
                                $question,
                                $field,
                                $slot
                            );
                        }
                    }
                    continue;
                }

                if (in_array($type, [11, 13], true)) {
                    foreach ($form->competitors as $competitor) {
                        $descriptors[] = [
                            'header' => $code.'_'.str($competitor->name)->upper()->toString(),
                            'type' => 'competitor',
                            'question' => $question,
                            'competitor_id' => $competitor->id,
                            'field' => 'value',
                        ];
                    }
                    continue;
                }

                if (in_array($type, [6, 7], true)) {
                    $rankCount = $type === 6 ? 3 : 5;
                    foreach (range(1, $rankCount) as $rank) {
                        $descriptors[] = [
                            'header' => $code.'_RANKING_'.$rank,
                            'type' => 'global',
                            'question' => $question,
                            'field' => 'ranking_value',
                            'rank' => $rank,
                        ];
                        $descriptors[] = [
                            'header' => 'ALASAN.'.$code.'_RANKING_'.$rank,
                            'type' => 'global',
                            'question' => $question,
                            'field' => 'ranking_child',
                            'rank' => $rank,
                        ];
                    }
                    continue;
                }

                $descriptors[] = [
                    'header' => $code,
                    'type' => 'global',
                    'question' => $question,
                    'field' => 'value',
                ];

                if ($question->options->contains(fn ($option) => (int) $option->has_child === 1)) {
                    $descriptors[] = [
                        'header' => 'ALASAN.'.$code,
                        'type' => 'global',
                        'question' => $question,
                        'field' => 'child',
                    ];
                }
            }
        }

        return $descriptors;
    }

    private function answerDescriptor(string $header, Question $question, string $field, int $slot): array
    {
        return compact('header', 'question', 'field', 'slot') + ['type' => 'subunit'];
    }

    private function cellValue(
        array $descriptor,
        UserProfile $profile,
        int $index,
        Collection $answers,
        Collection $lookup,
        Collection $subunitIds,
        Collection $subunitSlots
    ): mixed {
        if ($descriptor['type'] === 'profile') {
            return match ($descriptor['field']) {
                'user_id' => $profile->user_id,
                'number' => $index + 1,
                'username' => $profile->user?->username,
                'unit' => $profile->unit?->name,
            };
        }

        if ($descriptor['type'] === 'subunit_name') {
            $subunitId = $subunitIds->get($descriptor['slot'] - 1);
            return $answers->firstWhere('subunit_id', $subunitId)?->subunit?->name;
        }

        $question = $descriptor['question'];
        $subunitId = 0;
        $competitorId = 0;

        if ($descriptor['type'] === 'subunit') {
            $subunitId = (int) $subunitSlots->search($descriptor['slot'], true);
            if (! $subunitId) {
                return null;
            }
        } elseif ($descriptor['type'] === 'competitor') {
            $competitorId = (int) $descriptor['competitor_id'];
        }

        $answer = $lookup->get(implode(':', [$question->id, $subunitId, $competitorId]));
        if (! $answer) {
            return null;
        }

        return $this->answerField($answer->answer, $descriptor['field'], $question, $descriptor['rank'] ?? null);
    }

    private function answerField(mixed $payload, string $field, Question $question, ?int $rank = null): mixed
    {
        $payload = is_array($payload) ? $payload : ['value' => $payload];
        $value = match ($field) {
            'importance' => data_get($payload, 'importance', data_get($payload, 'kepentingan')),
            'performance' => data_get($payload, 'performance', data_get($payload, 'kinerja')),
            'reason' => data_get($payload, 'reason', data_get($payload, 'alasan', data_get($payload, 'essay'))),
            'children' => data_get($payload, 'children', data_get($payload, 'child', data_get($payload, 'alasan_lainnya', []))),
            'comparison' => data_get($payload, 'comparison', data_get($payload, 'pembanding')),
            'strength' => data_get($payload, 'strength', data_get($payload, 'keunggulan')),
            'complaint' => data_get($payload, 'complaint', data_get($payload, 'keluhan')),
            'suggestion' => data_get($payload, 'suggestion', data_get($payload, 'saran')),
            'ranking_value' => data_get($payload, $rank.'.value', data_get($payload, 'value.'.$rank.'.value')),
            'ranking_child' => data_get($payload, $rank.'.child', data_get($payload, 'value.'.$rank.'.child')),
            'child' => data_get($payload, 'child', data_get($payload, 'children', data_get($payload, 'alasan_lainnya', []))),
            'value' => data_get($payload, 'value', data_get($payload, 'nilai', $payload)),
            default => data_get($payload, $field),
        };

        return $this->readableValue($value, $question);
    }

    private function readableValue(mixed $value, Question $question): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->readableValue($item, $question))
                ->filter(fn ($item) => filled($item) || $item === 0 || $item === '0')
                ->implode('; ');
        }

        if (is_numeric($value)) {
            $option = $question->options->firstWhere('id', (int) $value);
            if ($option) {
                return $option->answer_text;
            }

            return $value + 0;
        }

        return is_bool($value) ? ($value ? 'Ya' : 'Tidak') : (string) $value;
    }

    private function isTitle(Form $form, Question $question): bool
    {
        return (int) $question->questiontype_id === 10
            || ((int) $form->formtype_id !== 1 && (int) $question->questiontype_id === 1);
    }

    private function questionCode(Form $form, Question $question, ?int $firstGeneralFormId): string
    {
        $header = trim((string) $question->no_header);
        $number = trim((string) $question->no);

        if (in_array((int) $form->formtype_id, [2, 3], true)) {
            return 'B'.$header.$number;
        }

        if (in_array((int) $form->formtype_id, [11, 13], true)
            || (int) $form->id === (int) $firstGeneralFormId) {
            return trim($header.'.'.$number, '.');
        }

        if ((int) $form->formtype_id === 1
            && preg_match('/^\s*([A-Z]|\d+)\s*[\.\)]/iu', $question->name, $match)) {
            return $header.$number.'.'.str($match[1])->upper()->toString();
        }

        return $header.$number;
    }

    private function assessmentHeader(string $field, string $code, int $slot): string
    {
        $prefix = match ($field) {
            'importance' => 'KEPENTINGAN',
            'performance' => 'KINERJA',
            'reason' => 'ALASAN',
            'children' => 'ALASAN_LAINNYA',
        };

        return $prefix.'.'.$code.'_'.$slot;
    }

    private function spreadsheet(
        array $headers,
        array $rows,
        Activity $activity,
        Group $group
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Raw Data');
        $sheet->fromArray($headers, null, 'A1', true);

        if ($rows !== []) {
            $sheet->fromArray($rows, null, 'A2', true);
        }

        $lastColumn = Coordinate::stringFromColumnIndex(max(1, count($headers)));
        $lastRow = max(1, count($rows) + 1);
        $sheet->freezePane('E2');
        $sheet->setAutoFilter('A1:'.$lastColumn.$lastRow);
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2457D6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(42);
        foreach (range(1, min(4, count($headers))) as $column) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
        }
        for ($column = 5; $column <= count($headers); $column++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setWidth(22);
        }
        $spreadsheet->getProperties()
            ->setTitle('Raw Data '.$activity->name.' - '.$group->name)
            ->setSubject('Export jawaban survey satu baris per responden')
            ->setCreator('SKP Dinamis');

        return $spreadsheet;
    }
}
