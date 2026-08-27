<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Form;
use App\Models\Question;
use App\Models\RespondentCompetitor;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\SurveySession;
use App\Models\UserProfile;
use App\Services\UnitCompetitorVisibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{
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

    private const MEANINGFUL_ANSWER_ROLE_IDS = [
        2,
        4,
    ];

    private const MEANINGLESS_ANSWERS = [
        'tidak ada',
        'tidak ada saran',
        'tidak ada keluhan',
        'tidak ada keunggulan',
        'tidak ada masukan',
        'tidak ada komentar',
        'tidak ada jawaban',

        'ga ada',
        'gak ada',
        'nggak ada',
        'ngga ada',
        'enggak ada',
        'g ada',
        'gada',

        'belum ada',
        'nihil',
        'kosong',

        'tidak tahu',
        'ga tahu',
        'gak tahu',
        'nggak tahu',
        'ngga tahu',
        'enggak tahu',
        'g tahu',
        'kurang tahu',
        'entah',

        'terserah',
        'skip',
        'lewati',
        'pass',

        'none',
        'nothing',
        'nil',

        'n a',
        'na',

        'no comment',
        'no comments',
        'no suggestion',
        'no suggestions',
        'no complaint',
        'no complaints',
        'no idea',
        'nothing to say',
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

        abort_unless(
            (int) $form->group_id
                ===
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
            (int) $session->group_id
                ===
            (int) $profile->group_id
            &&
            (int) $session->unit_id
                ===
            (int) $profile->unit_id,
            409,
            'Profil responden berubah. Mulai ulang sesi survei sebelum menyimpan jawaban.'
        );

        abort_if(
            $session?->status === 'completed',
            403,
            'Survei sudah selesai dan akun harus direset oleh Admin sebelum jawaban dapat diubah.'
        );

        if (
            (int) $form->formtype_id
            === 12
        ) {
            return $this->goToNextForm(
                $form
            );
        }

        $form->load([
            'questions',

            'questions.options' =>
                function ($query) {
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
            ->map(
                fn ($id) =>
                    (int) $id
            );

        $activeRows =
            SubUnitQuestion::query()
                ->where(
                    'form_id',
                    $form->id
                )
                ->whereIn(
                    'subunit_id',
                    $subunitIds
                )
                ->get();

        $activeQuestionIds =
            $activeRows
                ->pluck('question_id')
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();

        $questions =
            $form->questions
                ->whereIn(
                    'id',
                    $activeQuestionIds
                )
                ->values();

        $competitorIds =
            collect();

        if (
            in_array(
                (int) $form->formtype_id,
                self::COMPETITOR_TYPES,
                true
            )
        ) {
            $competitorIds =
                app(
                    UnitCompetitorVisibilityService::class
                )
                    ->filterForUnit(
                        Competitor::query()
                            ->where(
                                'group_id',
                                $profile->group_id
                            )
                            ->get(),

                        $profile->unit_id
                            ? (int) $profile->unit_id
                            : null
                    )
                    ->pluck('id')
                    ->map(
                        fn ($id) =>
                            (int) $id
                    );
        }

        $answers =
            (array) $request->input(
                'answers',
                []
            );

        $branching =
            app(
                \App\Services\SurveyBranchingService::class
            );

        $hiddenConditionalQuestionIds =
            $branching->hiddenQuestionIds(
                $form,
                $answers
            );

        $questions =
            $questions
                ->reject(
                    fn ($question) =>
                        $hiddenConditionalQuestionIds
                            ->contains(
                                (int) $question->id
                            )
                )
                ->values();

        if (
            (int) $form->formtype_id
            === 14
        ) {
            return $this
                ->saveRespondentCompetitorAnswers(
                    $request,
                    $form,
                    $profile,
                    $questions,
                    $answers
                );
        }

        $errors =
            $this->validateAnswers(
                $form,
                $questions,
                $activeRows,
                $competitorIds,
                $answers
            );

        if (
            !empty($errors)
        ) {
            throw ValidationException
                ::withMessages(
                    $errors
                );
        }

        DB::transaction(
            function () use (
                $form,
                $questions,
                $activeRows,
                $competitorIds,
                $answers,
                $hiddenConditionalQuestionIds
            ): void {
                if (
                    $hiddenConditionalQuestionIds
                        ->isNotEmpty()
                ) {
                    Answer::query()
                        ->where(
                            'user_id',
                            Auth::id()
                        )
                        ->where(
                            'form_id',
                            $form->id
                        )
                        ->whereIn(
                            'question_id',
                            $hiddenConditionalQuestionIds
                        )
                        ->delete();
                }

                foreach (
                    $questions as $question
                ) {
                    if (
                        $this->isTitleQuestion(
                            $form,
                            $question
                        )
                    ) {
                        continue;
                    }

                    $questionPayload =
                        Arr::get(
                            $answers,
                            (string) $question->id,
                            []
                        );

                    if (
                        in_array(
                            (int) $form->formtype_id,
                            self::PER_SUBUNIT_TYPES,
                            true
                        )
                    ) {
                        $targetIds =
                            $activeRows
                                ->where(
                                    'question_id',
                                    $question->id
                                )
                                ->pluck(
                                    'subunit_id'
                                )
                                ->map(
                                    fn ($id) =>
                                        (int) $id
                                )
                                ->unique();

                        foreach (
                            $targetIds
                            as $subunitId
                        ) {
                            $value =
                                Arr::get(
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

                    if (
                        in_array(
                            (int) $form->formtype_id,
                            self::COMPETITOR_TYPES,
                            true
                        )
                    ) {
                        foreach (
                            $competitorIds
                            as $competitorId
                        ) {
                            $value =
                                Arr::get(
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


    /*
    |--------------------------------------------------------------------------
    | FEEDBACK FIELDS
    |--------------------------------------------------------------------------
    */
    private function feedbackFieldsForForm(
        Form $form
    ): array {
        return match (
            (int) $form->formtype_id
        ) {
            8 => [
                'strength' =>
                    'Keunggulan',

                'complaint' =>
                    'Keluhan',

                'suggestion' =>
                    'Saran',
            ],

            9 => [
                'complaint' =>
                    'Keluhan',

                'suggestion' =>
                    'Saran',
            ],

            10 => [
                'suggestion' =>
                    'Saran',
            ],

            default => [],
        };
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE FEEDBACK
    |--------------------------------------------------------------------------
    */
    private function validateFeedbackAnswer(
        Form $form,
        Question $question,
        int $subunitId,
        array $payload,
        array &$errors
    ): void {
        $requiredFields =
            $this->feedbackFieldsForForm(
                $form
            );

        foreach (
            $requiredFields
            as $field => $label
        ) {
            $value =
                Arr::get(
                    $payload,
                    $field
                );

            $errorKey =
                "answers.{$question->id}.{$subunitId}.{$field}";

            if (!filled($value)) {
                $errors[
                    $errorKey
                ] =
                    "{$label} untuk pertanyaan {$question->name} wajib diisi.";

                continue;
            }

            if (
                $this->shouldValidateMeaningfulAnswers()
                &&
                $this->isMeaninglessAnswer(
                    $value
                )
            ) {
                $errors[
                    $errorKey
                ] =
                    'Berikan pendapat agar masukan Anda dapat dianalisa.';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DUPLICATE FEEDBACK
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Question D1
    |
    | Sub Unit A:
    | Keluhan = "Sudah baik"
    |
    | Sub Unit B:
    | Keluhan = "Sudah baik"
    |
    | Maka keduanya dianggap duplicate.
    |
    */
    private function validateDuplicateFeedbackAnswers(
        Form $form,
        Question $question,
        Collection $targetIds,
        array $questionPayload,
        array &$errors
    ): void {
        if (
            !$this
                ->shouldValidateMeaningfulAnswers()
        ) {
            return;
        }

        $feedbackFields =
            $this->feedbackFieldsForForm(
                $form
            );

        foreach (
            $feedbackFields
            as $field => $label
        ) {
            $groupedValues = [];

            foreach (
                $targetIds
                as $subunitId
            ) {
                $value =
                    Arr::get(
                        $questionPayload,
                        "{$subunitId}.{$field}"
                    );

                if (!filled($value)) {
                    continue;
                }

                if (
                    $this->isMeaninglessAnswer(
                        $value
                    )
                ) {
                    continue;
                }

                $normalized =
                    $this->normalizeAnswer(
                        $value
                    );

                if ($normalized === '') {
                    continue;
                }

                if (
                    !array_key_exists(
                        $normalized,
                        $groupedValues
                    )
                ) {
                    $groupedValues[
                        $normalized
                    ] = [];
                }

                $groupedValues[
                    $normalized
                ][] =
                    (int) $subunitId;
            }

            foreach (
                $groupedValues
                as $subunitIds
            ) {
                if (
                    count($subunitIds)
                    <= 1
                ) {
                    continue;
                }

                foreach (
                    $subunitIds
                    as $subunitId
                ) {
                    $errorKey =
                        "answers.{$question->id}.{$subunitId}.{$field}";

                    $errors[
                        $errorKey
                    ] =
                        'Berikan pendapat yang berbeda untuk setiap Sub Unit agar masukan Anda dapat dianalisa.';
                }
            }
        }
    }


    private function validateAnswers(
        Form $form,
        Collection $questions,
        Collection $activeRows,
        Collection $competitorIds,
        array $answers
    ): array {
        $errors = [];

        foreach (
            $questions
            as $question
        ) {
            if (
                $this->isTitleQuestion(
                    $form,
                    $question
                )
            ) {
                continue;
            }

            $questionPayload =
                Arr::get(
                    $answers,
                    (string) $question->id,
                    []
                );

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    (int) $form->formtype_id,
                    self::CUSTOMER_TYPES,
                    true
                )
            ) {
                $targetIds =
                    $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck(
                            'subunit_id'
                        )
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->unique();

                foreach (
                    $targetIds
                    as $subunitId
                ) {
                    $payload =
                        Arr::get(
                            $questionPayload,
                            (string) $subunitId,
                            []
                        );

                    $this
                        ->validateCustomerAnswer(
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
            | FEEDBACK
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    (int) $form->formtype_id,
                    self::FEEDBACK_TYPES,
                    true
                )
            ) {
                $targetIds =
                    $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck(
                            'subunit_id'
                        )
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->unique();

                foreach (
                    $targetIds
                    as $subunitId
                ) {
                    $payload =
                        Arr::get(
                            $questionPayload,
                            (string) $subunitId,
                            []
                        );

                    $this
                        ->validateFeedbackAnswer(
                            $form,
                            $question,
                            $subunitId,
                            (array) $payload,
                            $errors
                        );
                }

                /*
                 * Cek duplicate setelah seluruh
                 * Sub Unit divalidasi.
                 */
                $this
                    ->validateDuplicateFeedbackAnswers(
                        $form,
                        $question,
                        $targetIds,
                        (array) $questionPayload,
                        $errors
                    );

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
                $this
                    ->validateRankingAnswer(
                        $form,
                        $question,
                        (array) $questionPayload,
                        $errors
                    );

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PER SUB UNIT
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    (int) $form->formtype_id,
                    self::PER_SUBUNIT_TYPES,
                    true
                )
            ) {
                $targetIds =
                    $activeRows
                        ->where(
                            'question_id',
                            $question->id
                        )
                        ->pluck(
                            'subunit_id'
                        )
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->unique();

                foreach (
                    $targetIds
                    as $subunitId
                ) {
                    $payload =
                        Arr::get(
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
                        ] =
                            "Pertanyaan {$question->name} wajib diisi untuk setiap Sub Unit.";
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
                    $competitorIds
                    as $competitorId
                ) {
                    $payload =
                        Arr::get(
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
                        ] =
                            "Pertanyaan {$question->name} wajib diisi untuk setiap kompetitor.";
                    }
                }

                continue;
            }

            $this
                ->validateGlobalAnswer(
                    $question,
                    (array) $questionPayload,
                    $errors
                );
        }

        return $errors;
    }


    private function validateRankingAnswer(
        Form $form,
        Question $question,
        array $payload,
        array &$errors
    ): void {
        $maximumRank =
            (int) $form->formtype_id
                === 6
                ? 3
                : 5;

        $rankings =
            Arr::get(
                $payload,
                'value',
                []
            );

        $rankings =
            is_array(
                $rankings
            )
                ? $rankings
                : [];

        $selectedOptionIds = [];

        for (
            $rank = 1;
            $rank <= $maximumRank;
            $rank++
        ) {
            $ranking =
                (array) Arr::get(
                    $rankings,
                    (string) $rank,
                    []
                );

            $optionId =
                Arr::get(
                    $ranking,
                    'option_id'
                );

            $errorKey =
                "answers.{$question->id}.value.{$rank}.option_id";

            if (!filled($optionId)) {
                $errors[
                    $errorKey
                ] =
                    "Ranking {$rank} untuk pertanyaan {$question->name} wajib dipilih.";

                continue;
            }

            $option =
                $question
                    ->options
                    ->firstWhere(
                        'id',
                        (int) $optionId
                    );

            if (!$option) {
                $errors[
                    $errorKey
                ] =
                    "Pilihan Ranking {$rank} untuk pertanyaan {$question->name} tidak valid.";

                continue;
            }

            if (
                in_array(
                    (int) $option->id,
                    $selectedOptionIds,
                    true
                )
            ) {
                $errors[
                    $errorKey
                ] =
                    "Pilihan pada setiap urutan Ranking untuk pertanyaan {$question->name} tidak boleh sama.";

                continue;
            }

            $selectedOptionIds[] =
                (int) $option->id;

            if (
                (int) $option->has_child
                === 1
            ) {
                $childValue =
                    Arr::get(
                        $ranking,
                        'child'
                    );

                $childErrorKey =
                    "answers.{$question->id}.value.{$rank}.child";

                if (!filled($childValue)) {
                    $errors[
                        $childErrorKey
                    ] =
                        "Jawaban tambahan untuk {$option->answer_text} wajib diisi.";

                    continue;
                }

                if (
                    $this->shouldValidateMeaningfulAnswers()
                    &&
                    $this->isMeaninglessAnswer(
                        $childValue
                    )
                ) {
                    $errors[
                        $childErrorKey
                    ] =
                        'Berikan pendapat agar masukan Anda dapat dianalisa.';
                }
            }
        }
    }


    private function validateCustomerAnswer(
        Form $form,
        Question $question,
        int $subunitId,
        array $payload,
        array &$errors
    ): void {
        $questionTypeId =
            (int) $question
                ->questiontype_id;

        if (
            in_array(
                $questionTypeId,
                [2, 3, 4],
                true
            )
        ) {
            $importance =
                Arr::get(
                    $payload,
                    'importance'
                );

            $performance =
                Arr::get(
                    $payload,
                    'performance'
                );

            $maximumScale =
                (int) $form->formtype_id
                    === 2
                    ? 5
                    : 7;

            $allowedValues =
                array_merge(
                    range(
                        1,
                        $maximumScale
                    ),
                    [0]
                );

            if (
                !filled($importance)
                ||
                !in_array(
                    (int) $importance,
                    $allowedValues,
                    true
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.importance"
                ] =
                    "Nilai Kepentingan {$question->name} wajib dipilih.";
            }

            if (
                !filled($performance)
                ||
                !in_array(
                    (int) $performance,
                    $allowedValues,
                    true
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.performance"
                ] =
                    "Nilai Kinerja {$question->name} wajib dipilih.";
            }

            $reasonMaximum =
                (int) $form->formtype_id
                    === 2
                    ? 3
                    : 4;

            $needsReason =
                filled($performance)
                &&
                (int) $performance !== 0
                &&
                (int) $performance
                    <=
                    $reasonMaximum;

            if (
                $questionTypeId === 3
                &&
                $needsReason
                &&
                !filled(
                    Arr::get(
                        $payload,
                        'reason'
                    )
                )
            ) {
                $errors[
                    "answers.{$question->id}.{$subunitId}.reason"
                ] =
                    "Alasan penilaian Kinerja {$question->name} wajib diisi.";
            }

            if (
                $questionTypeId === 4
                &&
                $needsReason
            ) {
                $selectedReasonIds =
                    collect(
                        Arr::get(
                            $payload,
                            'reasons',
                            []
                        )
                    )
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->unique()
                        ->values();

                if (
                    $selectedReasonIds
                        ->isEmpty()
                ) {
                    $errors[
                        "answers.{$question->id}.{$subunitId}.reasons"
                    ] =
                        "Pilih minimal satu alasan penilaian Kinerja {$question->name}.";
                } else {
                    $validOptions =
                        $question
                            ->options
                            ->whereIn(
                                'id',
                                $selectedReasonIds
                            );

                    if (
                        $validOptions
                            ->count()
                        !==
                        $selectedReasonIds
                            ->count()
                    ) {
                        $errors[
                            "answers.{$question->id}.{$subunitId}.reasons"
                        ] =
                            'Pilihan alasan tidak valid.';
                    }

                    foreach (
                        $validOptions
                        as $option
                    ) {
                        if (
                            (int) $option
                                ->has_child
                            !== 1
                        ) {
                            continue;
                        }

                        $childValue =
                            Arr::get(
                                $payload,
                                "children.{$option->id}"
                            );

                        $childErrorKey =
                            "answers.{$question->id}.{$subunitId}.children.{$option->id}";

                        if (
                            !filled(
                                $childValue
                            )
                        ) {
                            $errors[
                                $childErrorKey
                            ] =
                                "Jawaban tambahan untuk alasan {$option->answer_text} wajib diisi.";

                            continue;
                        }

                        if (
                            $this
                                ->shouldValidateMeaningfulAnswers()
                            &&
                            $this
                                ->isMeaninglessAnswer(
                                    $childValue
                                )
                        ) {
                            $errors[
                                $childErrorKey
                            ] =
                                'Berikan pendapat agar masukan Anda dapat dianalisa.';
                        }
                    }
                }
            }

            return;
        }

        if (
            in_array(
                $questionTypeId,
                [5, 6],
                true
            )
            &&
            !filled(
                Arr::get(
                    $payload,
                    'value'
                )
            )
        ) {
            $errors[
                "answers.{$question->id}.{$subunitId}.value"
            ] =
                "Pertanyaan {$question->name} wajib diisi.";
        }
    }


    private function validateGlobalAnswer(
        Question $question,
        array $payload,
        array &$errors
    ): void {
        $questionTypeId =
            (int) $question
                ->questiontype_id;

        $value =
            Arr::get(
                $payload,
                'value'
            );

        if (!filled($value)) {
            $errors[
                "answers.{$question->id}.value"
            ] =
                "Pertanyaan {$question->name} wajib diisi.";

            return;
        }

        if (
            in_array(
                $questionTypeId,
                [3, 4],
                true
            )
        ) {
            $selectedOptionIds =
                is_array(
                    $value
                )
                    ? $value
                    : [$value];

            $selectedOptionIds =
                collect(
                    $selectedOptionIds
                )
                    ->map(
                        fn ($id) =>
                            (int) $id
                    )
                    ->unique()
                    ->values();

            $validOptions =
                $question
                    ->options
                    ->whereIn(
                        'id',
                        $selectedOptionIds
                    );

            if (
                $validOptions
                    ->count()
                !==
                $selectedOptionIds
                    ->count()
            ) {
                $errors[
                    "answers.{$question->id}.value"
                ] =
                    "Pilihan jawaban {$question->name} tidak valid.";

                return;
            }

            foreach (
                $validOptions
                as $option
            ) {
                if (
                    (int) $option
                        ->has_child
                    !== 1
                ) {
                    continue;
                }

                $childValue =
                    Arr::get(
                        $payload,
                        "child.{$option->id}"
                    );

                $childErrorKey =
                    "answers.{$question->id}.child.{$option->id}";

                if (
                    !filled(
                        $childValue
                    )
                ) {
                    $errors[
                        $childErrorKey
                    ] =
                        "Jawaban tambahan untuk {$option->answer_text} wajib diisi.";

                    continue;
                }

                if (
                    $this
                        ->shouldValidateMeaningfulAnswers()
                    &&
                    $this
                        ->isMeaninglessAnswer(
                            $childValue
                        )
                ) {
                    $errors[
                        $childErrorKey
                    ] =
                        'Berikan pendapat agar masukan Anda dapat dianalisa.';
                }
            }
        }
    }


    private function shouldValidateMeaningfulAnswers(): bool
    {
        return in_array(
            (int) Auth::user()
                ?->role_id,
            self::MEANINGFUL_ANSWER_ROLE_IDS,
            true
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE
    |--------------------------------------------------------------------------
    */
    private function normalizeAnswer(
        mixed $value
    ): string {
        if (!is_scalar($value)) {
            return '';
        }

        $normalized =
            mb_strtolower(
                trim(
                    (string) $value
                ),
                'UTF-8'
            );

        $normalized =
            preg_replace(
                '/[^\p{L}\p{N}]+/u',
                ' ',
                $normalized
            );

        $normalized =
            preg_replace(
                '/\s+/u',
                ' ',
                (string) $normalized
            );

        return trim(
            (string) $normalized
        );
    }


    private function isMeaninglessAnswer(
        mixed $value
    ): bool {
        if (!is_scalar($value)) {
            return false;
        }

        $value =
            trim(
                (string) $value
            );

        if ($value === '') {
            return true;
        }

        if (
            preg_match(
                '/^[\p{P}\p{S}\s]+$/u',
                $value
            ) === 1
        ) {
            return true;
        }

        $normalized =
            $this->normalizeAnswer(
                $value
            );

        if ($normalized === '') {
            return true;
        }

        return in_array(
            $normalized,
            self::MEANINGLESS_ANSWERS,
            true
        );
    }


    private function isTitleQuestion(
        Form $form,
        Question $question
    ): bool {
        return $question
            ->questiontype
            ?->isTitleOnly()
            ||
            (
                (int) $form
                    ->formtype_id
                !== 1
                &&
                (int) $question
                    ->questiontype_id
                === 1
            );
    }


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

            'respondent_competitor_id' =>
                null,
        ];

        $answer =
            Answer::query()
                ->updateOrCreate(
                    $attributes,
                    [
                        'answer' =>
                            $value,
                    ]
                );

        Answer::query()
            ->where(
                $attributes
            )
            ->whereKeyNot(
                $answer->getKey()
            )
            ->delete();
    }


    private function saveRespondentCompetitorAnswers(
        Request $request,
        Form $form,
        UserProfile $profile,
        Collection $questions,
        array $answers
    ): RedirectResponse {
        $rows =
            collect(
                (array) $request
                    ->input(
                        'respondent_competitors',
                        []
                    )
            );

        $errors = [];

        if (
            $rows->isEmpty()
            ||
            $rows->count() > 10
        ) {
            $errors[
                'respondent_competitors'
            ] =
                'Jumlah kompetitor harus antara 1 sampai 10.';
        }

        $names = [];

        foreach (
            $rows
            as $index => $row
        ) {
            $name =
                trim(
                    (string) Arr::get(
                        (array) $row,
                        'name'
                    )
                );

            if ($name === '') {
                $errors[
                    "respondent_competitors.{$index}.name"
                ] =
                    'Nama kompetitor wajib diisi.';
            } elseif (
                in_array(
                    mb_strtolower(
                        $name
                    ),
                    $names,
                    true
                )
            ) {
                $errors[
                    "respondent_competitors.{$index}.name"
                ] =
                    'Nama kompetitor tidak boleh sama.';
            }

            $names[] =
                mb_strtolower(
                    $name
                );

            foreach (
                $questions
                as $question
            ) {
                if (
                    $this
                        ->isTitleQuestion(
                            $form,
                            $question
                        )
                ) {
                    continue;
                }

                $value =
                    Arr::get(
                        $answers,
                        "{$question->id}.{$index}.value"
                    );

                if (
                    !in_array(
                        (string) $value,
                        [
                            '0',
                            '1',
                            '2',
                            '3',
                            '4',
                            '5',
                            '6',
                            '7',
                        ],
                        true
                    )
                ) {
                    $errors[
                        "answers.{$question->id}.{$index}.value"
                    ] =
                        "Penilaian {$question->name} untuk {$name} wajib dipilih.";
                }
            }
        }

        if (
            $errors !== []
        ) {
            throw ValidationException
                ::withMessages(
                    $errors
                );
        }

        DB::transaction(
            function () use (
                $rows,
                $form,
                $profile,
                $questions,
                $answers
            ): void {
                $existing =
                    RespondentCompetitor
                        ::query()
                        ->where(
                            'user_id',
                            Auth::id()
                        )
                        ->where(
                            'form_id',
                            $form->id
                        )
                        ->get()
                        ->keyBy('id');

                $submittedIds =
                    $rows
                        ->pluck('id')
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->filter(
                            fn (int $id) =>
                                $existing
                                    ->has($id)
                        )
                        ->values();

                RespondentCompetitor
                    ::query()
                    ->where(
                        'user_id',
                        Auth::id()
                    )
                    ->where(
                        'form_id',
                        $form->id
                    )
                    ->whereNotIn(
                        'id',
                        $submittedIds
                    )
                    ->delete();

                $existing =
                    $existing
                        ->only(
                            $submittedIds
                                ->all()
                        );

                $kept = [];

                foreach (
                    $rows->values()
                    as $position => $row
                ) {
                    $row =
                        (array) $row;

                    $requestedId =
                        (int) (
                            $row['id']
                            ?? 0
                        );

                    $competitor =
                        $requestedId
                        &&
                        $existing
                            ->has(
                                $requestedId
                            )
                            ? $existing
                                ->get(
                                    $requestedId
                                )
                            : new RespondentCompetitor();

                    $competitor
                        ->fill([
                            'user_id' =>
                                Auth::id(),

                            'activity_id' =>
                                $profile
                                    ->activity_id,

                            'form_id' =>
                                $form->id,

                            'position' =>
                                $position + 1,

                            'name' =>
                                trim(
                                    (string) $row['name']
                                ),
                        ])
                        ->save();

                    $kept[] =
                        $competitor->id;

                    foreach (
                        $questions
                        as $question
                    ) {
                        if (
                            $this
                                ->isTitleQuestion(
                                    $form,
                                    $question
                                )
                        ) {
                            continue;
                        }

                        $value =
                            Arr::get(
                                $answers,
                                "{$question->id}.{$rows->keys()->get($position)}.value"
                            );

                        Answer::query()
                            ->updateOrCreate(
                                [
                                    'user_id' =>
                                        Auth::id(),

                                    'form_id' =>
                                        $form->id,

                                    'question_id' =>
                                        $question->id,

                                    'subunit_id' =>
                                        null,

                                    'competitor_id' =>
                                        null,

                                    'respondent_competitor_id' =>
                                        $competitor->id,
                                ],
                                [
                                    'answer' => [
                                        'value' =>
                                            $value,
                                    ],
                                ]
                            );
                    }
                }

                RespondentCompetitor
                    ::query()
                    ->where(
                        'user_id',
                        Auth::id()
                    )
                    ->where(
                        'form_id',
                        $form->id
                    )
                    ->whereNotIn(
                        'id',
                        $kept
                    )
                    ->delete();
            }
        );

        return $this
            ->goToNextForm(
                $form
            );
    }


    private function goToNextForm(
        Form $form
    ): RedirectResponse {
        $nextForm =
            app(
                \App\Services\SurveyBranchingService::class
            )
                ->nextVisibleForm(
                    $form,
                    (int) Auth::id()
                );

        SurveySession::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->update([
                'current_form_id' =>
                    $nextForm?->id,
            ]);

        return $nextForm
            ? redirect()
                ->route(
                    'survey.show',
                    [
                        'form' =>
                            $nextForm->id,
                    ]
                )
            : redirect()
                ->route(
                    'survey.finish.page'
                );
    }
}
