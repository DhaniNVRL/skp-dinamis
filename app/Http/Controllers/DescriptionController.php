<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Description;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescriptionController extends Controller
{
    /**
     * Store description.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
            ],

            'form_id' => [
                'required',
                'exists:forms,id',
            ],

            'content' => [
                'required',
                'string',
            ],
        ]);

        $form = Form::where('id', $validated['form_id'])
            ->where('group_id', $validated['group_id'])
            ->firstOrFail();

        $descriptionExists = Description::where(
            'form_id',
            $form->id
        )->exists();

        if ($descriptionExists) {
            return redirect()
                ->route('admin.units', [
                    'id' => $form->group_id,
                    'tab' => 'question',
                ])
                ->with(
                    'error',
                    'Form ini sudah memiliki description.'
                );
        }

        Description::create([
            'group_id' => $form->group_id,
            'form_id' => $form->id,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $form->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Description berhasil ditambahkan.'
            );
    }

    /**
     * Update description.
     */
    public function update(Request $request, $id)
    {
        $description = Description::findOrFail($id);

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
            ],
        ]);

        $description->update([
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $description->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Description berhasil diperbarui.'
            );
    }

    /**
     * Delete description.
     */
    public function destroy($id)
    {
        $description = Description::findOrFail($id);

        $groupId = $description->group_id;

        $description->delete();

        return redirect()
            ->route('admin.units', [
                'id' => $groupId,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Description berhasil dihapus.'
            );
    }
}