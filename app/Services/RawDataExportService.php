<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Form;
use App\Models\Group;
use App\Models\Question;
use App\Models\UserProfile;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class RawDataExportService
{
    public function download(Activity $activity, Group $group): StreamedResponse
    {
        abort_unless((int) $group->activity_id === (int) $activity->id, 422);

        $path = $this->writeStreamingWorkbook($activity, $group);
        $filename = 'raw_data_'.str($activity->name.'_'.$group->name)->slug('_').'_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($path): void {
            $stream = fopen($path, 'rb');
            if ($stream !== false) {
                fpassthru($stream);
                fclose($stream);
            }
            @unlink($path);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    private function writeStreamingWorkbook(Activity $activity, Group $group): string
    {
        $forms = Form::query()
            ->where('group_id', $group->id)
            ->with([
                'questions.questiontype:id,name',
                'questions.options:id,question_id,no,answer_text,answer_text2,has_child',
                'competitors:id,group_id,form_id,name',
            ])
            ->orderBy('no_urut')->orderBy('id')->get();
        $formIds = $forms->pluck('id');
        $answeredCompetitorsByForm = $this->answeredCompetitorsByForm($formIds);

        $slotCounts = Answer::query()
            ->whereIn('form_id', $formIds)
            ->whereNotNull('subunit_id')
            ->selectRaw('user_id, COUNT(DISTINCT subunit_id) AS slot_count')
            ->groupBy('user_id');
        $answeredSlotCount = (int) DB::query()->fromSub($slotCounts, 'slot_counts')->max('slot_count');
        $configuredSlot = DB::table('subunits')
            ->join('units', 'units.id', '=', 'subunits.unit_id')
            ->where('units.group_id', $group->id)
            ->selectRaw('COUNT(subunits.id) AS slot_count')
            ->groupBy('subunits.unit_id')
            ->orderByDesc('slot_count')
            ->first();
        $configuredSlotCount = (int) ($configuredSlot->slot_count ?? 0);
        $descriptors = $this->descriptors(
            $forms,
            max($answeredSlotCount, $configuredSlotCount),
            $answeredCompetitorsByForm
        );
        $headers = collect($descriptors)->pluck('header')->all();
        [$formHeaders, $headerMerges] = $this->headerLayout($descriptors);

        $sheetPath = tempnam(sys_get_temp_dir(), 'skp_sheet_');
        $xlsxPath = tempnam(sys_get_temp_dir(), 'skp_raw_');
        if ($sheetPath === false || $xlsxPath === false) {
            abort(500, 'File sementara export tidak dapat dibuat.');
        }

        $sheet = fopen($sheetPath, 'wb');
        if ($sheet === false) {
            abort(500, 'File sementara sheet tidak dapat dibuka.');
        }

        fwrite($sheet, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>');
        fwrite($sheet, '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane xSplit="4" ySplit="2" topLeftCell="E3" activePane="bottomRight" state="frozen"/></sheetView></sheetViews><sheetFormatPr defaultRowHeight="15"/><sheetData>');
        $this->writeXlsxRow($sheet, 1, $formHeaders, true);
        $this->writeXlsxRow($sheet, 2, $headers, true);

        $profileQuery = UserProfile::query()
            ->where('activity_id', $activity->id)
            ->where('group_id', $group->id)
            ->whereHas('user', function ($query) use ($formIds): void {
                $query->whereHas('role', fn ($roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['user']))
                    ->whereHas('answers', fn ($answerQuery) => $answerQuery->whereIn('form_id', $formIds));
            })
            ->with(['user:id,username', 'unit:id,name']);

        $rowNumber = 2;
        $profileNumber = 0;
        $profileQuery->chunkById(250, function (Collection $profiles) use (
            $formIds, $descriptors, $sheet, &$rowNumber, &$profileNumber
        ): void {
            $answersByUser = Answer::query()
                ->whereIn('user_id', $profiles->pluck('user_id'))
                ->whereIn('form_id', $formIds)
                ->select([
                    'id', 'user_id', 'form_id', 'question_id', 'subunit_id',
                    'competitor_id', 'respondent_competitor_id', 'answer',
                ])
                ->with('subunit:id,name')
                ->orderBy('id')->get()->groupBy('user_id');

            foreach ($profiles as $profile) {
                $profileNumber++;
                $userAnswers = $answersByUser->get($profile->user_id, collect());
                $subunitIds = $userAnswers->pluck('subunit_id')->filter()->unique()->values();
                $subunitSlots = $subunitIds->flip()->map(fn ($position) => $position + 1);
                $answerLookup = $userAnswers->mapWithKeys(function (Answer $answer): array {
                    $key = implode(':', [
                        $answer->question_id,
                        $answer->subunit_id ?: 0,
                        $answer->competitor_id ?: 0,
                    ]);

                    return [$key => [
                        'answer' => $answer,
                        'payload' => $answer->answer,
                    ]];
                });
                $values = [];
                foreach ($descriptors as $descriptor) {
                    $values[] = $this->cellValue(
                        $descriptor,
                        $profile,
                        $profileNumber - 1,
                        $userAnswers,
                        $answerLookup,
                        $subunitIds,
                        $subunitSlots
                    );
                }
                $this->writeXlsxRow($sheet, ++$rowNumber, $values);
            }

            unset($answersByUser);
        }, 'id');

        fwrite($sheet, '</sheetData>');
        if ($headers !== []) {
            $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
            fwrite($sheet, '<autoFilter ref="A2:'.$lastColumn.$rowNumber.'"/>');
        }
        if ($headerMerges !== []) {
            fwrite($sheet, '<mergeCells count="'.count($headerMerges).'">');
            foreach ($headerMerges as $mergeReference) {
                fwrite($sheet, '<mergeCell ref="'.$mergeReference.'"/>');
            }
            fwrite($sheet, '</mergeCells>');
        }
        fwrite($sheet, '</worksheet>');
        fclose($sheet);

        $this->packageXlsx($xlsxPath, $sheetPath, $activity, $group);
        @unlink($sheetPath);

        return $xlsxPath;
    }

    private function writeXlsxRow($stream, int $row, array $values, bool $header = false): void
    {
        $xml = '<row r="'.$row.'"'.($header ? ' ht="42" customHeight="1"' : '').'>';
        foreach (array_values($values) as $offset => $value) {
            $reference = Coordinate::stringFromColumnIndex($offset + 1).$row;
            if ($value === null || $value === '') {
                $xml .= '<c r="'.$reference.'"'.($header ? ' s="1"' : '').'/>';
                continue;
            }
            if (! $header && (is_int($value) || is_float($value))) {
                $xml .= '<c r="'.$reference.'"><v>'.$value.'</v></c>';
                continue;
            }
            $text = mb_substr($this->cleanXmlText((string) $value), 0, 32767);
            $xml .= '<c r="'.$reference.'" t="inlineStr"'.($header ? ' s="1"' : '').'><is><t xml:space="preserve">'.htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</t></is></c>';
        }
        fwrite($stream, $xml.'</row>');
    }

    private function cleanXmlText(string $value): string
    {
        return preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value) ?? '';
    }

    private function packageXlsx(string $xlsxPath, string $sheetPath, Activity $activity, Group $group): void
    {
        $zip = new ZipArchive();
        if ($zip->open($xlsxPath, ZipArchive::OVERWRITE) !== true) {
            abort(500, 'File Excel tidak dapat dibuat.');
        }
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Raw Data" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2457D6"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf></cellXfs></styleSheet>');
        $title = htmlspecialchars('Raw Data '.$activity->name.' - '.$group->name, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $zip->addFromString('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/"><dc:title>'.$title.'</dc:title><dc:creator>SKP Dinamis</dc:creator></cp:coreProperties>');
        $zip->addFile($sheetPath, 'xl/worksheets/sheet1.xml');
        $zip->close();
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
        $answeredCompetitorsByForm = $this->answeredCompetitorsByForm($formIds);
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
        $descriptors = $this->descriptors($forms, $slotCount, $answeredCompetitorsByForm);
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

    private function descriptors(
        Collection $forms,
        int $slotCount,
        ?Collection $answeredCompetitorsByForm = null
    ): array
    {
        $answeredCompetitorsByForm ??= collect();
        $descriptors = [
            ['header' => 'id_user', 'type' => 'profile', 'field' => 'user_id', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
            ['header' => 'No', 'type' => 'profile', 'field' => 'number', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
            ['header' => 'fullname', 'type' => 'profile', 'field' => 'username', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
            ['header' => 'id_unit', 'type' => 'profile', 'field' => 'unit', 'section_key' => 'respondent', 'form_name' => 'DATA RESPONDEN'],
        ];

        for ($slot = 1; $slot <= $slotCount; $slot++) {
            $descriptors[] = [
                'header' => 'UNIT_YANG_DINILAI_'.$slot,
                'type' => 'subunit_name',
                'slot' => $slot,
                'section_key' => 'respondent',
                'form_name' => 'DATA RESPONDEN',
            ];
        }

        $firstGeneralFormId = $forms->firstWhere('formtype_id', 1)?->id;
        $slots = $slotCount > 0 ? range(1, $slotCount) : [];

        foreach ($forms as $form) {
            if ((int) $form->formtype_id === 12) {
                continue;
            }

            $formDescriptorStart = count($descriptors);

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

                if (in_array($type, [2, 3, 15, 16, 17, 19], true)
                    && ($numberCounts[$numberKey] ?? 0) > 1) {
                    preg_match('/^\s*([A-Z])\s*[\.\)]/iu', $question->name, $match);
                    $suffix = isset($match[1])
                        ? str($match[1])->upper()->toString()
                        : chr(64 + $numberPositions[$numberKey]);
                    $code .= '.'.$suffix;
                }

                if (in_array($type, [2, 3, 15, 16, 17, 19], true)) {
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
                    if ((int) $question->questiontype_id === 3) {
                        $descriptors[] = [
                            'header' => $code.'_NAMA_KOMPETITOR',
                            'type' => 'global',
                            'question' => $question,
                            'field' => 'value',
                        ];

                        continue;
                    }

                    $formCompetitors = $form->competitors
                        ->concat($answeredCompetitorsByForm->get($form->id, collect()))
                        ->unique('id')
                        ->values();

                    foreach ($formCompetitors as $competitor) {
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

            for ($index = $formDescriptorStart; $index < count($descriptors); $index++) {
                $descriptors[$index]['section_key'] = 'form:'.$form->id;
                $descriptors[$index]['form_name'] = $form->name;
            }
        }

        return $descriptors;
    }

    private function answeredCompetitorsByForm(Collection $formIds): Collection
    {
        if ($formIds->isEmpty()) {
            return collect();
        }

        $references = Answer::query()
            ->whereIn('form_id', $formIds)
            ->whereNotNull('competitor_id')
            ->select(['form_id', 'competitor_id'])
            ->distinct()
            ->get();

        $competitors = Competitor::query()
            ->whereIn('id', $references->pluck('competitor_id')->unique())
            ->get(['id', 'name'])
            ->keyBy('id');

        return $references
            ->groupBy('form_id')
            ->map(fn (Collection $items) => $items
                ->map(fn (Answer $answer) => $competitors->get($answer->competitor_id))
                ->filter()
                ->unique('id')
                ->values());
    }

    private function headerLayout(array $descriptors): array
    {
        $formHeaders = array_fill(0, count($descriptors), '');
        $merges = [];
        $start = 0;

        while ($start < count($descriptors)) {
            $sectionKey = $descriptors[$start]['section_key'] ?? 'column:'.$start;
            $end = $start;

            while ($end + 1 < count($descriptors)
                && ($descriptors[$end + 1]['section_key'] ?? 'column:'.($end + 1)) === $sectionKey) {
                $end++;
            }

            $formHeaders[$start] = $descriptors[$start]['form_name'] ?? '';
            if ($end > $start) {
                $startColumn = Coordinate::stringFromColumnIndex($start + 1);
                $endColumn = Coordinate::stringFromColumnIndex($end + 1);
                $merges[] = $startColumn.'1:'.$endColumn.'1';
            }

            $start = $end + 1;
        }

        return [$formHeaders, $merges];
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

        $answerData = $lookup->get(implode(':', [$question->id, $subunitId, $competitorId]));
        if (! $answerData) {
            return null;
        }

        $payload = is_array($answerData) && array_key_exists('payload', $answerData)
            ? $answerData['payload']
            : $answerData->answer;

        return $this->answerField($payload, $descriptor['field'], $question, $descriptor['rank'] ?? null);
    }

    private function answerField(mixed $payload, string $field, Question $question, ?int $rank = null): mixed
    {
        $payload = $this->normalizePayload($payload);
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
            'ranking_value' => data_get(
                $payload,
                'value.'.$rank.'.option_id',
                data_get(
                    $payload,
                    'value.'.$rank.'.value',
                    data_get(
                        $payload,
                        $rank.'.option_id',
                        data_get($payload, $rank.'.value')
                    )
                )
            ),
            'ranking_child' => data_get($payload, $rank.'.child', data_get($payload, 'value.'.$rank.'.child')),
            'child' => data_get($payload, 'child', data_get($payload, 'children', data_get($payload, 'alasan_lainnya', []))),
            'value' => data_get($payload, 'value', data_get($payload, 'nilai', $payload)),
            default => data_get($payload, $field),
        };

        return $this->readableValue($value, $question);
    }

    private function normalizePayload(mixed $payload): mixed
    {
        for ($attempt = 0; $attempt < 2 && is_string($payload); $attempt++) {
            $decoded = json_decode($payload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }

            $payload = $decoded;
        }

        return $payload;
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

        if (in_array((int) $form->formtype_id, [2, 3, 15, 16, 17, 19], true)) {
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
