<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompleteProfile;

class CompleteProfileController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_activities'
                => 'required|exists:activities,id',
            'pertanyaan_group'
                => 'required|array',
            'pertanyaan_group.*'
                => 'required|string|max:255',
            'pertanyaan_unit'
                => 'required|array',
            'pertanyaan_unit.*'
                => 'required|string|max:255',
        ]);

        CompleteProfile::query()->updateOrCreate(
            ['activity_id' => $validated['id_activities']],
            [
                'group_question' => $validated['pertanyaan_group'][0],
                'unit_question' => $validated['pertanyaan_unit'][0],
            ]
        );

        return back()
            ->with(
                'success',
                'Complete Profile berhasil disimpan'
            );
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([

            'pgroup' => 'required|string|max:255',

            'punit' => 'required|string|max:255',

        ]);


        $completeProfile = CompleteProfile::findOrFail($id);

        $completeProfile->update([

            'group_question' => $validated['pgroup'],

            'unit_question' => $validated['punit'],

        ]);


        return back()->with(
            'success',
            'Pertanyaan berhasil diperbarui.'
        );
    }

    public function destroy($id)
    {
        $cprofile = CompleteProfile::findOrFail($id);

        $cprofile->delete();

        return back()->with(
            'success',
            'Pertanyaan berhasil diperbarui.'
        );
    }

}
