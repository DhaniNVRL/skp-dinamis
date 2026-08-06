<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\SubUnit;
use App\Models\SubUnitQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubUnitQuestionController extends Controller
{
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

        /*
         * Pastikan pertanyaan memang milik form tersebut.
         * Sesuaikan form_id jika kolom Question Anda bernama id_forms.
         */
        if ((int) $question->form_id !== (int) $form->id) {
            throw ValidationException::withMessages([
                'question_id' => 'Pertanyaan tidak terdaftar pada form yang dipilih.',
            ]);
        }

        $subunits = SubUnit::query()
            ->whereIn('id', $validated['subunit_ids'])
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