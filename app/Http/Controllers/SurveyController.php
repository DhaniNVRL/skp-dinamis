<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Form;
use App\Models\RespondentCompetitor;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\SurveySession;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SurveyController extends Controller
{
    private const PER_SUBUNIT_TYPES = [2, 3, 8, 9, 10];

    private const COMPETITOR_TYPES = [11, 13];

    private const DESCRIPTION_TYPE = 12;

    public function index(): RedirectResponse
    {
        $profile = $this->completeProfile();
        $session = SurveySession::where('user_id', Auth::id())->first();

        if ($session?->status === 'completed') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Survei sudah selesai dan akun terkunci. Admin harus melakukan Reset Account sebelum pengisian dapat dimulai kembali.');
        }

        $form = $session?->current_form_id
            ? Form::where('group_id', $profile->group_id)->find($session->current_form_id)
            : null;

        $form ??= Form::where('group_id', $profile->group_id)
            ->orderBy('no_urut')
            ->orderBy('id')
            ->first();

        abort_unless($form, 404, 'Form survei untuk group Anda belum tersedia.');

        SurveySession::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'activity_id' => $profile->activity_id,
                'group_id' => $profile->group_id,
                'unit_id' => $profile->unit_id,
                'current_form_id' => $form->id,
                'status' => 'in_progress',
                'started_at' => $session?->started_at ?? now(),
                'finished_at' => null,
            ]
        );

        return redirect()->route('survey.show', $form);
    }

    public function show(Form $form): View|RedirectResponse
    {
        $profile = $this->completeProfile();

        abort_unless((int) $form->group_id === (int) $profile->group_id, 403);

        $session = SurveySession::where('user_id', Auth::id())->first();
        if ($session?->status === 'completed') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Survei yang telah selesai tidak dapat dibuka kembali sebelum Admin melakukan Reset Account.');
        }

        $branching = app(\App\Services\SurveyBranchingService::class);
        if ($branching->shouldSkipForm($form, (int) Auth::id())) {
            $nextVisibleForm = $branching->nextVisibleForm($form, (int) Auth::id());

            return $nextVisibleForm
                ? redirect()->route('survey.show', $nextVisibleForm)
                : redirect()->route('survey.finish.page');
        }

        $form->load([
            'description',
            'formtype',

            'questions',

            'questions.questiontype',

            'questions.options' => function ($query) {
                $query
                    ->orderByRaw(
                        'CAST(no AS UNSIGNED) ASC'
                    )
                    ->orderBy('id');
            },
        ]);

        $forms = Form::where('group_id', $profile->group_id)
            ->orderBy('no_urut')
            ->orderBy('id')
            ->get();

        $currentIndex = $forms->search(fn (Form $item) => $item->is($form));
        abort_if($currentIndex === false, 404);
        $visibleForms = $forms
            ->reject(fn (Form $item) => $branching->shouldSkipForm($item, (int) Auth::id()))
            ->values();
        $visibleCurrentIndex = $visibleForms->search(fn (Form $item) => $item->is($form));
        abort_if($visibleCurrentIndex === false, 404);

        $subunits = SubUnit::where('unit_id', $profile->unit_id)
            ->orderBy('name')
            ->get();

        $subunitIds = $subunits->pluck('id');
        $activeRows = SubUnitQuestion::where('form_id', $form->id)
            ->whereIn('subunit_id', $subunitIds)
            ->get();

        $activeMapSubUnit = $activeRows
            ->groupBy(fn ($row) => $row->form_id.'-'.$row->question_id)
            ->map(fn ($rows) => $rows->pluck('subunit_id')->map(fn ($id) => (int) $id)->unique()->values()->all())
            ->all();

        if ((int) $form->formtype_id !== 12) {
            $activeQuestionIds = $activeRows->pluck('question_id')->unique();
            $form->setRelation(
                'questions',
                $form->questions->whereIn('id', $activeQuestionIds)->values()
            );
        }

        $questions = $form->questions;
        $competitors = Competitor::where('group_id', $profile->group_id)
            ->orderBy('name')
            ->get();
        // Database lama dapat belum memiliki tabel untuk form pembanding
        // dinamis. Jangan biarkan seluruh halaman survei berakhir 500.
        $respondentCompetitors = Schema::hasTable('respondent_competitors')
            ? RespondentCompetitor::query()
                ->where('user_id', Auth::id())
                ->where('form_id', $form->id)
                ->orderBy('position')
                ->get()
            : collect();

        $answerMap = [];
        Answer::where('user_id', Auth::id())
            ->where('form_id', $form->id)
            ->get()
            ->each(function (Answer $answer) use (&$answerMap): void {
                $value = $answer->answer;
                $targetId = $answer->respondent_competitor_id ?? $answer->competitor_id ?? 0;
                $answerMap[$answer->question_id][$answer->subunit_id ?? 0][$targetId]
                    = is_array($value) ? $value : ['value' => $answer->answer];
            });

        SurveySession::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'activity_id' => $profile->activity_id,
                'group_id' => $profile->group_id,
                'unit_id' => $profile->unit_id,
                'current_form_id' => $form->id,
                'status' => 'in_progress',
                'started_at' => $session?->started_at ?? now(),
                'finished_at' => null,
            ]
        );

        return view('user.survey.index', [
            'form' => $form,
            'questions' => $questions,
            'subunits' => $subunits,
            'activeMapSubUnit' => $activeMapSubUnit,
            'competitors' => $competitors,
            'respondentCompetitors' => $respondentCompetitors,
            'answerMap' => $answerMap,
            'conditionalBranches' => $branching->definitions($form),
            'previousForm' => $visibleCurrentIndex > 0 ? $visibleForms[$visibleCurrentIndex - 1] : null,
            'nextForm' => $visibleCurrentIndex < $visibleForms->count() - 1 ? $visibleForms[$visibleCurrentIndex + 1] : null,
            'firstQuestionForm' => $visibleForms->first(
                fn (Form $item) => (int) $item->formtype_id !== 12
            ),
            'isLastForm' => $visibleCurrentIndex === $visibleForms->count() - 1,
            // Nomor tampilan mengikuti no_urut asli. Form yang dilewati tidak
            // membuat form setelahnya dinomori ulang (mis. 6 langsung 8).
            'currentPosition' => (int) $form->no_urut,
            'totalForms' => max((int) $forms->max('no_urut'), $forms->count()),
        ]);
    }

    public function finishPage(): View|RedirectResponse
    {
        $profile = $this->completeProfile();
        $session = SurveySession::where('user_id', Auth::id())->first();

        if ($session?->status === 'completed') {
            return redirect()->route('user.dashboard');
        }

        if (! $session) {
            return redirect()->route('survey.index')
                ->with('error', 'Mulai survei sebelum menyelesaikannya.');
        }

        $incompleteForm = $this->firstIncompleteForm($profile);

        if ($incompleteForm) {
            return redirect()->route('survey.show', $incompleteForm)
                ->with('error', 'Lengkapi seluruh jawaban pada form ini sebelum menyelesaikan survei.');
        }

        $lastForm = Form::where('group_id', $profile->group_id)
            ->orderByDesc('no_urut')
            ->orderByDesc('id')
            ->first();

        return view('user.survey.finish', compact('lastForm'));
    }

    public function finish(
            Request $request
        ): RedirectResponse {
            $session = SurveySession::query()
                ->where(
                    'user_id',
                    Auth::id()
                )
                ->first();

            if (!$session) {
                return redirect()
                    ->route('user.dashboard')
                    ->with(
                        'error',
                        'Session survei tidak ditemukan.'
                    );
            }

            /*
            * Jika sudah selesai, jangan diproses
            * kembali.
            */
            if ($session->status === 'completed') {
                if ($this->isSurveyor()) {
                    return redirect()
                        ->route('user.dashboard')
                        ->with('error', 'Simulasi sudah selesai dan terkunci. Hubungi Admin untuk melakukan Reset Account.');
                }

                Auth::logout();

                $request
                    ->session()
                    ->invalidate();

                $request
                    ->session()
                    ->regenerateToken();

                return redirect()
                    ->route('login')
                    ->with(
                        'finish',
                        'Terima kasih, nilai sudah terinput.'
                    );
            }

            $profile = $this->completeProfile();


            $incompleteForm = $this->firstIncompleteForm($profile);

            if ($incompleteForm) {
                return redirect()
                    ->route('survey.show', $incompleteForm)
                    ->with(
                        'error',
                        'Survei belum lengkap. Lengkapi seluruh jawaban sebelum menyelesaikan survei.'
                    );
            }

            /*
            * Selesaikan survei.
            */
            $session->update([
                'status' => 'completed',
                'finished_at' => now(),
                'current_form_id' => null,
            ]);

            if ($this->isSurveyor()) {
                return redirect()
                    ->route('user.dashboard')
                    ->with('success', 'Simulasi selesai dan dikunci. Jawaban serta profil tetap tersimpan sampai Admin melakukan Reset Account.');
            }

            /*
            * Logout responden.
            */
            Auth::logout();

            /*
            * Hapus session login lama.
            */
            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            /*
            * Redirect ke login dengan alert.
            */
            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Terima kasih, nilai sudah terinput.'
                );
        }

    private function completeProfile(): UserProfile
    {
        $profile = UserProfile::where('user_id', Auth::id())->first();

        if (!$profile || !$profile->activity_id || !$profile->group_id || !$profile->unit_id) {
            abort(403, 'Lengkapi profil responden sebelum memulai survei.');
        }

        return $profile;
    }

    private function isSurveyor(): bool
    {
        return Auth::user()?->hasRole('surveyor') ?? false;
    }

    private function firstIncompleteForm(UserProfile $profile): ?Form
    {
        $forms = Form::query()
            ->where('group_id', $profile->group_id)
            ->with([
                'questions:id,form_id,no_header,no,questiontype_id',
                'questions.questiontype:id,name',
                'questions.options:id,question_id,answer_text',
            ])
            ->orderBy('no_urut')
            ->orderBy('id')
            ->get();

        $subunitIds = SubUnit::query()
            ->where('unit_id', $profile->unit_id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        $activeRows = SubUnitQuestion::query()
            ->whereIn('form_id', $forms->pluck('id'))
            ->whereIn('subunit_id', $subunitIds)
            ->get(['form_id', 'question_id', 'subunit_id'])
            ->groupBy('form_id');

        $competitorIds = Competitor::query()
            ->where('group_id', $profile->group_id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        $answerKeys = Answer::query()
            ->where('user_id', Auth::id())
            ->whereIn('form_id', $forms->pluck('id'))
            ->get(['form_id', 'question_id', 'subunit_id', 'competitor_id', 'answer'])
            ->filter(fn (Answer $answer) => $this->hasMeaningfulValue($answer->answer))
            ->mapWithKeys(fn (Answer $answer) => [
                $this->answerKey(
                    $answer->form_id,
                    $answer->question_id,
                    $answer->subunit_id,
                    $answer->competitor_id
                ) => true,
            ]);

        $branching = app(\App\Services\SurveyBranchingService::class);

        foreach ($forms as $form) {
            if ((int) $form->formtype_id === self::DESCRIPTION_TYPE
                || $branching->shouldSkipForm($form, (int) Auth::id())) {
                continue;
            }

            $rows = $activeRows->get($form->id, collect());
            $activeQuestionIds = $rows->pluck('question_id')->map(fn ($id) => (int) $id)->unique();

            $questions = $form->questions
                ->whereIn('id', $activeQuestionIds)
                ->reject(fn ($question) => $this->isTitleQuestion($form, $question));

            if ((int) $form->formtype_id === 1) {
                $savedPayloads = Answer::query()
                    ->where('user_id', Auth::id())
                    ->where('form_id', $form->id)
                    ->get()
                    ->mapWithKeys(fn (Answer $answer) => [
                        $answer->question_id => is_array($answer->answer)
                            ? $answer->answer
                            : ['value' => $answer->answer],
                    ])
                    ->all();
                $hiddenIds = $branching->hiddenQuestionIds($form, $savedPayloads);
                $questions = $questions->reject(
                    fn ($question) => $hiddenIds->contains((int) $question->id)
                );
            }

            foreach ($questions as $question) {
                if (in_array((int) $form->formtype_id, self::PER_SUBUNIT_TYPES, true)) {
                    $targets = $rows
                        ->where('question_id', $question->id)
                        ->pluck('subunit_id')
                        ->map(fn ($id) => (int) $id)
                        ->unique();

                    foreach ($targets as $subunitId) {
                        if (! $answerKeys->has($this->answerKey($form->id, $question->id, $subunitId, null))) {
                            return $form;
                        }
                    }

                    continue;
                }

                if ((int) $form->formtype_id === 14) {
                    $respondentCompetitorIds = RespondentCompetitor::query()
                        ->where('user_id', Auth::id())
                        ->where('form_id', $form->id)
                        ->pluck('id');

                    if ($respondentCompetitorIds->isEmpty()) {
                        return $form;
                    }

                    foreach ($respondentCompetitorIds as $respondentCompetitorId) {
                        $saved = Answer::query()
                            ->where('user_id', Auth::id())
                            ->where('form_id', $form->id)
                            ->where('question_id', $question->id)
                            ->where('respondent_competitor_id', $respondentCompetitorId)
                            ->get()
                            ->contains(fn (Answer $answer) => $this->hasMeaningfulValue($answer->answer));
                        if (! $saved) {
                            return $form;
                        }
                    }

                    continue;
                }
                if (in_array((int) $form->formtype_id, self::COMPETITOR_TYPES, true)) {
                    foreach ($competitorIds as $competitorId) {
                        if (! $answerKeys->has($this->answerKey($form->id, $question->id, null, $competitorId))) {
                            return $form;
                        }
                    }

                    continue;
                }

                if (! $answerKeys->has($this->answerKey($form->id, $question->id, null, null))) {
                    return $form;
                }
            }
        }

        return null;
    }

    private function isTitleQuestion(Form $form, $question): bool
    {
        return $question->questiontype?->isTitleOnly()
            || (
                (int) $form->formtype_id !== 1
                && (int) $question->questiontype_id === 1
            );
    }

    private function answerKey(
        int $formId,
        int $questionId,
        ?int $subunitId,
        ?int $competitorId
    ): string {
        return implode(':', [
            $formId,
            $questionId,
            $subunitId ?? 0,
            $competitorId ?? 0,
        ]);
    }

    private function hasMeaningfulValue($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->hasMeaningfulValue($item)) {
                    return true;
                }
            }

            return false;
        }

        return $value === 0
            || $value === '0'
            || $value === false
            || (is_string($value) && trim($value) !== '')
            || (is_numeric($value) && $value !== '');
    }
}

