<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\Group;
use App\Models\QuestionType;
use App\Models\SurveySession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\QuestionTemplateSpreadsheet;

class QuestionController extends Controller
{
    protected $group;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function masterdata()
    {
        $questions = Question::query()
            ->inDisplayOrder()
            ->get();

        return view('/admin/masterdata/question', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'exists:forms,id',
            ],

            'questions' => [
                'required',
                'array',
                'min:1',
            ],

            'questions.*.no_header' => [
                'nullable',
                'string',
                'max:20',
            ],

            'questions.*.no' => [
                'required',
                'integer',
                'min:1',
            ],

            'questions.*.name' => [
                'required',
                'string',
                'max:1000',
            ],

            'questions.*.questiontype_id' => [
                'required',
                'exists:question_types,id',
            ],
        ]);

        $form = Form::query()
            ->where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return back()->with('error', 'Pertanyaan tidak dapat ditambahkan karena survei pada group ini sudah dimulai.');
        }

        if ((int) $form->formtype_id === 12) {
            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form tipe Description tidak dapat memiliki pertanyaan.'
                );
        }

        $allowedQuestionTypeIds = $this->getQuestionTypesByForm($form)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($validated['questions'] as $index => $questionData) {
            if (! in_array((int) $questionData['questiontype_id'], $allowedQuestionTypeIds, true)) {
                throw ValidationException::withMessages([
                    "questions.{$index}.questiontype_id" => 'Tipe pertanyaan tidak sesuai dengan tipe form yang dipilih.',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $form): void {
            foreach ($validated['questions'] as $questionData) {
                Question::create([
                    'group_id' => $form->group_id,
                    'form_id' => $form->id,
                    'no_header' => $questionData['no_header'] ?? null,
                    'no' => $questionData['no'],
                    'name' => $questionData['name'],
                    'questiontype_id' => $questionData['questiontype_id'],
                ]);
            }
        });

        return redirect()
            ->route('admin.units', [
                'id' => $form->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Pertanyaan berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $question = Question::findOrFail($id);

        if (SurveySession::query()->where('group_id', $question->group_id)->exists()) {
            return back()->with('error', 'Pertanyaan tidak dapat diubah karena survei pada group ini sudah dimulai.');
        }

        // Ambil group untuk kembali ke halaman unit/group
        $group = Group::findOrFail($question->group_id);

        $questionypes = QuestionType::all();

        return view('admin.edit.editquestion', compact('question', 'questionypes', 'group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'exists:forms,id',
            ],

            'no_header' => [
                'nullable',
                'string',
                'max:20',
            ],

            'no' => [
                'required',
                'integer',
                'min:1',
            ],

            'name' => [
                'required',
                'string',
                'max:1000',
            ],

            'questiontype_id' => [
                'required',
                'exists:question_types,id',
            ],
        ]);
        
        $form = Form::findOrFail(
            $validated['form_id']
        );

        abort_unless(
            (int) $form->group_id === (int) $validated['group_id'],
            422,
            'Form tidak sesuai dengan group yang dipilih.'
        );

        if ((int) $form->formtype_id === 12) {
            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form tipe Description tidak dapat memiliki pertanyaan.'
                );
        }

        $allowedQuestionTypeIds = $this->getQuestionTypesByForm($form)
            ->pluck('id')
            ->map(fn ($questionTypeId) => (int) $questionTypeId)
            ->all();

        if (! in_array((int) $validated['questiontype_id'], $allowedQuestionTypeIds, true)) {
            throw ValidationException::withMessages([
                'questiontype_id' => 'Tipe pertanyaan tidak sesuai dengan tipe form yang dipilih.',
            ]);
        }

        $question = Question::findOrFail($id);

        abort_unless(
            (int) $question->form_id === (int) $form->id
                && (int) $question->group_id === (int) $form->group_id,
            422,
            'Pertanyaan tidak sesuai dengan form yang dipilih.'
        );

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return back()->with('error', 'Pertanyaan tidak dapat diubah karena survei pada group ini sudah dimulai.');
        }

        $question->update([
            'group_id' =>
                $validated['group_id'],

            'form_id' =>
                $validated['form_id'],

            'no_header' =>
                $validated['no_header'] ?? null,

            'no' =>
                $validated['no'],

            'name' =>
                $validated['name'],

            'questiontype_id' =>
                $validated['questiontype_id'],
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $question->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Pertanyaan berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $question = Question::with('options')
            ->findOrFail($id);

        $groupId = $question->group_id;

        try {
            if ($question->answers()->exists()) {
                return redirect()
                    ->route('admin.units', ['id' => $groupId, 'tab' => 'question'])
                    ->with('error', 'Pertanyaan tidak dapat dihapus karena memiliki jawaban responden.');
            }

            if (SurveySession::query()->where('group_id', $question->group_id)->exists()) {
                return redirect()
                    ->route('admin.units', ['id' => $groupId, 'tab' => 'question'])
                    ->with('error', 'Pertanyaan tidak dapat dihapus karena survei pada group ini sudah dimulai.');
            }

            DB::transaction(function () use ($question) {
                /*
                * Jika tidak ada option, query ini tetap aman.
                */
                $question->options()->delete();

                $question->subUnitQuestions()->delete();

                $question->delete();
            });

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'success',
                    'Pertanyaan dan seluruh option berhasil dihapus.'
                );
        } catch (\Throwable $error) {
            report($error);

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Pertanyaan gagal dihapus.'
                );
        }
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:questions,id'],
        ]);

        $form = Form::query()->findOrFail($validated['form_id']);

        if ((int) $form->formtype_id === 12) {
            throw ValidationException::withMessages([
                'ids' => 'Form tipe Description tidak memiliki pertanyaan yang dapat dihapus.',
            ]);
        }

        $questions = Question::query()
            ->where('form_id', $form->id)
            ->whereIn('id', $validated['ids'])
            ->get();

        if ($questions->count() !== count($validated['ids'])) {
            throw ValidationException::withMessages([
                'ids' => 'Sebagian pertanyaan tidak berasal dari form yang dipilih.',
            ]);
        }

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return redirect()
                ->route('admin.units', ['id' => $form->group_id, 'tab' => 'question'])
                ->with('error', 'Pertanyaan tidak dapat dihapus karena survei pada group ini sudah dimulai.');
        }

        if (DB::table('answers')->whereIn('question_id', $validated['ids'])->exists()) {
            return redirect()
                ->route('admin.units', ['id' => $form->group_id, 'tab' => 'question'])
                ->with('error', 'Pertanyaan terpilih tidak dapat dihapus karena memiliki jawaban responden.');
        }

        DB::transaction(function () use ($validated): void {
            DB::table('options')->whereIn('question_id', $validated['ids'])->delete();
            DB::table('subunit_questions')->whereIn('question_id', $validated['ids'])->delete();
            Question::query()->whereIn('id', $validated['ids'])->delete();
        });

        return redirect()
            ->route('admin.units', ['id' => $form->group_id, 'tab' => 'question'])
            ->with('successdelete', count($validated['ids']).' pertanyaan berhasil dihapus.');
    }

    
    private function getQuestionTypesByForm(Form $form): Collection
    {
        $formTypeId = (int) $form->formtype_id;

        /*
        |--------------------------------------------------------------------------
        | 1. General Questionnaire
        |--------------------------------------------------------------------------
        | Mengambil semua tipe pertanyaan dari database.
        */
        if ($formTypeId === 1) {
            return QuestionType::query()
                ->orderBy('id')
                ->get()
                ->map(function ($questionType) {
                    return [
                        'id' => (int) $questionType->id,
                        'name' => $questionType->name,
                        'description' => $questionType->description ?? '',
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Customer Assessment 1–5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 2) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Digunakan sebagai judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Kepentingan & Kinerja',
                    'description' => 'Penilaian Kepentingan dan Kinerja skala 1 sampai 5.',
                ],
                [
                    'id' => 3,
                    'name' => 'Kepentingan & Kinerja dengan Alasan',
                    'description' => 'Penilaian Kepentingan dan Kinerja dengan textarea alasan.',
                ],
                [
                    'id' => 4,
                    'name' => 'Kepentingan & Kinerja dengan Pilihan Alasan',
                    'description' => 'Penilaian dengan pilihan alasan checkbox dan textarea lanjutan.',
                ],
                [
                    'id' => 5,
                    'name' => 'Satu Indikator',
                    'description' => 'Penilaian menggunakan satu indikator skala 1 sampai 5.',
                ],
                [
                    'id' => 6,
                    'name' => 'Jawaban Textarea',
                    'description' => 'Pertanyaan dengan jawaban berbentuk textarea.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Customer Assessment 1–7
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 3) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Digunakan sebagai judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Kepentingan & Kinerja',
                    'description' => 'Penilaian Kepentingan dan Kinerja skala 1 sampai 7.',
                ],
                [
                    'id' => 3,
                    'name' => 'Kepentingan & Kinerja dengan Alasan',
                    'description' => 'Penilaian Kepentingan dan Kinerja dengan textarea alasan.',
                ],
                [
                    'id' => 4,
                    'name' => 'Kepentingan & Kinerja dengan Pilihan Alasan',
                    'description' => 'Penilaian dengan pilihan alasan checkbox dan textarea lanjutan.',
                ],
                [
                    'id' => 5,
                    'name' => 'Satu Indikator',
                    'description' => 'Penilaian menggunakan satu indikator skala 1 sampai 7.',
                ],
                [
                    'id' => 6,
                    'name' => 'Jawaban Textarea',
                    'description' => 'Pertanyaan dengan jawaban berbentuk textarea.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Engagement Assessment 1–5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 4) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' => 'Pertanyaan penilaian keterikatan skala 1 sampai 5.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Engagement Assessment 1–7
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 5) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' => 'Pertanyaan penilaian keterikatan skala 1 sampai 7.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Ranking 1–3
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 6) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan ranking.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' => 'Pertanyaan dengan pilihan ranking 1 sampai 3.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Ranking 1–5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 7) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan ranking.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' => 'Pertanyaan dengan pilihan ranking 1 sampai 5.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Strength–Complaint–Suggestion
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 8) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Keunggulan, Keluhan, dan Saran',
                    'description' => 'Pertanyaan dengan tiga jawaban: Keunggulan, Keluhan, dan Saran.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Complaint–Suggestion
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 9) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Keluhan dan Saran',
                    'description' => 'Pertanyaan dengan dua jawaban: Keluhan dan Saran.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Suggestion
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 10) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Saran',
                    'description' => 'Pertanyaan dengan jawaban berupa saran atau masukan.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Competitor
        |--------------------------------------------------------------------------
        */
        elseif (in_array($formTypeId, [11, 13], true)) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' => 'Judul atau pemisah kelompok pertanyaan kompetitor.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' => 'Pertanyaan penilaian terhadap kompetitor.',
                ],
            ]);
        }

        return collect();
    }

    public function downloadTemplate(
        $formId,
        QuestionTemplateSpreadsheet $templateService
        ) {
        $form = Form::query()
            ->with('group')
            ->findOrFail($formId);

        if ((int) $form->formtype_id === 12) {
            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form tipe Description tidak dapat memiliki pertanyaan.'
                );
        }

        $questionTypes = $this->getQuestionTypesByForm(
            $form
        );

        $spreadsheet = $templateService->create(
            $form,
            $questionTypes
        );

        $fileName = 'template-pertanyaan-form-'
            . $form->id
            . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                    $spreadsheet
                );

                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
            },
            $fileName,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
        }

    public function import(Request $request, $formId)
    {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],

            'group_id' => [
                'required',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'exists:forms,id',
            ],
        ]);

        $form = Form::query()
            ->where('id', $formId)
            ->where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return back()->with('error', 'Pertanyaan tidak dapat diimport karena survei pada group ini sudah dimulai.');
        }

        if ((int) $form->formtype_id === 12) {
            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form tipe Description tidak dapat memiliki pertanyaan.'
                );
        }

        try {
            $spreadsheet = IOFactory::load(
                $request->file('file')->getRealPath()
            );

            $questionSheet = $spreadsheet->getSheetByName(
                'INPUT_PERTANYAAN'
            );

            $optionSheet = $spreadsheet->getSheetByName(
                'INPUT_OPTIONS'
            );

            $masterFormSheet = $spreadsheet->getSheetByName(
                'MASTER_FORM'
            );

            if (!$questionSheet) {
                throw ValidationException::withMessages([
                    'file' => 'Sheet INPUT_PERTANYAAN tidak ditemukan.',
                ]);
            }

            if (!$optionSheet) {
                throw ValidationException::withMessages([
                    'file' => 'Sheet INPUT_OPTIONS tidak ditemukan.',
                ]);
            }

            if (!$masterFormSheet) {
                throw ValidationException::withMessages([
                    'file' => 'Sheet MASTER_FORM tidak ditemukan.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan template berasal dari form yang benar
            |--------------------------------------------------------------------------
            */
            $templateFormId = (int) $masterFormSheet
                ->getCell('A2')
                ->getValue();

            if ($templateFormId !== (int) $form->id) {
                throw ValidationException::withMessages([
                    'file' => 'Template Excel tidak sesuai dengan form yang dipilih.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil semua baris Excel
            |--------------------------------------------------------------------------
            */
            $questionRows = $questionSheet->toArray(
                null,
                true,
                true,
                false
            );

            $optionRows = $optionSheet->toArray(
                null,
                true,
                true,
                false
            );

            $this->validateQuestionHeaders($questionRows);
            $this->validateOptionHeaders($optionRows);

            /*
            |--------------------------------------------------------------------------
            | Tipe pertanyaan yang diizinkan untuk form
            |--------------------------------------------------------------------------
            */
            $allowedQuestionTypeIds = $this
                ->getQuestionTypesByForm($form)
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all();

            $questionsForImport = [];
            $questionCodes = [];

            /*
            |--------------------------------------------------------------------------
            | Baca INPUT_PERTANYAAN
            |--------------------------------------------------------------------------
            */
            foreach ($questionRows as $index => $row) {
                /*
                | Baris pertama adalah header.
                */
                if ($index === 0) {
                    continue;
                }

                $excelRow = $index + 1;

                $code = trim((string) ($row[0] ?? ''));
                $noHeader = trim((string) ($row[2] ?? ''));
                $number = trim((string) ($row[3] ?? ''));
                $name = trim((string) ($row[4] ?? ''));
                $questionTypeValue = trim(
                    (string) ($row[5] ?? '')
                );

                /*
                | Abaikan baris yang benar-benar kosong.
                | Kolom form tidak diperiksa karena sudah terisi otomatis.
                */
                $isEmpty = $code === ''
                    && $noHeader === ''
                    && $number === ''
                    && $name === ''
                    && $questionTypeValue === '';

                if ($isEmpty) {
                    continue;
                }

                if ($code === '') {
                    throw ValidationException::withMessages([
                        'file' => "Kode pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if ($number === '') {
                    throw ValidationException::withMessages([
                        'file' => "Nomor pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if ($name === '') {
                    throw ValidationException::withMessages([
                        'file' => "Nama pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if (mb_strlen($noHeader) > 20) {
                    throw ValidationException::withMessages([
                        'file' => "No header pada baris {$excelRow} maksimal 20 karakter.",
                    ]);
                }

                if (filter_var($number, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                    throw ValidationException::withMessages([
                        'file' => "Nomor pertanyaan pada baris {$excelRow} harus bilangan bulat minimal 1.",
                    ]);
                }

                if (mb_strlen($name) > 1000) {
                    throw ValidationException::withMessages([
                        'file' => "Nama pertanyaan pada baris {$excelRow} maksimal 1000 karakter.",
                    ]);
                }

                $questionTypeId = $this->extractReferenceId(
                    $questionTypeValue
                );

                if (!$questionTypeId) {
                    throw ValidationException::withMessages([
                        'file' => "Tipe pertanyaan pada baris {$excelRow} tidak valid.",
                    ]);
                }

                if (
                    !in_array(
                        $questionTypeId,
                        $allowedQuestionTypeIds,
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' => "Tipe pertanyaan pada baris {$excelRow} tidak diizinkan untuk form ini.",
                    ]);
                }

                $normalizedCode = strtoupper($code);

                if (isset($questionCodes[$normalizedCode])) {
                    throw ValidationException::withMessages([
                        'file' => "Kode pertanyaan {$code} digunakan lebih dari satu kali.",
                    ]);
                }

                $questionCodes[$normalizedCode] = true;

                $questionsForImport[$normalizedCode] = [
                    'code' => $code,
                    'no_header' => $noHeader,
                    'no' => $number,
                    'name' => $name,
                    'questiontype_id' => $questionTypeId,
                ];
            }

            if (empty($questionsForImport)) {
                throw ValidationException::withMessages([
                    'file' => 'Tidak ada pertanyaan yang dapat diimport.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Baca INPUT_OPTIONS
            |--------------------------------------------------------------------------
            */
            $optionsForImport = [];

            foreach ($optionRows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $excelRow = $index + 1;

                $questionCode = trim(
                    (string) ($row[0] ?? '')
                );

                $number = trim(
                    (string) ($row[1] ?? '')
                );

                $answerText = trim(
                    (string) ($row[2] ?? '')
                );

                $hasChildValue = trim(
                    (string) ($row[3] ?? '')
                );

                $answerText2 = trim(
                    (string) ($row[4] ?? '')
                );

                $isEmpty = $questionCode === ''
                    && $number === ''
                    && $answerText === ''
                    && $hasChildValue === ''
                    && $answerText2 === '';

                if ($isEmpty) {
                    continue;
                }

                if ($questionCode === '') {
                    throw ValidationException::withMessages([
                        'file' => "Kode pertanyaan option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                $normalizedCode = strtoupper(
                    $questionCode
                );

                if (!isset($questionsForImport[$normalizedCode])) {
                    throw ValidationException::withMessages([
                        'file' => "Kode pertanyaan {$questionCode} pada sheet INPUT_OPTIONS tidak ditemukan.",
                    ]);
                }

                if ($number === '') {
                    throw ValidationException::withMessages([
                        'file' => "Urutan option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if ($answerText === '') {
                    throw ValidationException::withMessages([
                        'file' => "Nama option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if (filter_var($number, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                    throw ValidationException::withMessages([
                        'file' => "Urutan option pada baris {$excelRow} harus bilangan bulat minimal 1.",
                    ]);
                }

                if (mb_strlen($answerText) > 255) {
                    throw ValidationException::withMessages([
                        'file' => "Nama option pada baris {$excelRow} maksimal 255 karakter.",
                    ]);
                }

                $hasChild = $this->extractReferenceId(
                    $hasChildValue
                );

                if (
                    $hasChild === null
                    || !in_array(
                        $hasChild,
                        [0, 1],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' => "Has child pada baris {$excelRow} harus dipilih dari dropdown: 0 - Tidak atau 1 - Iya.",
                    ]);
                }

                if ((int) $hasChild === 1 && $answerText2 === '') {
                    throw ValidationException::withMessages([
                        'file' => "Label child pada baris {$excelRow} wajib diisi ketika has_child bernilai 1.",
                    ]);
                }

                if (mb_strlen($answerText2) > 255) {
                    throw ValidationException::withMessages([
                        'file' => "Label child pada baris {$excelRow} maksimal 255 karakter.",
                    ]);
                }

                $optionsForImport[$normalizedCode][] = [
                    'no' => $number,
                    'answer_text' => $answerText,
                    'answer_text2' => $answerText2 !== ''
                        ? $answerText2
                        : null,
                    'has_child' => $hasChild,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan dengan transaksi
            |--------------------------------------------------------------------------
            */
            $result = DB::transaction(function () use (
                $form,
                $questionsForImport,
                $optionsForImport
            ) {
                $questionCount = 0;
                $optionCount = 0;

                foreach ($questionsForImport as $code => $questionData) {
                    $question = Question::create([
                        'group_id' => $form->group_id,
                        'form_id' => $form->id,
                        'no_header' => $questionData['no_header'],
                        'no' => $questionData['no'],
                        'name' => $questionData['name'],
                        'questiontype_id' =>
                            $questionData['questiontype_id'],
                    ]);

                    $questionCount++;

                    foreach (
                        $optionsForImport[$code] ?? []
                        as $optionData
                    ) {
                        $question->options()->create([
                            'no' => $optionData['no'],
                            'answer_text' =>
                                $optionData['answer_text'],
                            'answer_text2' =>
                                $optionData['answer_text2'],
                            'has_child' =>
                                $optionData['has_child'],
                        ]);

                        $optionCount++;
                    }
                }

                return [
                    'questions' => $questionCount,
                    'options' => $optionCount,
                ];
            });

            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'success',
                    "{$result['questions']} pertanyaan dan "
                    . "{$result['options']} option berhasil diimport."
                );
        } catch (ValidationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            report($error);

            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Import pertanyaan gagal. Periksa kembali format file Excel.'
                );
        }
    }

    private function validateQuestionHeaders(array $rows): void
    {
        $expectedHeaders = [
            'kode_pertanyaan',
            'form',
            'no_header',
            'no',
            'nama_pertanyaan',
            'tipe_pertanyaan',
        ];

        $actualHeaders = array_map(
            function ($value) {
                return strtolower(
                    trim((string) $value)
                );
            },
            array_slice($rows[0] ?? [], 0, 6)
        );

        if ($actualHeaders !== $expectedHeaders) {
            throw ValidationException::withMessages([
                'file' => 'Judul kolom pada sheet INPUT_PERTANYAAN tidak sesuai template.',
            ]);
        }
    }


    private function validateOptionHeaders(array $rows): void
    {
        $expectedHeaders = [
            'kode_pertanyaan',
            'urutan',
            'nama_option',
            'has_child',
            'answer_text2',
        ];

        $actualHeaders = array_map(
            function ($value) {
                return strtolower(
                    trim((string) $value)
                );
            },
            array_slice($rows[0] ?? [], 0, 5)
        );

        if ($actualHeaders !== $expectedHeaders) {
            throw ValidationException::withMessages([
                'file' => 'Judul kolom pada sheet INPUT_OPTIONS tidak sesuai template.',
            ]);
        }
    }


    private function extractReferenceId(string $value): ?int
    {
        if (
            preg_match(
                '/^\s*(\d+)\s*(?:-|$)/',
                $value,
                $matches
            )
        ) {
            return (int) $matches[1];
        }

        return null;
    }
}
