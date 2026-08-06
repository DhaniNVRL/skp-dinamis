<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'question_id' => [
                'required',
                'exists:questions,id',
            ],

            'answer_text' => [
                'required',
                'array',
                'min:1',
            ],

            'answer_text.*' => [
                'required',
                'string',
                'max:255',
            ],

            'answer_text2' => [
                'nullable',
                'array',
                'size:'.count((array) $request->input('answer_text', [])),
            ],

            'answer_text2.*' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no' => [
                'required',
                'array',
                'size:'.count((array) $request->input('answer_text', [])),
            ],

            'no.*' => [
                'required',
                'integer',
                'min:1',
            ],

            'has_child' => [
                'required',
                'array',
                'size:'.count((array) $request->input('answer_text', [])),
            ],

            'has_child.*' => [
                'required',
                'in:0,1',
            ],
        ]);


        $question = Question::findOrFail(
            $validated['question_id']
        );


        DB::transaction(function () use ($validated) {

            foreach (
                $validated['answer_text'] as $index => $text
            ) {
                $hasChild = (int) (
                    $validated['has_child'][$index] ?? 0
                );

                Option::create([
                    'question_id' =>
                        $validated['question_id'],

                    'answer_text' =>
                        $text,

                    'answer_text2' =>
                        $hasChild === 1
                            ? ($validated['answer_text2'][$index] ?? null)
                            : null,

                    'has_child' =>
                        $hasChild,

                    'no' =>
                        $validated['no'][$index],
                ]);
            }

        });


        return redirect()
            ->route('admin.units', [
                'id' => $question->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Option berhasil ditambahkan.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Option $option)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no' => [
                'required',
                'integer',
                'min:1',
            ],

            'answer_text' => [
                'required',
                'string',
                'max:255',
            ],

            'answer_text2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'has_child' => [
                'required',
                'boolean',
            ],
        ]);

        $option = Option::with('question')
            ->findOrFail($id);

        $hasChild = (bool) $validated['has_child'];

        $option->update([
            'no' => $validated['no'],

            'answer_text' =>
                $validated['answer_text'],

            'answer_text2' =>
                $hasChild
                    ? ($validated['answer_text2'] ?? null)
                    : null,

            'has_child' =>
                $hasChild,
        ]);

        return redirect()
            ->route('admin.units', [
                'id' => $option->question->group_id,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Option berhasil diperbarui.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $option = Option::with('question')
            ->findOrFail($id);

        $groupId = $option->question->group_id;

        $option->delete();

        return redirect()
            ->route('admin.units', [
                'id' => $groupId,
                'tab' => 'question',
            ])
            ->with(
                'success',
                'Option berhasil dihapus.'
            );
    }
}
