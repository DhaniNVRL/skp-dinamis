<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use App\Models\SurveyBranchRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SurveyBranchingService
{
    /**
     * Percabangan otomatis untuk Kuesioner Umum.
     *
     * Pertanyaan bernomor 1 dengan opsi Ya/Tidak menjadi pemicu ketika
     * pada form yang sama terdapat nomor 1a, 1b, 1c, dan seterusnya.
     */
    public function definitions(Form $form): Collection
    {
        if ((int) $form->formtype_id !== 1) {
            return collect();
        }

        if (Schema::hasTable('survey_branch_rules')) {
            $configured = SurveyBranchRule::query()
                ->where('group_id', $form->group_id)
                ->whereHas('parentQuestion', fn ($query) => $query->where('form_id', $form->id))
                ->with(['parentQuestion', 'affirmativeOption', 'dependentQuestions', 'skippedQuestions', 'skippedForms'])
                ->get();

            if ($configured->isNotEmpty()) {
                return $configured->map(fn (SurveyBranchRule $rule) => [
                    'rule_id' => (int) $rule->id,
                    'parent_id' => (int) $rule->parent_question_id,
                    'parent_number' => $this->number($rule->parentQuestion),
                    'affirmative_option_ids' => [(int) $rule->affirmative_option_id],
                    'dependent_question_ids' => $rule->dependentQuestions
                        ->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'skipped_question_ids' => $rule->skippedQuestions
                        ->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'skip_form_ids' => $rule->skippedForms->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
                    'skip_form_id' => $rule->skip_form_id ? (int) $rule->skip_form_id : null,
                ])->values();
            }
        }

        $form->loadMissing(['questions.options']);
        $questions = $form->questions->values();

        return $questions
            ->map(function (Question $parent) use ($questions): ?array {
                $parentNumber = $this->number($parent);

                if (! preg_match('/^[A-Z]*\d+$/i', $parentNumber)) {
                    return null;
                }

                $affirmativeOptionIds = $parent->options
                    ->filter(fn ($option) => $this->isAffirmativeText($option->answer_text))
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                if ($affirmativeOptionIds->isEmpty()) {
                    return null;
                }

                $pattern = '/^'.preg_quote($parentNumber, '/').'[._-]?[A-Z]+$/i';
                $dependentIds = $questions
                    ->filter(fn (Question $candidate) =>
                        (int) $candidate->id !== (int) $parent->id
                        && preg_match($pattern, $this->number($candidate)) === 1
                    )
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values();

                if ($dependentIds->isEmpty()) {
                    return null;
                }

                return [
                    'parent_id' => (int) $parent->id,
                    'parent_number' => $parentNumber,
                    'affirmative_option_ids' => $affirmativeOptionIds->all(),
                    'dependent_question_ids' => $dependentIds->all(),
                    'skipped_question_ids' => [],
                ];
            })
            ->filter()
            ->values();
    }

    public function hiddenQuestionIds(Form $form, array $submittedAnswers): Collection
    {
        $definitions = $this->definitions($form);
        $shownTargets = $definitions
            ->flatMap(fn (array $definition) => $definition['dependent_question_ids'] ?? [])
            ->unique();

        $matchedShownTargets = $definitions
            ->filter(fn (array $definition) => $this->isAffirmativeSelection(
                $definition,
                data_get($submittedAnswers, $definition['parent_id'].'.value')
            ))
            ->flatMap(fn (array $definition) => $definition['dependent_question_ids'] ?? [])
            ->unique();

        $matchedSkippedTargets = $definitions
            ->filter(fn (array $definition) => $this->isAffirmativeSelection(
                $definition,
                data_get($submittedAnswers, $definition['parent_id'].'.value')
            ))
            ->flatMap(fn (array $definition) => $definition['skipped_question_ids'] ?? [])
            ->unique();

        return $shownTargets
            ->diff($matchedShownTargets)
            ->merge($matchedSkippedTargets)
            ->unique()
            ->values();
    }
    public function shouldSkipForm(Form $form, int $userId): bool
    {
        if (Schema::hasTable('survey_branch_rules')) {
            $configured = SurveyBranchRule::query()
                ->where('group_id', $form->group_id)
                ->where(function ($query) use ($form): void {
                    $query->where('skip_form_id', $form->id)
                        ->orWhereHas('skippedForms', fn ($forms) => $forms->where('forms.id', $form->id));
                })
                ->with('parentQuestion')
                ->get();

            if ($configured->isNotEmpty()) {
                $answers = Answer::query()
                    ->where('user_id', $userId)
                    ->whereIn('question_id', $configured->pluck('parent_question_id'))
                    ->get()
                    ->keyBy('question_id');

                // Form yang dicentang dilewati ketika opsi pemicu rule dipilih,
                // sama seperti perilaku lewati pertanyaan pada konfigurasi admin.
                return $configured->contains(function (SurveyBranchRule $rule) use ($answers): bool {
                    $answer = $answers->get($rule->parent_question_id);
                    $selected = is_array($answer?->answer)
                        ? data_get($answer->answer, 'value')
                        : $answer?->answer;

                    return (int) $selected === (int) $rule->affirmative_option_id;
                });
            }
        }

        if (! in_array((int) $form->formtype_id, [11, 13], true)) {
            return false;
        }

        $previous = Form::query()
            ->where('group_id', $form->group_id)
            ->where(function ($query) use ($form): void {
                $query
                    ->where('no_urut', '<', $form->no_urut)
                    ->orWhere(function ($sameOrder) use ($form): void {
                        $sameOrder
                            ->where('no_urut', $form->no_urut)
                            ->where('id', '<', $form->id);
                    });
            })
            ->orderByDesc('no_urut')
            ->orderByDesc('id')
            ->first();

        if (! $previous || (int) $previous->formtype_id !== 1) {
            return false;
        }

        $definitions = $this->definitions($previous);
        if ($definitions->isEmpty()) {
            return false;
        }

        $parentIds = $definitions->pluck('parent_id');
        $answers = Answer::query()
            ->where('user_id', $userId)
            ->where('form_id', $previous->id)
            ->whereIn('question_id', $parentIds)
            ->get()
            ->keyBy('question_id');

        return ! $definitions->contains(function (array $definition) use ($answers): bool {
            $answer = $answers->get($definition['parent_id']);
            $selected = is_array($answer?->answer)
                ? data_get($answer->answer, 'value')
                : $answer?->answer;

            return $this->isAffirmativeSelection($definition, $selected);
        });
    }

    public function nextVisibleForm(Form $form, int $userId): ?Form
    {
        return $this->formsAfter($form)
            ->first(fn (Form $candidate) => ! $this->shouldSkipForm($candidate, $userId));
    }

    public function previousVisibleForm(Form $form, int $userId): ?Form
    {
        return $this->formsBefore($form)
            ->first(fn (Form $candidate) => ! $this->shouldSkipForm($candidate, $userId));
    }

    private function formsAfter(Form $form): Collection
    {
        return Form::query()
            ->where('group_id', $form->group_id)
            ->where(function ($query) use ($form): void {
                $query
                    ->where('no_urut', '>', $form->no_urut)
                    ->orWhere(function ($sameOrder) use ($form): void {
                        $sameOrder->where('no_urut', $form->no_urut)->where('id', '>', $form->id);
                    });
            })
            ->orderBy('no_urut')
            ->orderBy('id')
            ->get();
    }

    private function formsBefore(Form $form): Collection
    {
        return Form::query()
            ->where('group_id', $form->group_id)
            ->where(function ($query) use ($form): void {
                $query
                    ->where('no_urut', '<', $form->no_urut)
                    ->orWhere(function ($sameOrder) use ($form): void {
                        $sameOrder->where('no_urut', $form->no_urut)->where('id', '<', $form->id);
                    });
            })
            ->orderByDesc('no_urut')
            ->orderByDesc('id')
            ->get();
    }

    private function isAffirmativeSelection(array $definition, mixed $selected): bool
    {
        return in_array((int) $selected, $definition['affirmative_option_ids'], true);
    }

    private function isAffirmativeText(?string $text): bool
    {
        return preg_match('/^(ya|yes)(\b|\s|\/)/iu', trim((string) $text)) === 1;
    }

    private function number(Question $question): string
    {
        return preg_replace(
            '/\s+/',
            '',
            trim((string) $question->no_header.(string) $question->no)
        );
    }
}
