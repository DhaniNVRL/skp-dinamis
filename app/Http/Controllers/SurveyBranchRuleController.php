<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Option;
use App\Models\Question;
use App\Models\SurveyBranchRule;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SurveyBranchRuleController extends Controller
{
    public function store(Request $request, Group $group): RedirectResponse
    {
        $validated = $request->validate([
            'parent_question_id' => [
                'required', 'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'affirmative_option_id' => ['required', 'integer', 'exists:options,id'],
            'dependent_question_ids' => ['nullable', 'array'],
            'dependent_question_ids.*' => [
                'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'skipped_question_ids' => ['nullable', 'array'],
            'skipped_question_ids.*' => [
                'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'skip_form_ids' => ['nullable', 'array'],
            'skip_form_ids.*' => [
                'integer',
                Rule::exists('forms', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
        ]);

        $parent = Question::query()->where('group_id', $group->id)->findOrFail($validated['parent_question_id']);
        Option::query()->where('question_id', $parent->id)->findOrFail($validated['affirmative_option_id']);

        $shownIds = array_values(array_unique(array_map('intval', $validated['dependent_question_ids'] ?? [])));
        $skippedIds = array_values(array_unique(array_map('intval', $validated['skipped_question_ids'] ?? [])));
        $configuredQuestionIds = array_values(array_unique([...$shownIds, ...$skippedIds]));
        $skippedFormIds = array_values(array_unique(array_map('intval', $validated['skip_form_ids'] ?? [])));

        if (in_array((int) $parent->form_id, $skippedFormIds, true)) {
            throw ValidationException::withMessages([
                'skip_form_ids' => 'Form tempat pertanyaan pemicu berada tidak boleh dilewati.',
            ]);
        }

        if ($configuredQuestionIds === [] && $skippedFormIds === []) {
            throw ValidationException::withMessages([
                'dependent_question_ids' => 'Pilih minimal satu aksi: tampilkan pertanyaan, lewati pertanyaan, atau lewati form.',
            ]);
        }

        $validQuestionCount = Question::query()
            ->where('group_id', $group->id)
            ->where('form_id', $parent->form_id)
            ->where('id', '!=', $parent->id)
            ->whereKey($configuredQuestionIds)
            ->count();

        if ($validQuestionCount !== count($configuredQuestionIds)) {
            throw ValidationException::withMessages([
                'dependent_question_ids' => 'Pertanyaan yang diatur harus berasal dari form pemicu yang sama dan bukan pertanyaan pemicu.',
            ]);
        }

        if (array_intersect($shownIds, $skippedIds) !== []) {
            throw ValidationException::withMessages([
                'skipped_question_ids' => 'Pertanyaan yang sama tidak boleh sekaligus ditampilkan dan dilewati.',
            ]);
        }

        try {
            DB::transaction(function () use ($group, $validated, $shownIds, $skippedIds, $skippedFormIds): void {
                $rule = SurveyBranchRule::query()->create([
                    'group_id' => $group->id,
                    'parent_question_id' => $validated['parent_question_id'],
                    'affirmative_option_id' => $validated['affirmative_option_id'],
                    'skip_form_id' => $skippedFormIds[0] ?? null,
                ]);
                $rule->dependentQuestions()->sync($shownIds);
                $rule->skippedQuestions()->sync($skippedIds);
                $rule->skippedForms()->sync($skippedFormIds);
            });
        } catch (QueryException $exception) {
            if (in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                throw ValidationException::withMessages([
                    'affirmative_option_id' => 'Konfigurasi untuk pertanyaan dan opsi tersebut sudah ada.',
                ]);
            }
            throw $exception;
        }

        return redirect()->route('admin.units', ['id' => $group->id, 'tab' => 'question'])
            ->with('success', 'Konfigurasi percabangan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Group $group, SurveyBranchRule $rule): RedirectResponse
    {
        abort_unless((int) $rule->group_id === (int) $group->id, 404);

        $validated = $request->validate([
            'parent_question_id' => [
                'required', 'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'affirmative_option_id' => ['required', 'integer', 'exists:options,id'],
            'dependent_question_ids' => ['nullable', 'array'],
            'dependent_question_ids.*' => [
                'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'skipped_question_ids' => ['nullable', 'array'],
            'skipped_question_ids.*' => [
                'integer',
                Rule::exists('questions', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
            'skip_form_ids' => ['nullable', 'array'],
            'skip_form_ids.*' => [
                'integer',
                Rule::exists('forms', 'id')->where(fn ($query) => $query->where('group_id', $group->id)),
            ],
        ]);

        $parent = Question::query()->where('group_id', $group->id)->findOrFail($validated['parent_question_id']);
        Option::query()->where('question_id', $parent->id)->findOrFail($validated['affirmative_option_id']);

        $shownIds = array_values(array_unique(array_map('intval', $validated['dependent_question_ids'] ?? [])));
        $skippedIds = array_values(array_unique(array_map('intval', $validated['skipped_question_ids'] ?? [])));
        $skippedFormIds = array_values(array_unique(array_map('intval', $validated['skip_form_ids'] ?? [])));
        $configuredQuestionIds = array_values(array_unique([...$shownIds, ...$skippedIds]));

        if (in_array((int) $parent->form_id, $skippedFormIds, true)) {
            throw ValidationException::withMessages([
                'skip_form_ids' => 'Form tempat pertanyaan pemicu berada tidak boleh dilewati.',
            ]);
        }
        if ($configuredQuestionIds === [] && $skippedFormIds === []) {
            throw ValidationException::withMessages([
                'dependent_question_ids' => 'Pilih minimal satu aksi: tampilkan pertanyaan, lewati pertanyaan, atau lewati form.',
            ]);
        }
        $validQuestionCount = Question::query()
            ->where('group_id', $group->id)
            ->where('form_id', $parent->form_id)
            ->where('id', '!=', $parent->id)
            ->whereKey($configuredQuestionIds)
            ->count();
        if ($validQuestionCount !== count($configuredQuestionIds)) {
            throw ValidationException::withMessages([
                'dependent_question_ids' => 'Pertanyaan yang diatur harus berasal dari form pemicu yang sama dan bukan pertanyaan pemicu.',
            ]);
        }
        if (array_intersect($shownIds, $skippedIds) !== []) {
            throw ValidationException::withMessages([
                'skipped_question_ids' => 'Pertanyaan yang sama tidak boleh sekaligus ditampilkan dan dilewati.',
            ]);
        }

        try {
            DB::transaction(function () use ($rule, $validated, $shownIds, $skippedIds, $skippedFormIds): void {
                $rule->update([
                    'parent_question_id' => $validated['parent_question_id'],
                    'affirmative_option_id' => $validated['affirmative_option_id'],
                    'skip_form_id' => $skippedFormIds[0] ?? null,
                ]);
                $rule->dependentQuestions()->sync($shownIds);
                $rule->skippedQuestions()->sync($skippedIds);
                $rule->skippedForms()->sync($skippedFormIds);
            });
        } catch (QueryException $exception) {
            if (in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                throw ValidationException::withMessages([
                    'affirmative_option_id' => 'Konfigurasi untuk pertanyaan dan opsi tersebut sudah digunakan rule lain.',
                ]);
            }
            throw $exception;
        }

        return redirect()->route('admin.units', ['id' => $group->id, 'tab' => 'question'])
            ->with('success', 'Konfigurasi percabangan berhasil diperbarui.');
    }
    public function destroy(Group $group, SurveyBranchRule $rule): RedirectResponse
    {
        abort_unless((int) $rule->group_id === (int) $group->id, 404);

        DB::transaction(function () use ($rule): void {
            $rule->delete();
        });

        return redirect()->route('admin.units', ['id' => $group->id, 'tab' => 'question'])
            ->with('success', 'Konfigurasi percabangan berhasil dihapus.');
    }
}