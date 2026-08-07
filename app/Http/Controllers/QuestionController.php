<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Group;
use App\Models\Question;
use App\Models\QuestionType;
use App\Services\QuestionTemplateSpreadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuestionController extends Controller
{
    protected $group;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Master data question.
     */
    public function masterdata()
    {
        $questions = Question::query()
            ->inDisplayOrder()
            ->get();

        return view(
            '/admin/masterdata/question',
            compact('questions')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store pertanyaan.
     *
     * Catatan:
     * - Survei yang sudah berjalan TIDAK menghalangi penambahan.
     * - no boleh 0.
     * - no boleh 3.1, 3.10, dst.
     * - no boleh sama/kembar.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'integer',
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

            /*
            |--------------------------------------------------------------------------
            | NO PERTANYAAN
            |--------------------------------------------------------------------------
            | Contoh yang diperbolehkan:
            |
            | 0
            | 1
            | 2
            | 3.1
            | 3.2
            | 3.10
            | 3.11
            |
            | Tidak menggunakan unique/distinct,
            | sehingga nilai yang sama diperbolehkan.
            */
            'questions.*.no' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d+(?:\.\d+)?$/',
            ],

            'questions.*.name' => [
                'required',
                'string',
                'max:1000',
            ],

            'questions.*.questiontype_id' => [
                'required',
                'integer',
                'exists:question_types,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan form sesuai dengan group
        |--------------------------------------------------------------------------
        */
        $form = Form::query()
            ->where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Description tidak memiliki pertanyaan
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Tipe pertanyaan yang diperbolehkan
        |--------------------------------------------------------------------------
        */
        $allowedQuestionTypeIds = $this
            ->getQuestionTypesByForm($form)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (
            $validated['questions']
            as $index => $questionData
        ) {
            if (
                ! in_array(
                    (int) $questionData['questiontype_id'],
                    $allowedQuestionTypeIds,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    "questions.{$index}.questiontype_id" =>
                        'Tipe pertanyaan tidak sesuai dengan tipe form yang dipilih.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan pertanyaan
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use (
            $validated,
            $form
        ): void {
            foreach (
                $validated['questions']
                as $questionData
            ) {
                Question::create([
                    'group_id' =>
                        $form->group_id,

                    'form_id' =>
                        $form->id,

                    'no_header' =>
                        $questionData['no_header'] ?? null,

                    /*
                    | Simpan sebagai string.
                    | Jangan cast menjadi integer/float.
                    */
                    'no' =>
                        (string) $questionData['no'],

                    'name' =>
                        $questionData['name'],

                    'questiontype_id' =>
                        $questionData['questiontype_id'],
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
     * Edit pertanyaan.
     */
    public function edit($id)
    {
        $question = Question::query()
            ->findOrFail($id);

        $group = Group::query()
            ->findOrFail($question->group_id);

        $questionypes = QuestionType::query()
            ->orderBy('id')
            ->get();

        return view(
            'admin.edit.editquestion',
            compact(
                'question',
                'questionypes',
                'group'
            )
        );
    }

    /**
     * Update pertanyaan.
     *
     * Tetap dapat dilakukan ketika survey sudah berjalan.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],

            'no_header' => [
                'nullable',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | NO
            |--------------------------------------------------------------------------
            | 0 diperbolehkan.
            | 3.1, 3.10, dst diperbolehkan.
            | Nomor sama dengan pertanyaan lain diperbolehkan.
            */
            'no' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d+(?:\.\d+)?$/',
            ],

            'name' => [
                'required',
                'string',
                'max:1000',
            ],

            'questiontype_id' => [
                'required',
                'integer',
                'exists:question_types,id',
            ],
        ]);

        $form = Form::query()
            ->findOrFail(
                $validated['form_id']
            );

        /*
        |--------------------------------------------------------------------------
        | Pastikan form milik group
        |--------------------------------------------------------------------------
        */
        abort_unless(
            (int) $form->group_id ===
            (int) $validated['group_id'],
            422,
            'Form tidak sesuai dengan group yang dipilih.'
        );

        /*
        |--------------------------------------------------------------------------
        | Form Description
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Validasi Question Type
        |--------------------------------------------------------------------------
        */
        $allowedQuestionTypeIds = $this
            ->getQuestionTypesByForm($form)
            ->pluck('id')
            ->map(
                fn ($questionTypeId) =>
                    (int) $questionTypeId
            )
            ->all();

        if (
            ! in_array(
                (int) $validated['questiontype_id'],
                $allowedQuestionTypeIds,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'questiontype_id' =>
                    'Tipe pertanyaan tidak sesuai dengan tipe form yang dipilih.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Question
        |--------------------------------------------------------------------------
        */
        $question = Question::query()
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Pastikan question benar-benar dari form tersebut
        |--------------------------------------------------------------------------
        */
        abort_unless(
            (int) $question->form_id ===
                (int) $form->id
            &&
            (int) $question->group_id ===
                (int) $form->group_id,
            422,
            'Pertanyaan tidak sesuai dengan form yang dipilih.'
        );

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use (
            $question,
            $validated
        ): void {
            $question->update([
                'group_id' =>
                    $validated['group_id'],

                'form_id' =>
                    $validated['form_id'],

                'no_header' =>
                    $validated['no_header'] ?? null,

                'no' =>
                    (string) $validated['no'],

                'name' =>
                    $validated['name'],

                'questiontype_id' =>
                    $validated['questiontype_id'],
            ]);
        });

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
     * Delete satu pertanyaan.
     *
     * Yang ikut dihapus:
     * - answers
     * - options
     * - subunit_questions
     * - question
     */
    public function destroy($id)
    {
        $question = Question::query()
            ->findOrFail($id);

        $groupId = $question->group_id;

        try {
            DB::transaction(function () use (
                $question
            ): void {
                /*
                |--------------------------------------------------------------------------
                | 1. Hapus jawaban
                |--------------------------------------------------------------------------
                */
                Answer::query()
                    ->where(
                        'question_id',
                        $question->id
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 2. Hapus option
                |--------------------------------------------------------------------------
                */
                DB::table('options')
                    ->where(
                        'question_id',
                        $question->id
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 3. Hapus konfigurasi sub unit
                |--------------------------------------------------------------------------
                */
                DB::table('subunit_questions')
                    ->where(
                        'question_id',
                        $question->id
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 4. Hapus question
                |--------------------------------------------------------------------------
                */
                $question->delete();
            });

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'successdelete',
                    'Pertanyaan beserta option dan jawaban responden berhasil dihapus.'
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

    /**
     * Bulk delete.
     *
     * Ikut menghapus:
     * - answers
     * - options
     * - subunit_questions
     * - questions
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],

            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:questions,id',
            ],
        ]);

        $form = Form::query()
            ->findOrFail(
                $validated['form_id']
            );

        /*
        |--------------------------------------------------------------------------
        | Description tidak mempunyai question
        |--------------------------------------------------------------------------
        */
        if ((int) $form->formtype_id === 12) {
            throw ValidationException::withMessages([
                'ids' =>
                    'Form tipe Description tidak memiliki pertanyaan yang dapat dihapus.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan semua pertanyaan dari form tersebut
        |--------------------------------------------------------------------------
        */
        $questions = Question::query()
            ->where(
                'form_id',
                $form->id
            )
            ->whereIn(
                'id',
                $validated['ids']
            )
            ->get();

        if (
            $questions->count()
            !== count($validated['ids'])
        ) {
            throw ValidationException::withMessages([
                'ids' =>
                    'Sebagian pertanyaan tidak berasal dari form yang dipilih.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $validated
            ): void {
                $questionIds =
                    $validated['ids'];

                /*
                |--------------------------------------------------------------------------
                | 1. Answers
                |--------------------------------------------------------------------------
                */
                DB::table('answers')
                    ->whereIn(
                        'question_id',
                        $questionIds
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 2. Options
                |--------------------------------------------------------------------------
                */
                DB::table('options')
                    ->whereIn(
                        'question_id',
                        $questionIds
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 3. Sub Unit Question
                |--------------------------------------------------------------------------
                */
                DB::table('subunit_questions')
                    ->whereIn(
                        'question_id',
                        $questionIds
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | 4. Questions
                |--------------------------------------------------------------------------
                */
                Question::query()
                    ->whereIn(
                        'id',
                        $questionIds
                    )
                    ->delete();
            });

            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'successdelete',
                    count($validated['ids'])
                    . ' pertanyaan beserta option dan jawaban responden berhasil dihapus.'
                );
        } catch (\Throwable $error) {
            report($error);

            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Pertanyaan terpilih gagal dihapus.'
                );
        }
    }

    /**
     * Question Type berdasarkan Form Type.
     */
    private function getQuestionTypesByForm(
        Form $form
    ): Collection {
        $formTypeId =
            (int) $form->formtype_id;

        /*
        |--------------------------------------------------------------------------
        | 1. General Questionnaire
        |--------------------------------------------------------------------------
        */
        if ($formTypeId === 1) {
            return QuestionType::query()
                ->orderBy('id')
                ->get()
                ->map(function ($questionType) {
                    return [
                        'id' =>
                            (int) $questionType->id,

                        'name' =>
                            $questionType->name,

                        'description' =>
                            $questionType->description ?? '',
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Customer Assessment 1-5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 2) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Digunakan sebagai judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Kepentingan & Kinerja',
                    'description' =>
                        'Penilaian Kepentingan dan Kinerja skala 1 sampai 5.',
                ],
                [
                    'id' => 3,
                    'name' =>
                        'Kepentingan & Kinerja dengan Alasan',
                    'description' =>
                        'Penilaian Kepentingan dan Kinerja dengan textarea alasan.',
                ],
                [
                    'id' => 4,
                    'name' =>
                        'Kepentingan & Kinerja dengan Pilihan Alasan',
                    'description' =>
                        'Penilaian dengan pilihan alasan checkbox dan textarea lanjutan.',
                ],
                [
                    'id' => 5,
                    'name' => 'Satu Indikator',
                    'description' =>
                        'Penilaian menggunakan satu indikator skala 1 sampai 5.',
                ],
                [
                    'id' => 6,
                    'name' => 'Jawaban Textarea',
                    'description' =>
                        'Pertanyaan dengan jawaban berbentuk textarea.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Customer Assessment 1-7
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 3) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Digunakan sebagai judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Kepentingan & Kinerja',
                    'description' =>
                        'Penilaian Kepentingan dan Kinerja skala 1 sampai 7.',
                ],
                [
                    'id' => 3,
                    'name' =>
                        'Kepentingan & Kinerja dengan Alasan',
                    'description' =>
                        'Penilaian Kepentingan dan Kinerja dengan textarea alasan.',
                ],
                [
                    'id' => 4,
                    'name' =>
                        'Kepentingan & Kinerja dengan Pilihan Alasan',
                    'description' =>
                        'Penilaian dengan pilihan alasan checkbox dan textarea lanjutan.',
                ],
                [
                    'id' => 5,
                    'name' => 'Satu Indikator',
                    'description' =>
                        'Penilaian menggunakan satu indikator skala 1 sampai 7.',
                ],
                [
                    'id' => 6,
                    'name' => 'Jawaban Textarea',
                    'description' =>
                        'Pertanyaan dengan jawaban berbentuk textarea.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Engagement Assessment 1-5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 4) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' =>
                        'Pertanyaan penilaian keterikatan skala 1 sampai 5.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Engagement Assessment 1-7
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 5) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' =>
                        'Pertanyaan penilaian keterikatan skala 1 sampai 7.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Ranking 1-3
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 6) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan ranking.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' =>
                        'Pertanyaan dengan pilihan ranking 1 sampai 3.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Ranking 1-5
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 7) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan ranking.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' =>
                        'Pertanyaan dengan pilihan ranking 1 sampai 5.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Strength Complaint Suggestion
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 8) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' =>
                        'Keunggulan, Keluhan, dan Saran',
                    'description' =>
                        'Pertanyaan dengan tiga jawaban: Keunggulan, Keluhan, dan Saran.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Complaint Suggestion
        |--------------------------------------------------------------------------
        */
        elseif ($formTypeId === 9) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Keluhan dan Saran',
                    'description' =>
                        'Pertanyaan dengan dua jawaban: Keluhan dan Saran.',
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
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan.',
                ],
                [
                    'id' => 2,
                    'name' => 'Saran',
                    'description' =>
                        'Pertanyaan dengan jawaban berupa saran atau masukan.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 11 / 13. Competitor
        |--------------------------------------------------------------------------
        */
        elseif (
            in_array(
                $formTypeId,
                [11, 13],
                true
            )
        ) {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Judul Pertanyaan',
                    'description' =>
                        'Judul atau pemisah kelompok pertanyaan kompetitor.',
                ],
                [
                    'id' => 2,
                    'name' => 'Pertanyaan',
                    'description' =>
                        'Pertanyaan penilaian terhadap kompetitor.',
                ],
            ]);
        }

        return collect();
    }

    /**
     * Download template Excel.
     */
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

        $questionTypes =
            $this->getQuestionTypesByForm($form);

        $spreadsheet =
            $templateService->create(
                $form,
                $questionTypes
            );

        $fileName =
            'template-pertanyaan-form-'
            . $form->id
            . '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer =
                    new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                        $spreadsheet
                    );

                $writer->save(
                    'php://output'
                );

                $spreadsheet
                    ->disconnectWorksheets();
            },
            $fileName,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Import Question + Option.
     *
     * Survei yang sudah berjalan TIDAK menghalangi import.
     */
    public function import(
        Request $request,
        $formId
    ) {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],

            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan form sesuai
        |--------------------------------------------------------------------------
        */
        $form = Form::query()
            ->where('id', $formId)
            ->where(
                'id',
                $validated['form_id']
            )
            ->where(
                'group_id',
                $validated['group_id']
            )
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Description tidak boleh
        |--------------------------------------------------------------------------
        */
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
            /*
            |--------------------------------------------------------------------------
            | Buka Spreadsheet
            |--------------------------------------------------------------------------
            */
            $spreadsheet = IOFactory::load(
                $request
                    ->file('file')
                    ->getRealPath()
            );

            $questionSheet =
                $spreadsheet->getSheetByName(
                    'INPUT_PERTANYAAN'
                );

            $optionSheet =
                $spreadsheet->getSheetByName(
                    'INPUT_OPTIONS'
                );

            $masterFormSheet =
                $spreadsheet->getSheetByName(
                    'MASTER_FORM'
                );

            /*
            |--------------------------------------------------------------------------
            | Validasi sheet
            |--------------------------------------------------------------------------
            */
            if (!$questionSheet) {
                throw ValidationException::withMessages([
                    'file' =>
                        'Sheet INPUT_PERTANYAAN tidak ditemukan.',
                ]);
            }

            if (!$optionSheet) {
                throw ValidationException::withMessages([
                    'file' =>
                        'Sheet INPUT_OPTIONS tidak ditemukan.',
                ]);
            }

            if (!$masterFormSheet) {
                throw ValidationException::withMessages([
                    'file' =>
                        'Sheet MASTER_FORM tidak ditemukan.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan template berasal dari form yang benar
            |--------------------------------------------------------------------------
            */
            $templateFormId = (int)
                $masterFormSheet
                    ->getCell('A2')
                    ->getValue();

            if (
                $templateFormId !==
                (int) $form->id
            ) {
                throw ValidationException::withMessages([
                    'file' =>
                        'Template Excel tidak sesuai dengan form yang dipilih.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Baca data Excel
            |--------------------------------------------------------------------------
            */
            $questionRows =
                $questionSheet->toArray(
                    null,
                    true,
                    true,
                    false
                );

            $optionRows =
                $optionSheet->toArray(
                    null,
                    true,
                    true,
                    false
                );

            /*
            |--------------------------------------------------------------------------
            | Validasi header
            |--------------------------------------------------------------------------
            */
            $this->validateQuestionHeaders(
                $questionRows
            );

            $this->validateOptionHeaders(
                $optionRows
            );

            /*
            |--------------------------------------------------------------------------
            | Allowed Question Types
            |--------------------------------------------------------------------------
            */
            $allowedQuestionTypeIds =
                $this
                    ->getQuestionTypesByForm(
                        $form
                    )
                    ->pluck('id')
                    ->map(
                        fn ($id) => (int) $id
                    )
                    ->all();

            $questionsForImport = [];
            $questionCodes = [];

            /*
            |--------------------------------------------------------------------------
            | INPUT_PERTANYAAN
            |--------------------------------------------------------------------------
            */
            foreach (
                $questionRows
                as $index => $row
            ) {
                /*
                | Row pertama adalah header.
                */
                if ($index === 0) {
                    continue;
                }

                $excelRow =
                    $index + 1;

                $code = trim(
                    (string) ($row[0] ?? '')
                );

                $noHeader = trim(
                    (string) ($row[2] ?? '')
                );

                /*
                |--------------------------------------------------------------------------
                | NO harus dibaca sebagai string
                |--------------------------------------------------------------------------
                */
                $number = trim(
                    (string) ($row[3] ?? '')
                );

                $name = trim(
                    (string) ($row[4] ?? '')
                );

                $questionTypeValue = trim(
                    (string) ($row[5] ?? '')
                );

                /*
                |--------------------------------------------------------------------------
                | Skip row kosong
                |--------------------------------------------------------------------------
                */
                $isEmpty =
                    $code === ''
                    &&
                    $noHeader === ''
                    &&
                    $number === ''
                    &&
                    $name === ''
                    &&
                    $questionTypeValue === '';

                if ($isEmpty) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Kode
                |--------------------------------------------------------------------------
                */
                if ($code === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Kode pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | NO
                |--------------------------------------------------------------------------
                | Diperbolehkan:
                |
                | 0
                | 1
                | 2
                | 3
                | 3.1
                | 3.10
                | 3.11
                |
                | NO boleh kembar.
                |--------------------------------------------------------------------------
                */
                if ($number === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nomor pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if (
                    ! preg_match(
                        '/^\d+(?:\.\d+)?$/',
                        $number
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nomor pertanyaan pada baris {$excelRow} tidak valid. "
                            . "Gunakan format seperti 0, 1, 2, 3.1, 3.10, dan seterusnya.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Nama Question
                |--------------------------------------------------------------------------
                */
                if ($name === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nama pertanyaan pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | No Header
                |--------------------------------------------------------------------------
                */
                if (
                    mb_strlen($noHeader) > 20
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "No header pada baris {$excelRow} maksimal 20 karakter.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Question Name
                |--------------------------------------------------------------------------
                */
                if (
                    mb_strlen($name) > 1000
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nama pertanyaan pada baris {$excelRow} maksimal 1000 karakter.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Question Type
                |--------------------------------------------------------------------------
                */
                $questionTypeId =
                    $this->extractReferenceId(
                        $questionTypeValue
                    );

                if (!$questionTypeId) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Tipe pertanyaan pada baris {$excelRow} tidak valid.",
                    ]);
                }

                if (
                    ! in_array(
                        $questionTypeId,
                        $allowedQuestionTypeIds,
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Tipe pertanyaan pada baris {$excelRow} tidak diizinkan untuk form ini.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | KODE PERTANYAAN tetap harus unik
                |--------------------------------------------------------------------------
                |
                | Perhatikan:
                |
                | NO boleh sama.
                | KODE tidak boleh sama.
                |
                | Kode digunakan untuk menghubungkan pertanyaan dengan option.
                */
                $normalizedCode =
                    strtoupper($code);

                if (
                    isset(
                        $questionCodes[
                            $normalizedCode
                        ]
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Kode pertanyaan {$code} digunakan lebih dari satu kali.",
                    ]);
                }

                $questionCodes[
                    $normalizedCode
                ] = true;

                /*
                |--------------------------------------------------------------------------
                | Masukkan ke buffer import
                |--------------------------------------------------------------------------
                */
                $questionsForImport[
                    $normalizedCode
                ] = [
                    'code' =>
                        $code,

                    'no_header' =>
                        $noHeader !== ''
                            ? $noHeader
                            : null,

                    'no' =>
                        $number,

                    'name' =>
                        $name,

                    'questiontype_id' =>
                        $questionTypeId,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Tidak ada pertanyaan
            |--------------------------------------------------------------------------
            */
            if (
                empty($questionsForImport)
            ) {
                throw ValidationException::withMessages([
                    'file' =>
                        'Tidak ada pertanyaan yang dapat diimport.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | INPUT_OPTIONS
            |--------------------------------------------------------------------------
            */
            $optionsForImport = [];

            foreach (
                $optionRows
                as $index => $row
            ) {
                if ($index === 0) {
                    continue;
                }

                $excelRow =
                    $index + 1;

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

                /*
                |--------------------------------------------------------------------------
                | Skip kosong
                |--------------------------------------------------------------------------
                */
                $isEmpty =
                    $questionCode === ''
                    &&
                    $number === ''
                    &&
                    $answerText === ''
                    &&
                    $hasChildValue === ''
                    &&
                    $answerText2 === '';

                if ($isEmpty) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Question Code
                |--------------------------------------------------------------------------
                */
                if ($questionCode === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Kode pertanyaan option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                $normalizedCode =
                    strtoupper(
                        $questionCode
                    );

                if (
                    ! isset(
                        $questionsForImport[
                            $normalizedCode
                        ]
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Kode pertanyaan {$questionCode} pada sheet INPUT_OPTIONS tidak ditemukan.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Urutan Option
                |--------------------------------------------------------------------------
                */
                if ($number === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Urutan option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Untuk OPTION, tetap integer minimal 1
                |--------------------------------------------------------------------------
                */
                if (
                    filter_var(
                        $number,
                        FILTER_VALIDATE_INT,
                        [
                            'options' => [
                                'min_range' => 1,
                            ],
                        ]
                    ) === false
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Urutan option pada baris {$excelRow} harus bilangan bulat minimal 1.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Option Text
                |--------------------------------------------------------------------------
                */
                if ($answerText === '') {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nama option pada baris {$excelRow} wajib diisi.",
                    ]);
                }

                if (
                    mb_strlen($answerText) > 255
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Nama option pada baris {$excelRow} maksimal 255 karakter.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Has Child
                |--------------------------------------------------------------------------
                */
                $hasChild =
                    $this->extractReferenceId(
                        $hasChildValue
                    );

                if (
                    $hasChild === null
                    ||
                    ! in_array(
                        $hasChild,
                        [0, 1],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Has child pada baris {$excelRow} harus dipilih dari dropdown: 0 - Tidak atau 1 - Iya.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Child Label
                |--------------------------------------------------------------------------
                */
                if (
                    (int) $hasChild === 1
                    &&
                    $answerText2 === ''
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Label child pada baris {$excelRow} wajib diisi ketika has_child bernilai 1.",
                    ]);
                }

                if (
                    mb_strlen($answerText2) > 255
                ) {
                    throw ValidationException::withMessages([
                        'file' =>
                            "Label child pada baris {$excelRow} maksimal 255 karakter.",
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Buffer Option
                |--------------------------------------------------------------------------
                */
                $optionsForImport[
                    $normalizedCode
                ][] = [
                    'no' =>
                        (int) $number,

                    'answer_text' =>
                        $answerText,

                    'answer_text2' =>
                        $answerText2 !== ''
                            ? $answerText2
                            : null,

                    'has_child' =>
                        (int) $hasChild,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Transaction Import
            |--------------------------------------------------------------------------
            */
            $result = DB::transaction(
                function () use (
                    $form,
                    $questionsForImport,
                    $optionsForImport
                ) {
                    $questionCount = 0;
                    $optionCount = 0;

                    foreach (
                        $questionsForImport
                        as $code => $questionData
                    ) {
                        /*
                        |--------------------------------------------------------------------------
                        | Create Question
                        |--------------------------------------------------------------------------
                        */
                        $question =
                            Question::create([
                                'group_id' =>
                                    $form->group_id,

                                'form_id' =>
                                    $form->id,

                                'no_header' =>
                                    $questionData[
                                        'no_header'
                                    ],

                                /*
                                | Tetap string.
                                */
                                'no' =>
                                    (string) $questionData[
                                        'no'
                                    ],

                                'name' =>
                                    $questionData[
                                        'name'
                                    ],

                                'questiontype_id' =>
                                    $questionData[
                                        'questiontype_id'
                                    ],
                            ]);

                        $questionCount++;

                        /*
                        |--------------------------------------------------------------------------
                        | Create Options
                        |--------------------------------------------------------------------------
                        */
                        foreach (
                            $optionsForImport[
                                $code
                            ] ?? []
                            as $optionData
                        ) {
                            $question
                                ->options()
                                ->create([
                                    'no' =>
                                        $optionData[
                                            'no'
                                        ],

                                    'answer_text' =>
                                        $optionData[
                                            'answer_text'
                                        ],

                                    'answer_text2' =>
                                        $optionData[
                                            'answer_text2'
                                        ],

                                    'has_child' =>
                                        $optionData[
                                            'has_child'
                                        ],
                                ]);

                            $optionCount++;
                        }
                    }

                    return [
                        'questions' =>
                            $questionCount,

                        'options' =>
                            $optionCount,
                    ];
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Bersihkan Spreadsheet
            |--------------------------------------------------------------------------
            */
            $spreadsheet
                ->disconnectWorksheets();

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */
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
        } catch (
            ValidationException $error
        ) {
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

    /**
     * Validasi header INPUT_PERTANYAAN.
     */
    private function validateQuestionHeaders(
        array $rows
    ): void {
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
                    trim(
                        (string) $value
                    )
                );
            },
            array_slice(
                $rows[0] ?? [],
                0,
                6
            )
        );

        if (
            $actualHeaders !==
            $expectedHeaders
        ) {
            throw ValidationException::withMessages([
                'file' =>
                    'Judul kolom pada sheet INPUT_PERTANYAAN tidak sesuai template.',
            ]);
        }
    }

    /**
     * Validasi header INPUT_OPTIONS.
     */
    private function validateOptionHeaders(
        array $rows
    ): void {
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
                    trim(
                        (string) $value
                    )
                );
            },
            array_slice(
                $rows[0] ?? [],
                0,
                5
            )
        );

        if (
            $actualHeaders !==
            $expectedHeaders
        ) {
            throw ValidationException::withMessages([
                'file' =>
                    'Judul kolom pada sheet INPUT_OPTIONS tidak sesuai template.',
            ]);
        }
    }

    /**
     * Ambil ID dari:
     *
     * 1 - Text
     * 2 - Textarea
     * 0 - Tidak
     * 1 - Iya
     */
    private function extractReferenceId(
        string $value
    ): ?int {
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
