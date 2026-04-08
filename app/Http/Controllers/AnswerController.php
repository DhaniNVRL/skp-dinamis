<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answers.*' => 'required|string',
            'values.*' => 'nullable|numeric',
        ]);

        $questionId = $request->question_id;
        $formId = $request->form_id;
        $groupId = $request->group_id;

        $answers = $request->input('answers', []);
        $values = $request->input('values', []);

        foreach ($answers as $index => $answerText) {
            Answer::create([
                'name' => $answerText,
                'no' => $index + 1,
                'id_questiontypes' => $questionId, // atau sesuai kolom relasi question_type
            ]);
        }

        return redirect()->back()->with('success', 'Answers added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Answer $answer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Answer $answer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Answer $answer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Answer $answer)
    {
        //
    }
}
