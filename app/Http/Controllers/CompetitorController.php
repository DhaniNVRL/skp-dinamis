<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\Form;
use App\Models\SurveySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $form = Form::query()
            ->where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return back()->with('error', 'Kompetitor tidak dapat ditambahkan karena survei pada group ini sudah dimulai.');
        }

        DB::transaction(function () use ($validated, $form): void {
            foreach ($validated['name'] as $name) {
                Competitor::create([
                    'group_id' => $form->group_id,
                    'form_id' => $form->id,
                    'name' => trim($name),
                ]);
            }
        });

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

        if (SurveySession::query()->where('group_id', $groupId)->exists()) {
            return back()->with('error', 'Kompetitor tidak dapat dihapus karena survei pada group ini sudah dimulai.');
        }

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

        if (SurveySession::query()->where('group_id', $form->group_id)->exists()) {
            return back()->with('error', 'Kompetitor tidak dapat diubah karena survei pada group ini sudah dimulai.');
        }

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
