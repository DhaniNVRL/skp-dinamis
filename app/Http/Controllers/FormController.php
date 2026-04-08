<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {

    }

    public function masterdata()
    {
        $forms = Form::all();

        return view('/admin/masterdata/form', compact('forms'));
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
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'formtype' => 'required|array',
            'formtype.*' => 'required|exists:form_types,id',
        ]);

        foreach ($validated['name'] as $index => $name) {
            Form::create([
                'id_groups' => $validated['id_groups'],
                'name' => $name,
                'id_formtype' => $validated['formtype'][$index],
            ]);
        }

        return redirect()->route('admin.units', $validated['id_groups'])
            ->with('success', 'Form berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(CatagoriesQuestion $catagoriesQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $form = Form::findOrFail($id);
        $formtypes = \App\Models\FormType::all();
        $unitId = $form->id_units; // ID unit untuk redirect

        return view('admin.edit.editform', compact('form', 'formtypes', 'unitId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_formtype' => 'required|exists:form_types,id',
        ]);

        $form->update($validated);

        // Redirect ke halaman unit
        return redirect()->to(route('admin.units', $form->id_groups) . '?tab=questions')
                 ->with('success', 'Form berhasil diperbarui!');
    }

    public function copy($id)
    {
        $form = Form::findOrFail($id);
        $newForm = $form->replicate();
        $newForm->name = $form->name . ' (Copy)';
        $newForm->save();

        return redirect()->to(route('admin.units', $form->id_groups) . '?tab=questions')
                 ->with('success', 'Form berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $unitId = $form->id_groups;
        $form->delete();

        return redirect()->to(route('admin.units', $form->id_groups) . '?tab=questions')
                 ->with('success', 'Form berhasil diperbarui!');
    }
}
