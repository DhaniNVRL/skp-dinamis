<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Group;
use App\Models\Form;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id){
        $group = Group::findOrFail($id);
        $unit = Unit::where('id_groups', $id)->get();
        $form = Form::where('id_groups', $id)->get();

        return view ('admin.unit', [
            'groups' => $group,
            'units' => $unit,
            'forms' => $form,
        ]);
    }

    public function store(Request $request){
        $validated =$request->validate([
            'id_groups' => 'required|integer|exists:groups,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        foreach ($validated['name'] as $name){
            Unit::create([
                'id_groups' => $validated['id_groups'],
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.units', $validated['id_groups'])
                        ->with('success', 'Units berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id){
        $unit = Unit::findOrFail($id);
        $group = Group::findOrFail($unit->id_groups); // ambil group terkait unit
        return view('admin.edit.editunit', compact('unit', 'group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id){
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.units', ['id' => $unit->id_groups])
            ->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('successdelete', 'Group berhasil dihapus.');
    }

    public function bulkDelete(Request $request){
        $ids = $request->input('selected', []);
        if(count($ids)>0){
            Unit::whereIn('id', $ids)->delete();
            return redirect()->back()->with('successdelete', 'Group terpilih berhasil di hapus');
        }
    }

    public function export(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('A2', '');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_unit.xlsx';

        return response()->streamDownload(function () use ($writer){
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'id_groups' => 'required|exists:groups,id',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            $name = $row[0] ?? null;

            if (!empty($name)) {
                Unit::create([
                    'id_groups' => $request->id_groups,
                    'name' => $name,
                ]);
            }
        }

        return back()->with('success', 'Unit berhasil diimport!');
    }
}
