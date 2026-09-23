<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Unit;
use App\Models\UnitFormVisibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnitFormVisibilityController extends Controller
{
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
