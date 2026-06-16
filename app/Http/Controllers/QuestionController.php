<?php

namespace App\Http\Controllers;


use App\Models\Form;
use App\Models\Question;
use App\Models\Group;
use App\Models\QuestionType;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function masterdata()
    {
        $questions = Question::all();

        return view('/admin/masterdata/question', compact('questions'));
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
        // dd($request->all());
        $validated = $request->validate([
            'id_groups' => 'required|integer|exists:groups,id',
            'form_id' => 'required|integer|exists:forms,id',
            'no_header' => 'required|array',
            'no_header.*' => 'required|string|max:255',
            'no' => 'required|array',
            'no.*' => 'required|string|max:255',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'formtype' => 'nullable|array',
            'formtype.*' => 'nullable|exists:question_types,id',
        ]);

        foreach ($validated['name'] as $index => $name) {
            Question::create([
                'id_groups' => $validated['id_groups'],
                'form_id' => $validated['form_id'],
                'no_header' => $validated['no_header'][$index],
                'no' => $validated['no'][$index],
                'name' => $name,
                'id_questiontypes' => $validated['formtype'][$index] ?? null,
            ]);
        }

        return redirect()->route('admin.units', $validated['id_groups'])
                        ->with('success', 'Questions berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $question = Question::findOrFail($id);

        // Ambil group untuk kembali ke halaman unit/group
        $group = Group::findOrFail($question->id_groups);

        $questionypes = QuestionType::all();

        return view('admin.edit.editquestion', compact('question', 'questionypes', 'group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'no' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'formtype' => 'required|exists:question_types,id',
        ]);

        $question->update([
            'no' => $request->no,
            'name' => $request->name,
            'id_questiontypes' => $request->formtype,
        ]);

        // Redirect kembali ke halaman group/unit
        return redirect()->to(route('admin.units', $question->id_groups) . '?tab=questions')
                 ->with('success', 'Question berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // dd($id);
        $question = Question::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Question berhasil dihapus!');
    }

    public function copy($id)
    {
        $question = Question::findOrFail($id);

        // bikin nomor baru (opsional biar gak bentrok)
        $newNo = $question->no . '_copy';

        Question::create([
            'id_groups' => $question->id_groups,
            'form_id' => $question->form_id,
            'no' => $newNo,
            'name' => $question->name . ' (Copy)',
            'id_questiontypes' => $question->id_questiontypes,
        ]);

        return back()->with('success', 'Question berhasil dicopy!');
    }
}
