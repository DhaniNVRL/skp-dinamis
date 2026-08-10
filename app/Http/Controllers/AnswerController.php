<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Form;
use App\Models\Question;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\SurveySession;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Form per Sub Unit
    |--------------------------------------------------------------------------
    */
    private const PER_SUBUNIT_TYPES = [
        2,
        3,
        8,
        9,
        10,
    ];

    private const FEEDBACK_TYPES = [
        8,
        9,
        10,
    ];

    private const COMPETITOR_TYPES = [
        11,
        13,
    ];

    private const CUSTOMER_TYPES = [
        2,
        3,
    ];

    public function store(
        Request $request,
        Form $form
    ): RedirectResponse {
        return $this->saveAnswers(
            $request,
            $form
        );
    }

    public function update(
        Request $request,
        Form $form
    ): RedirectResponse {
        return $this->saveAnswers(
            $request,
            $form
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE ANSWERS
    |--------------------------------------------------------------------------
    */
    private function saveAnswers(
        Request $request,
        Form $form
    ): RedirectResponse {
        $profile = UserProfile::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->firstOrFail();

        /*
         * Responden hanya boleh mengisi form
         * yang berasal dari group miliknya.
         */
        abort_unless(
            (int) $form->group_id ===
            (int) $profile->group_id,
            403,
            'Form tidak tersedia untuk responden ini.'
        );

        $session = SurveySession::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->first();

        abort_unless(
            $session,
            409,
            'Sesi survei belum dimulai.'
        );

        abort_unless(
            (int) $session->group_id === (int) $profile->group_id
                && (int) $session->unit_id === (int) $profile->unit_id,
            409,
            'Profil responden berubah. Mulai ulang sesi survei sebelum menyimpan jawaban.'
        );

        abort_if(
            $session?->status === 'completed',
            403,
            'Survei sudah selesai dan akun harus direset oleh Admin sebelum jawaban dapat diubah.'
        );

        /*
         * Form Description tidak mempunyai jawaban.
         */
        if (
            (int) $form->formtype_id === 12
        ) {
            return $this->goToNextForm(
                $form
            );
        }

        /*
         * Load questions dan options.
         *
         * Jangan gunakan:
         * where('questiontype_id', '!=', 1)
         *
         * Karena pada Kuesioner Umum,
         * questiontype_id 1 adalah jawaban singkat.
         */
        $form->load([
            'questions',

            'questions.options' => function (
                $query
            ) {
                $query
                    ->orderByRaw(
                        'CAST(no AS UNSIGNED) ASC'
                    )
                    ->orderBy('id');
            },

            'questions.questiontype',
        ]);

        $subunitIds = SubUnit::query()
            ->where(
                'unit_id',
                $profile->unit_id
            )
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        /*
         * Ambil konfigurasi Hide and Show.
         */
        $activeRows = SubUnitQuestion::query()
            ->where(
                'form_id',
                $form->id
            )
            ->whereIn(
                'subunit_id',
                $subunitIds
            )
            ->get();

        $activeQuestionIds = $activeRows
            ->pluck('question_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
         * Hanya pertanyaan aktif yang diproses.
         */
        $questions = $form->questions
            ->whereIn(
                'id',
                $activeQuestionIds
            )
            ->values();

        $competitorIds = collect();

        if (
            in_array(
                (int) $form->formtype_id,
                self::COMPETITOR_TYPES,
                true
            )
        ) {
            $competitorIds = Competitor::query()
                ->where(
                    'group_id',
                    $profile->group_id
                )
                ->pluck('id')
                ->map(fn ($id) => (int) $id);
        }

        $answers = (array) $request->input(
            'answers',
            []
        );

        /*
         * Server-side validation.
         */
        $errors = $this->validateAnswers(
            $form,
            $questions,
            $activeRows,
            $competitorIds,
            $answers
        );

        if (!empty($errors)) {
            throw ValidationException::withMessages(
                $errors
            );
        }

        /*
         * Simpan jawaban hanya berdasarkan
         * question dan target yang valid.
         */
        DB::transaction(
            function () use (
                $form,
                $questions,
                $activeRows,
                $competitorIds,
                $answers
            ): void {
                foreach (
                    $questions as $question
                ) {
                    /*
                     * Question type 1 merupakan
                     * judul pada semua form selain
                     * Kuesioner Umum.
                     */
                    if (
                        $this->isTitleQuestion(
                            $form,
                            $question
                        )
                    ) {
                        continue;
                    }

                    $questionPayload = Arr::get(
                        $answers,
                        (string) $question->id,
                        []
                    );

                    /*
                     * Per Sub Unit.
                     */
                    if (
                        in_array(
                            (int) $form->formtype_id,
                            self::PER_SUBUNIT_TYPES,
                            true
                        )
                    ) {
                        $targetIds = $activeRows
                            ->where(
                                'question_id',
                                $question->id
                            )
                            ->pluck('subunit_id')
                            ->map(
                                fn ($id) => (int) $id
                            )
                            ->unique();

                        foreach (
                            $targetIds as $subunitId
                        ) {
                            $value = Arr::get(
                                $questionPayload,
                                (string) $subunitId,
                                []
                            );

                            $this->saveAnswer(
                                $form,
                                $question->id,
                                (array) $value,
                                $subunitId,
                                null
                            );
                        }

                        continue;
                    }

                    /*
                     * Per Competitor.
                     */
                    if (
                        in_array(
                            (int) $form->formtype_id,
                            self::COMPETITOR_TYPES,
                            true
                        )
                    ) {
                        foreach (
                            $competitorIds as $competitorId
                        ) {
                            $value = Arr::get(
                                $questionPayload,
                                (string) $competitorId,
                                []
                            );

                            $this->saveAnswer(
                                $form,
                                $question->id,
                                (array) $value,
                                null,
                                $competitorId
                            );
                        }

                        continue;
                    }

                    /*
                     * Global.
                     */
                    $this->saveAnswer(
                        $form,
                        $question->id,
                        (array) $questionPayload,
                        null,
                        null
                    );
                }
            }
        );

        return $this->goToNextForm(
            $form
        );
    }

    private function validateFeedbackAnswer(
        Form $form,
        Question $question,
        int $subunitId,
        array $payload,
        array &$errors
    ): void {
        $requiredFields = match (
            (int) $form->formtype_id
        ) {
            /*
            * Keunggulan, Keluhan, dan Saran.
            */
            8 => [
                'strength' => 'Keunggulan',
                'complaint' => 'Keluhan',
                'suggestion' => 'Saran',
            ],

            /*
            * Keluhan dan Saran.
            */
            9 => [
                'complaint' => 'Keluhan',
                'suggestion' => 'Saran',
            ],

            /*
            * Saran.
            */
            10 => [
                'suggestion' => 'Saran',
            ],

            default => [],
        };

        foreach (
            $requiredFields as $field => $label
        ) {
            $value = Arr::get(
                $payload,
                $field
            );

            if (!filled($value)) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.{$field}"
                ] = "{$label} untuk pertanyaan {$question->name} wajib diisi.";
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    private function validateAnswers(
            Form $form,
            Collection $questions,
            Collection $activeRows,
            Collection $competitorIds,
            array $answers
        ): array {
            $errors = [];

            foreach ($questions as $question) {
                /*
                * Lewati questiontype_id 1
                * karena merupakan judul untuk
                * semua form selain Kuesioner Umum.
                */
                if (
                    $this->isTitleQuestion(
                        $form,
                        $question
                    )
                ) {
                    continue;
                }

                $questionPayload = Arr::get(
                    $answers,
                    (string) $question->id,
                    []
                );

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER ASSESSMENT
                |--------------------------------------------------------------------------
                | Form Type 2 dan 3.
                */
                if (
                    in_array(
                        (int) $form->formtype_id,
                        self::CUSTOMER_TYPES,
                        true
                    )
                ) {
                    $targetIds = $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck('subunit_id')
                        ->map(
                            fn ($id) => (int) $id
                        )
                        ->unique();

                    foreach (
                        $targetIds as $subunitId
                    ) {
                        $payload = Arr::get(
                            $questionPayload,
                            (string) $subunitId,
                            []
                        );

                        $this->validateCustomerAnswer(
                            $form,
                            $question,
                            $subunitId,
                            (array) $payload,
                            $errors
                        );
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | FEEDBACK FORM
                |--------------------------------------------------------------------------
                | Form Type:
                | 8  = Keunggulan, Keluhan, Saran
                | 9  = Keluhan, Saran
                | 10 = Saran
                |
                | WAJIB berada sebelum validasi PER_SUBUNIT_TYPES.
                */
                if (
                    in_array(
                        (int) $form->formtype_id,
                        self::FEEDBACK_TYPES,
                        true
                    )
                ) {
                    $targetIds = $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck('subunit_id')
                        ->map(
                            fn ($id) => (int) $id
                        )
                        ->unique();

                    foreach (
                        $targetIds as $subunitId
                    ) {
                        $payload = Arr::get(
                            $questionPayload,
                            (string) $subunitId,
                            []
                        );

                        $this->validateFeedbackAnswer(
                            $form,
                            $question,
                            $subunitId,
                            (array) $payload,
                            $errors
                        );
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | RANKING
                |--------------------------------------------------------------------------
                */
                if (
                    in_array(
                        (int) $form->formtype_id,
                        [6, 7],
                        true
                    )
                ) {
                    $this->validateRankingAnswer(
                        $form,
                        $question,
                        (array) $questionPayload,
                        $errors
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | PER SUB UNIT GENERIC
                |--------------------------------------------------------------------------
                | Digunakan jika nanti terdapat form
                | per Sub Unit lain dengan struktur [value].
                */
                if (
                    in_array(
                        (int) $form->formtype_id,
                        self::PER_SUBUNIT_TYPES,
                        true
                    )
                ) {
                    $targetIds = $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck('subunit_id')
                        ->map(
                            fn ($id) => (int) $id
                        )
                        ->unique();

                    foreach (
                        $targetIds as $subunitId
                    ) {
                        $payload = Arr::get(
                            $questionPayload,
                            (string) $subunitId,
                            []
                        );

                        if (
                            !filled(
                                Arr::get(
                                    $payload,
                                    'value'
                                )
                            )
                        ) {
                            $errors[
                                "answers.{$question->id}.{$subunitId}.value"
                            ] = "Pertanyaan {$question->name} wajib diisi untuk setiap Sub Unit.";
                        }
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | COMPETITOR
                |--------------------------------------------------------------------------
                */
                if (
                    in_array(
                        (int) $form->formtype_id,
                        self::COMPETITOR_TYPES,
                        true
                    )
                ) {
                    foreach (
                        $competitorIds as $competitorId
                    ) {
                        $payload = Arr::get(
                            $questionPayload,
                            (string) $competitorId,
                            []
                        );

                        if (
                            !filled(
                                Arr::get(
                                    $payload,
                                    'value'
                                )
                            )
                        ) {
                            $errors[
                                "answers.{$question->id}.{$competitorId}.value"
                            ] = "Pertanyaan {$question->name} wajib diisi untuk setiap kompetitor.";
                        }
                    }

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | GLOBAL FORM
                |--------------------------------------------------------------------------
                */
                $this->validateGlobalAnswer(
                    $question,
                    (array) $questionPayload,
                    $errors
                );
            }

            return $errors;
        }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE RANKING
    |--------------------------------------------------------------------------
    */
    private function validateRankingAnswer(
        Form $form,
        Question $question,
        array $payload,
        array &$errors
    ): void {
        $maximumRank = (int) $form->formtype_id === 6
            ? 3
            : 5;

        $rankings = Arr::get($payload, 'value', []);
        $rankings = is_array($rankings)
            ? $rankings
            : [];

        $selectedOptionIds = [];

        for ($rank = 1; $rank <= $maximumRank; $rank++) {
            $ranking = (array) Arr::get(
                $rankings,
                (string) $rank,
                []
            );

            $optionId = Arr::get($ranking, 'option_id');
            $errorKey = "answers.{$question->id}.value.{$rank}.option_id";

            if (!filled($optionId)) {
                $errors[$errorKey] = "Ranking {$rank} untuk pertanyaan {$question->name} wajib dipilih.";
                continue;
            }

            $option = $question->options->firstWhere(
                'id',
                (int) $optionId
            );

            if (!$option) {
                $errors[$errorKey] = "Pilihan Ranking {$rank} untuk pertanyaan {$question->name} tidak valid.";
                continue;
            }

            if (in_array((int) $option->id, $selectedOptionIds, true)) {
                $errors[$errorKey] = "Pilihan pada setiap urutan Ranking untuk pertanyaan {$question->name} tidak boleh sama.";
                continue;
            }

            $selectedOptionIds[] = (int) $option->id;

            if (
                (int) $option->has_child === 1
                && !filled(Arr::get($ranking, 'child'))
            ) {
                $errors[
                    "answers.{$question->id}.value.{$rank}.child"
                ] = "Jawaban tambahan untuk {$option->answer_text} wajib diisi.";
            }
        }
    }
    /*
    |--------------------------------------------------------------------------
    | VALIDATE CUSTOMER ASSESSMENT
    |--------------------------------------------------------------------------
    */
    private function validateCustomerAnswer(
        Form $form,
        Question $question,
        int $subunitId,
        array $payload,
        array &$errors
    ): void {
        $questionTypeId =
            (int) $question->questiontype_id;

        /*
         * Type 2, 3, dan 4:
         * Kepentingan dan Kinerja.
         */
        if (
            in_array(
                $questionTypeId,
                [2, 3, 4],
                true
            )
        ) {
            $importance = Arr::get(
                $payload,
                'importance'
            );

            $performance = Arr::get(
                $payload,
                'performance'
            );

            $maximumScale =
                (int) $form->formtype_id === 2
                    ? 5
                    : 7;

            $allowedValues = array_merge(
                range(1, $maximumScale),
                [0]
            );

            if (
                !filled($importance) ||
                !in_array(
                    (int) $importance,
                    $allowedValues,
                    true
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.importance"
                ] = "Nilai Kepentingan {$question->name} wajib dipilih.";
            }

            if (
                !filled($performance) ||
                !in_array(
                    (int) $performance,
                    $allowedValues,
                    true
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.performance"
                ] = "Nilai Kinerja {$question->name} wajib dipilih.";
            }

            $reasonMaximum =
                (int) $form->formtype_id === 2
                    ? 3
                    : 4;

            $needsReason =
                filled($performance) &&
                (int) $performance !== 0 &&
                (int) $performance <=
                    $reasonMaximum;

            /*
             * Type 3:
             * alasan berupa textarea.
             */
            if (
                $questionTypeId === 3 &&
                $needsReason &&
                !filled(
                    Arr::get(
                        $payload,
                        'reason'
                    )
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.reason"
                ] = "Alasan penilaian Kinerja {$question->name} wajib diisi.";
            }

            /*
             * Type 4:
             * alasan berupa checkbox options.
             */
            if (
                $questionTypeId === 4 &&
                $needsReason
            ) {
                $selectedReasonIds = collect(
                    Arr::get(
                        $payload,
                        'reasons',
                        []
                    )
                )
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                if (
                    $selectedReasonIds->isEmpty()
                ) {
                    $errors[
                        "answers.{$question->id}.{$subunitId}.reasons"
                    ] = "Pilih minimal satu alasan penilaian Kinerja {$question->name}.";
                } else {
                    $validOptions = $question
                        ->options
                        ->whereIn(
                            'id',
                            $selectedReasonIds
                        );

                    /*
                     * Mencegah option dari pertanyaan
                     * lain dikirimkan.
                     */
                    if (
                        $validOptions->count() !==
                        $selectedReasonIds->count()
                    ) {
                        $errors[
                            "answers.{$question->id}.{$subunitId}.reasons"
                        ] = "Pilihan alasan tidak valid.";
                    }

                    foreach (
                        $validOptions as $option
                    ) {
                        if (
                            (int) $option->has_child !== 1
                        ) {
                            continue;
                        }

                        $childValue = Arr::get(
                            $payload,
                            "children.{$option->id}"
                        );

                        if (!filled($childValue)) {
                            $errors[
                                "answers.{$question->id}.{$subunitId}.children.{$option->id}"
                            ] = "Jawaban tambahan untuk alasan {$option->answer_text} wajib diisi.";
                        }
                    }
                }
            }

            return;
        }

        /*
         * Type 5: satu indikator.
         * Type 6: textarea.
         */
        if (
            in_array(
                $questionTypeId,
                [5, 6],
                true
            ) &&
            !filled(
                Arr::get(
                    $payload,
                    'value'
                )
            )
        ) {
            $errors[
                "answers.{$question->id}.{$subunitId}.value"
            ] = "Pertanyaan {$question->name} wajib diisi.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE GLOBAL FORM
    |--------------------------------------------------------------------------
    */
    private function validateGlobalAnswer(
        Question $question,
        array $payload,
        array &$errors
    ): void {
        $questionTypeId =
            (int) $question->questiontype_id;

        $value = Arr::get(
            $payload,
            'value'
        );

        if (!filled($value)) {
            $errors[
                "answers.{$question->id}.value"
            ] = "Pertanyaan {$question->name} wajib diisi.";

            return;
        }

        /*
         * Kuesioner Umum:
         * Type 3 radio dan Type 4 checkbox.
         */
        if (
            in_array(
                $questionTypeId,
                [3, 4],
                true
            )
        ) {
            $selectedOptionIds = is_array(
                $value
            )
                ? $value
                : [$value];

            $selectedOptionIds = collect(
                $selectedOptionIds
            )
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $validOptions = $question
                ->options
                ->whereIn(
                    'id',
                    $selectedOptionIds
                );

            if (
                $validOptions->count() !==
                $selectedOptionIds->count()
            ) {
                $errors[
                    "answers.{$question->id}.value"
                ] = "Pilihan jawaban {$question->name} tidak valid.";

                return;
            }

            foreach (
                $validOptions as $option
            ) {
                if (
                    (int) $option->has_child !== 1
                ) {
                    continue;
                }

                $childValue = Arr::get(
                    $payload,
                    "child.{$option->id}"
                );

                if (!filled($childValue)) {
                    $errors[
                        "answers.{$question->id}.child.{$option->id}"
                    ] = "Jawaban tambahan untuk {$option->answer_text} wajib diisi.";
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK TITLE
    |--------------------------------------------------------------------------
    */
    private function isTitleQuestion(
        Form $form,
        Question $question
    ): bool {
        /*
         * Form Type 1:
         * questiontype_id 1 adalah jawaban singkat.
         *
         * Form lainnya:
         * questiontype_id 1 adalah judul.
         */
        return $question->questiontype?->isTitleOnly()
            || (
                (int) $form->formtype_id !== 1
                && (int) $question->questiontype_id === 1
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE ONE ANSWER
    |--------------------------------------------------------------------------
    */
    private function saveAnswer(
        Form $form,
        int $questionId,
        array $value,
        ?int $subunitId,
        ?int $competitorId
    ): void {
        $attributes = [
                'user_id' =>
                    Auth::id(),

                'form_id' =>
                    $form->id,

                'question_id' =>
                    $questionId,

                'subunit_id' =>
                    $subunitId,

                'competitor_id' =>
                    $competitorId,
            ];

        $answer = Answer::query()->updateOrCreate(
            $attributes,
            [
                'answer' => $value,
            ]
        );

        // Bersihkan duplikasi legacy untuk konteks logis yang sama tanpa
        // menyentuh jawaban lain milik responden.
        Answer::query()
            ->where($attributes)
            ->whereKeyNot($answer->getKey())
            ->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT FORM
    |--------------------------------------------------------------------------
    */
    private function goToNextForm(
        Form $form
    ): RedirectResponse {
        $nextForm = Form::query()
            ->where(
                'group_id',
                $form->group_id
            )
            ->where(
                function ($query) use (
                    $form
                ): void {
                    $query
                        ->where(
                            'no_urut',
                            '>',
                            $form->no_urut
                        )
                        ->orWhere(
                            function (
                                $query
                            ) use (
                                $form
                            ): void {
                                $query
                                    ->where(
                                        'no_urut',
                                        $form->no_urut
                                    )
                                    ->where(
                                        'id',
                                        '>',
                                        $form->id
                                    );
                            }
                        );
                }
            )
            ->orderBy('no_urut')
            ->orderBy('id')
            ->first();

        SurveySession::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->update([
                'current_form_id' =>
                    $nextForm?->id,
            ]);

        if ($nextForm) {
            return redirect()->route(
                'survey.show',
                [
                    'form' =>
                        $nextForm->id,
                ]
            );
        }

        return redirect()->route(
            'survey.finish.page'
        );
    }

}
