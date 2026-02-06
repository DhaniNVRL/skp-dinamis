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
    public function index()
    {
        $group = Group::findOrFail($id);
        $forms = Form::where('id_groups', $id)->get();
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
            'id_groups' => 'required|integer|exists:groups,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        foreach ($validated['name'] as $name){
            Form::create([
                'id_groups' => $validated['id_groups'],
                'name' => $name,
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
    public function edit(CatagoriesQuestion $catagoriesQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CatagoriesQuestion $catagoriesQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatagoriesQuestion $catagoriesQuestion)
    {
        //
    }
}
