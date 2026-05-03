<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function masterdata(){
        $option = Option::all();
        return view('/admin/masterdata/option', compact('option'));
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
        $validated = $request->validate([
            'question_id'     => 'required',
            'answer_text'     => 'required|array',
            'answer_text.*'   => 'required|string',
            'no'              => 'required|array',
            'no.*'            => 'required|numeric',
            'has_child'       => 'required|array',
            'has_child.*'     => 'nullable|in:0,1',
        ]);

        foreach ($validated['answer_text'] as $index => $text) {

            \App\Models\Option::create([
                'question_id' => $validated['question_id'],
                'answer_text' => $text,
                'has_child'   => $validated['has_child'][$index] ?? 0,
                'no'          => $validated['no'][$index] ?? ($index + 1),
            ]);
        }

        $question = \App\Models\Question::findOrFail($validated['question_id']);

        return redirect()->to(
            route('admin.units', $question->id_groups) . '?tab=questions'
        )->with('success', 'Option berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Option $option)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $option = Option::findOrFail($id);

        return view('admin.edit.editoption', compact('option'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'answer_text' => 'required|string|max:255',
            'has_child'   => 'nullable|boolean',
        ]);

        $option = Option::findOrFail($id);

        $option->update([
            'answer_text' => $request->answer_text,
            'has_child'   => $request->has_child ?? 0,
        ]);

        return redirect()->to(
            route('admin.units', $option->question->id_groups) . '?tab=questions'
        )->with('success', 'Option berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $option = Option::findOrFail($id);
        $option->delete();

        return redirect()->back()->with('success', 'Option berhasil dihapus');
    }
}
