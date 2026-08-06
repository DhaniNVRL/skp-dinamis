<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Group;
use App\Models\Form;
use App\Models\Activity;
use App\Models\FormType;
use App\Models\QuestionType;
use App\Models\Question;
use App\Models\SubUnit;
use App\Models\Competitor;
use App\Models\SubUnitQuestion;
use App\Models\Answer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SubUnitController extends Controller
{
    public function index(Request $request, $id)
    {
        // $id merupakan ID Unit.
        $unit = Unit::with('group.activity')
            ->findOrFail($id);

        // Ambil activity melalui relasi unit, bukan menggunakan ID Unit.
        $activity = $unit->group?->activity;

        if (!$unit->group) {
            abort(404, 'Group untuk unit ini tidak ditemukan.');
        }

        if (!$activity) {
            abort(404, 'Activity untuk unit ini tidak ditemukan.');
        }

        $search = trim(
            (string) $request->query('search', '')
        );

        $subunits = SubUnit::query()
            ->where('unit_id', $unit->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                );
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $allSubunits = SubUnit::query()
            ->where('unit_id', $unit->id)
            ->orderBy('name')
            ->get();

        $forms = Form::query()
            ->where('group_id', $unit->group_id)
            ->with([
                'formtype',
                'questions' => function ($query) {
                    $query
                        ->orderBy('no_header')
                        ->orderBy('no');
                },
                'questions.options' => function ($query) {
                    $query->orderBy('no');
                },
            ])
            ->orderBy('no_urut')
            ->get();

        $activeMapSubUnit = SubUnitQuestion::query()
            ->whereIn('form_id', $forms->pluck('id'))
            ->whereIn('subunit_id', $allSubunits->pluck('id'))
            ->get()
            ->groupBy(function ($item) {
                return $item->form_id . '-' . $item->question_id;
            })
            ->map(function ($items) {
                return $items
                    ->pluck('subunit_id')
                    ->map(fn ($subunitId) => (int) $subunitId)
                    ->unique()
                    ->values()
                    ->all();
            })
            ->all();

        $competitors = Competitor::query()
            ->where('group_id', $unit->group_id)
            ->orderBy('name')
            ->get();

        $allowedTabs = [
            'subunit',
            'hide-show',
            'question-preview',
        ];

        $activeTab = $request->query('tab', 'subunit');

        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'subunit';
        }

        return view('admin.subunit.index', [
            'units' => $unit,
            'unit' => $unit,
            'activity' => $activity,
            'subunits' => $subunits,
            'allSubunits' => $allSubunits,
            'forms' => $forms,
            'competitors' => $competitors,
            'activeMapSubUnit' => $activeMapSubUnit,
            'activeTab' => $activeTab,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'unit_id' => [
                    'required',
                    'integer',
                    'exists:units,id',
                ],

                'subunits' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'subunits.*.name' => [
                    'required',
                    'string',
                    'max:500',
                ],
            ],
            [
                'unit_id.required' =>
                    'Unit tidak ditemukan.',

                'unit_id.exists' =>
                    'Unit tidak valid.',

                'subunits.required' =>
                    'Data Sub Unit wajib diisi.',

                'subunits.min' =>
                    'Minimal satu Sub Unit wajib diisi.',

                'subunits.*.name.required' =>
                    'Nama Sub Unit wajib diisi.',

                'subunits.*.name.max' =>
                    'Nama Sub Unit maksimal 500 karakter.',
            ]
        );

        $inserted = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $validated,
            &$inserted,
            &$skipped
        ) {
            foreach ($validated['subunits'] as $row) {
                $name = trim($row['name']);

                $exists = SubUnit::query()
                    ->where(
                        'unit_id',
                        $validated['unit_id']
                    )
                    ->where('name', $name)
                    ->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                SubUnit::create([
                    'unit_id' =>
                        $validated['unit_id'],

                    'name' =>
                        $name,
                ]);

                $inserted++;
            }
        });

        $message =
            "{$inserted} Sub Unit berhasil ditambahkan.";

        if ($skipped > 0) {
            $message .=
                " {$skipped} data duplikat dilewati.";
        }

        return $this->redirectToSubUnit(
            $validated['unit_id'],
            'success',
            $message
        );
    }

    public function update(Request $request, $id)
    {
        $subunit = SubUnit::findOrFail($id);

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:500',
                ],
            ],
            [
                'name.required' =>
                    'Nama Sub Unit wajib diisi.',

                'name.max' =>
                    'Nama Sub Unit maksimal 500 karakter.',
            ]
        );

        $name = trim($validated['name']);

        $exists = SubUnit::query()
            ->where('unit_id', $subunit->unit_id)
            ->where('name', $name)
            ->where('id', '!=', $subunit->id)
            ->exists();

        if ($exists) {
            return $this->redirectToSubUnit(
                $subunit->unit_id,
                'error',
                'Nama Sub Unit tersebut sudah digunakan.'
            );
        }

        $subunit->update([
            'name' => $name,
        ]);

        return $this->redirectToSubUnit(
            $subunit->unit_id,
            'success',
            'Sub Unit berhasil diperbarui.'
        );
    }

    public function destroy($id)
    {
        $subunit = SubUnit::findOrFail($id);
        $unitId = $subunit->unit_id;

        if (Answer::query()->where('subunit_id', $subunit->id)->exists()) {
            return $this->redirectToSubUnit(
                $unitId,
                'error',
                'Sub Unit tidak dapat dihapus karena memiliki jawaban responden.'
            );
        }

        DB::transaction(function () use ($subunit) {
            SubUnitQuestion::query()
                ->where('subunit_id', $subunit->id)
                ->delete();

            $subunit->delete();
        });

        return $this->redirectToSubUnit(
            $unitId,
            'successdelete',
            'Sub Unit berhasil dihapus.'
        );
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate(
            [
                'unit_id' => [
                    'required',
                    'integer',
                    'exists:units,id',
                ],

                'selected' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'selected.*' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:subunits,id',
                ],
            ],
            [
                'selected.required' =>
                    'Pilih minimal satu Sub Unit.',

                'selected.min' =>
                    'Pilih minimal satu Sub Unit.',
            ]
        );

        $subunits = SubUnit::query()
            ->where(
                'unit_id',
                $validated['unit_id']
            )
            ->whereIn(
                'id',
                $validated['selected']
            )
            ->get();

        if ($subunits->isEmpty()) {
            return $this->redirectToSubUnit(
                $validated['unit_id'],
                'error',
                'Tidak ada Sub Unit yang dapat dihapus.'
            );
        }

        $subunitIds = $subunits->pluck('id');
        $deletedCount = $subunits->count();

        if ($subunits->count() !== count($validated['selected'])) {
            return $this->redirectToSubUnit(
                $validated['unit_id'],
                'error',
                'Sebagian Sub Unit tidak sesuai dengan Unit yang dipilih.'
            );
        }

        if (Answer::query()->whereIn('subunit_id', $subunitIds)->exists()) {
            return $this->redirectToSubUnit(
                $validated['unit_id'],
                'error',
                'Sebagian Sub Unit memiliki jawaban responden dan tidak dapat dihapus.'
            );
        }

        DB::transaction(function () use (
            $subunitIds,
            $subunits
        ) {
            SubUnitQuestion::query()
                ->whereIn('subunit_id', $subunitIds)
                ->delete();

            foreach ($subunits as $subunit) {
                $subunit->delete();
            }
        });

        return $this->redirectToSubUnit(
            $validated['unit_id'],
            'successdelete',
            "{$deletedCount} Sub Unit berhasil dihapus."
        );
    }

    public function export($unitId)
    {
        $unit = Unit::findOrFail($unitId);

        $spreadsheet = new Spreadsheet();

        /*
         * SHEET INPUT
         */
        $inputSheet = $spreadsheet->getActiveSheet();
        $inputSheet->setTitle('Input Sub Unit');

        $inputSheet->setCellValue(
            'A1',
            'NAMA SUB UNIT'
        );

        $this->styleHeader(
            $inputSheet,
            'A1:A1'
        );

        $inputSheet
            ->getColumnDimension('A')
            ->setWidth(50);

        $inputSheet->freezePane('A2');

        /*
         * SHEET INFORMASI UNIT
         */
        $unitSheet = $spreadsheet->createSheet();
        $unitSheet->setTitle('Informasi Unit');

        $unitSheet->fromArray(
            [
                [
                    'ID UNIT',
                    'NAMA UNIT',
                ],
                [
                    $unit->id,
                    $unit->name,
                ],
            ],
            null,
            'A1'
        );

        $this->styleHeader(
            $unitSheet,
            'A1:B1'
        );

        $unitSheet
            ->getColumnDimension('A')
            ->setWidth(18);

        $unitSheet
            ->getColumnDimension('B')
            ->setWidth(50);

        /*
         * SHEET PETUNJUK
         */
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');

        $instructionSheet->fromArray(
            [
                ['PETUNJUK IMPORT SUB UNIT'],
                [
                    '1. Isi nama Sub Unit pada sheet "Input Sub Unit".',
                ],
                [
                    '2. Jangan mengubah nama sheet dan judul kolom.',
                ],
                [
                    '3. Data mulai dibaca dari baris kedua.',
                ],
                [
                    '4. Baris kosong akan dilewati.',
                ],
                [
                    '5. Data dengan nama yang sudah ada akan dilewati.',
                ],
                [
                    '6. Simpan kembali file dalam format XLSX atau XLS.',
                ],
            ],
            null,
            'A1'
        );

        $this->styleHeader(
            $instructionSheet,
            'A1:A1'
        );

        $instructionSheet
            ->getColumnDimension('A')
            ->setWidth(90);

        $instructionSheet
            ->getStyle('A1:A7')
            ->getAlignment()
            ->setWrapText(true);

        $spreadsheet->setActiveSheetIndex(0);

        $filename =
            'template-sub-unit-' .
            Str::slug($unit->name ?: 'unit') .
            '.xlsx';

        return $this->downloadSpreadsheet(
            $spreadsheet,
            $filename
        );
    }

    public function downloadTemplate($unitId)
    {
        return $this->export($unitId);
    }

    public function import(Request $request)
    {
        $validated = $request->validate(
            [
                'unit_id' => [
                    'required',
                    'integer',
                    'exists:units,id',
                ],

                'file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls',
                    'max:10240',
                ],
            ],
            [
                'file.required' =>
                    'File import wajib dipilih.',

                'file.mimes' =>
                    'File harus berformat XLSX atau XLS.',

                'file.max' =>
                    'Ukuran file maksimal 10 MB.',
            ]
        );

        try {
            $file = $request->file('file');

            $spreadsheet = IOFactory::load(
                $file->getRealPath()
            );

            $sheet = $spreadsheet->getSheetByName(
                'Input Sub Unit'
            );

            if (!$sheet) {
                return $this->redirectToSubUnit(
                    $validated['unit_id'],
                    'error',
                    'Sheet "Input Sub Unit" tidak ditemukan.'
                );
            }

            $highestRow = $sheet->getHighestDataRow();

            if ($highestRow < 2) {
                return $this->redirectToSubUnit(
                    $validated['unit_id'],
                    'error',
                    'File tidak memiliki data Sub Unit.'
                );
            }

            $rows = [];
            $inserted = 0;
            $skipped = 0;

            for (
                $rowNumber = 2;
                $rowNumber <= $highestRow;
                $rowNumber++
            ) {
                $name = trim(
                    (string) $sheet
                        ->getCell("A{$rowNumber}")
                        ->getValue()
                );

                if ($name === '') {
                    continue;
                }

                $rowValidator = Validator::make(
                    [
                        'name' => $name,
                    ],
                    [
                        'name' => [
                            'required',
                            'string',
                            'max:500',
                        ],
                    ]
                );

                if ($rowValidator->fails()) {
                    return $this->redirectToSubUnit(
                        $validated['unit_id'],
                        'error',
                        "Data pada baris {$rowNumber} tidak valid."
                    );
                }

                $rows[] = [
                    'name' => $name,
                ];
            }

            if (empty($rows)) {
                return $this->redirectToSubUnit(
                    $validated['unit_id'],
                    'error',
                    'Tidak ada data yang dapat diimport.'
                );
            }

            DB::transaction(function () use (
                $rows,
                $validated,
                &$inserted,
                &$skipped
            ) {
                foreach ($rows as $row) {
                    $exists = SubUnit::query()
                        ->where(
                            'unit_id',
                            $validated['unit_id']
                        )
                        ->where(
                            'name',
                            $row['name']
                        )
                        ->exists();

                    if ($exists) {
                        $skipped++;

                        continue;
                    }

                    SubUnit::create([
                        'unit_id' =>
                            $validated['unit_id'],

                        'name' =>
                            $row['name'],
                    ]);

                    $inserted++;
                }
            });

            $spreadsheet->disconnectWorksheets();

            return $this->redirectToSubUnit(
                $validated['unit_id'],
                'success',
                "Import selesai. {$inserted} data ditambahkan dan " .
                "{$skipped} data duplikat dilewati."
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->redirectToSubUnit(
                $validated['unit_id'],
                'error',
                'Import gagal. Pastikan file sesuai template.'
            );
        }
    }

    private function redirectToSubUnit(
        int $unitId,
        string $sessionKey,
        string $message
    ) {
        return redirect()
            ->route('admin.subunit', [
                'id' => $unitId,
                'tab' => 'subunit',
            ])
            ->with(
                $sessionKey,
                $message
            );
    }

    private function styleHeader(
        $sheet,
        string $range
    ): void {
        $sheet
            ->getStyle($range)
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setARGB('FFFFFFFF');

        $sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF2563EB');

        $sheet
            ->getStyle($range)
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            )
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );
    }

    private function downloadSpreadsheet(
        Spreadsheet $spreadsheet,
        string $filename
    ) {
        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);

                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                'Cache-Control' =>
                    'max-age=0, no-cache, no-store, must-revalidate',
            ]
        );
    }
}
