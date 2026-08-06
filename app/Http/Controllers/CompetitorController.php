<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\Form;
use Illuminate\Http\Request;

class CompetitorController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],

            'name' => [
                'required',
                'array',
                'min:1',
            ],

            'name.*' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        foreach ($validated['name'] as $name) {
            Competitor::create([
                'group_id' => $validated['group_id'],
                'form_id' => $validated['form_id'],
                'name' => trim($name),
            ]);
        }

        return redirect()
            ->route('admin.units', [
                'id' => $validated['group_id'],
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Kompetitor berhasil ditambahkan.'
            );
    }

    public function destroy($id)
    {
        $competitor = Competitor::query()
            ->findOrFail($id);

        $groupId = $competitor->group_id;

        try {
            $competitor->delete();

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'success',
                    'Kompetitor berhasil dihapus.'
                );
        } catch (\Throwable $error) {
            report($error);

            return redirect()
                ->route('admin.units', [
                    'id' => $groupId,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Kompetitor gagal dihapus karena masih terhubung dengan data lain.'
                );
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'integer',
                'exists:forms,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $form = Form::query()
            ->where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        $competitor = Competitor::query()
            ->where('id', $id)
            ->where('group_id', $form->group_id)
            ->where('form_id', $form->id)
            ->firstOrFail();

        $competitor->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $form->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Kompetitor berhasil diperbarui.'
            );
    }
}