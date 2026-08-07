<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Competitor;
use App\Models\Form;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\SurveySession;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                ->with('error', 'Survei Anda sudah selesai.');
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
                ->with('error', 'Survei yang telah selesai tidak dapat diedit.');
        }

        $form->load([
            'description',
            'formtype',

            'questions' => function ($query) {
                $query
                    ->orderBy('no_header')
                    ->orderByRaw(
                        'CAST(no AS UNSIGNED) ASC'
                    )
                    ->orderBy('id');
            },

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

        $answerMap = [];
        Answer::where('user_id', Auth::id())
            ->where('form_id', $form->id)
            ->get()
            ->each(function (Answer $answer) use (&$answerMap): void {
                $value = $answer->answer;
                $answerMap[$answer->question_id][$answer->subunit_id ?? 0][$answer->competitor_id ?? 0]
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
            'answerMap' => $answerMap,
            'previousForm' => $currentIndex > 0 ? $forms[$currentIndex - 1] : null,
            'nextForm' => $currentIndex < $forms->count() - 1 ? $forms[$currentIndex + 1] : null,
            'isLastForm' => $currentIndex === $forms->count() - 1,
            'currentPosition' => $currentIndex + 1,
            'totalForms' => $forms->count(),
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

    private function firstIncompleteForm(UserProfile $profile): ?Form
    {
        $forms = Form::query()
            ->where('group_id', $profile->group_id)
            ->with([
                'questions:id,form_id,questiontype_id',
                'questions.questiontype:id,name',
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

        foreach ($forms as $form) {
            if ((int) $form->formtype_id === self::DESCRIPTION_TYPE) {
                continue;
            }

            $rows = $activeRows->get($form->id, collect());
            $activeQuestionIds = $rows->pluck('question_id')->map(fn ($id) => (int) $id)->unique();

            $questions = $form->questions
                ->whereIn('id', $activeQuestionIds)
                ->reject(fn ($question) => $this->isTitleQuestion($form, $question));

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
