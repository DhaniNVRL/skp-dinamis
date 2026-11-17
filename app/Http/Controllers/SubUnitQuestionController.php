<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubUnitQuestionController extends Controller
{
    public function showAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'form_id' => ['required', 'integer', 'exists:forms,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $form = Form::query()->findOrFail($validated['form_id']);
        $unit = Unit::query()->findOrFail($validated['unit_id']);

        if ((int) $unit->group_id !== (int) $form->group_id) {
            throw ValidationException::withMessages([
                'form_id' => 'Form tidak terdaftar pada group milik Unit ini.',
            ]);
        }

        $subunits = SubUnit::query()
            ->where('unit_id', $validated['unit_id'])
            ->get(['id']);

        if ($subunits->isEmpty()) {
            throw ValidationException::withMessages([
                'unit_id' => 'Unit ini belum memiliki Sub Unit.',
            ]);
        }

        $questionIds = Question::query()
            ->where('form_id', $form->id)
            ->pluck('id');

        DB::transaction(function () use ($form, $subunits, $questionIds, $validated): void {
            foreach ($questionIds as $questionId) {
                foreach ($subunits as $subunit) {
                    $attributes = [
                        'form_id' => $form->id,
                        'question_id' => $questionId,
                        'subunit_id' => $subunit->id,
                    ];

                    if ($validated['is_active']) {
                        SubUnitQuestion::query()->firstOrCreate($attributes);
                    } else {
                        SubUnitQuestion::query()->where($attributes)->delete();
                    }
                }
            }
        });

        return response()->json([
            'is_active' => (bool) $validated['is_active'],
            'question_count' => $questionIds->count(),
            'affected_subunits' => $subunits->count(),
            'message' => $validated['is_active']
                ? 'Semua pertanyaan pada form berhasil ditampilkan.'
                : 'Semua pertanyaan pada form berhasil disembunyikan.',
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],
            'question_id' => [
                'required',
                'integer',
                'exists:questions,id',
            ],
            'subunit_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'subunit_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:subunits,id',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $form = Form::findOrFail(
            $validated['form_id']
        );

        $question = Question::findOrFail(
            $validated['question_id']
        );

        // Pastikan pertanyaan memang milik form tersebut.
        // Sesuaikan form_id jika kolom Question Anda bernama id_forms.
        if ((int) $question->form_id !== (int) $form->id) {
            throw ValidationException::withMessages([
                'question_id' => 'Pertanyaan tidak terdaftar pada form yang dipilih.',
            ]);
        }

        $subunits = SubUnit::query()
            ->whereIn('id', $validated['subunit_ids'])
            ->whereHas('unit', function ($query) use ($form): void {
                $query->where('group_id', $form->group_id);
            })
            ->get();

        if (
            $subunits->count() !==
            count($validated['subunit_ids'])
        ) {
            throw ValidationException::withMessages([
                'subunit_ids' => 'Terdapat Sub Unit yang tidak valid.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['subunit_ids'] as $subunitId) {
                $attributes = [
                    'form_id' => $validated['form_id'],
                    'question_id' => $validated['question_id'],
                    'subunit_id' => $subunitId,
                ];

                if ($validated['is_active']) {
                    SubUnitQuestion::firstOrCreate(
                        $attributes
                    );
                } else {
                    SubUnitQuestion::query()
                        ->where($attributes)
                        ->delete();
                }
            }
        });

        return response()->json([
            'status' => $validated['is_active']
                ? 'added'
                : 'removed',
            'is_active' => (bool) $validated['is_active'],
            'affected_subunits' => count(
                $validated['subunit_ids']
            ),
            'message' => $validated['is_active']
                ? 'Pertanyaan berhasil ditampilkan.'
                : 'Pertanyaan berhasil disembunyikan.',
        ]);
    }
}
