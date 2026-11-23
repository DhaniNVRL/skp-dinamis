<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\Unit;
use App\Models\UnitFormVisibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnitFormVisibilityController extends Controller
{
    public function toggleAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'is_visible' => ['required', 'boolean'],
        ]);

        $unit = Unit::query()->findOrFail($validated['unit_id']);
        $forms = Form::query()
            ->where('group_id', $unit->group_id)
            ->get(['id']);
        $subunitIds = SubUnit::query()
            ->where('unit_id', $unit->id)
            ->pluck('id');

        if ($forms->isEmpty()) {
            throw ValidationException::withMessages([
                'unit_id' => 'Unit ini belum memiliki form yang dapat diatur.',
            ]);
        }

        if ($subunitIds->isEmpty()) {
            throw ValidationException::withMessages([
                'unit_id' => 'Tambahkan Sub Unit terlebih dahulu sebelum menampilkan seluruh pertanyaan.',
            ]);
        }

        $formIds = $forms->pluck('id');
        $questions = Question::query()
            ->whereIn('form_id', $formIds)
            ->get(['id', 'form_id']);

        DB::transaction(function () use ($unit, $forms, $formIds, $questions, $subunitIds, $validated): void {
            foreach ($forms as $form) {
                UnitFormVisibility::query()->updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'form_id' => $form->id,
                    ],
                    ['is_visible' => (bool) $validated['is_visible']]
                );
            }

            if (! $validated['is_visible']) {
                SubUnitQuestion::query()
                    ->whereIn('form_id', $formIds)
                    ->whereIn('subunit_id', $subunitIds)
                    ->delete();

                return;
            }

            foreach ($questions as $question) {
                foreach ($subunitIds as $subunitId) {
                    SubUnitQuestion::query()->firstOrCreate([
                        'form_id' => $question->form_id,
                        'question_id' => $question->id,
                        'subunit_id' => $subunitId,
                    ]);
                }
            }
        });

        return response()->json([
            'is_visible' => (bool) $validated['is_visible'],
            'form_count' => $forms->count(),
            'question_count' => $questions->count(),
            'affected_subunits' => $subunitIds->count(),
            'message' => $validated['is_visible']
                ? 'Seluruh form dan pertanyaan berhasil ditampilkan.'
                : 'Seluruh form dan pertanyaan berhasil disembunyikan.',
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'is_visible' => ['required', 'boolean'],
        ]);

        $unit = Unit::query()->findOrFail($validated['unit_id']);
        $form = Form::query()->findOrFail($validated['form_id']);

        if ((int) $unit->group_id !== (int) $form->group_id) {
            throw ValidationException::withMessages([
                'form_id' => 'Form tidak terdaftar pada group milik Unit ini.',
            ]);
        }

        $visibility = UnitFormVisibility::query()->updateOrCreate(
            [
                'unit_id' => $unit->id,
                'form_id' => $form->id,
            ],
            ['is_visible' => (bool) $validated['is_visible']]
        );

        return response()->json([
            'is_visible' => (bool) $visibility->is_visible,
            'message' => $visibility->is_visible
                ? 'Form berhasil ditampilkan.'
                : 'Form berhasil disembunyikan dan akan dilewati oleh responden.',
        ]);
    }
}
