<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubUnitQuestionController extends Controller
{
    public function edit($subunitId)
    {
        $subunit = SubUnit::findOrFail($subunitId);

        $questions = Question::all();

        return view(
            'subunit-question.edit',
            compact('subunit', 'questions')
        );
    }

    public function save(Request $request)
    {
        $subunit = SubUnit::findOrFail(
            $request->subunit_id
        );

        $subunit->questions()->sync(
            $request->questions ?? []
        );

        return redirect()
            ->back()
            ->with(
                'success',
                'Konfigurasi berhasil disimpan'
            );
    }
}
