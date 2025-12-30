<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Group;
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

        return view ('admin.unit', [
            'groups' => $group,
            'units' => $unit
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

    public function import(Request $request, $id)
{
    // pastikan group ada
    Group::findOrFail($id);

    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $rows = IOFactory::load(
        $request->file('file')->getRealPath()
    )->getActiveSheet()->toArray();

    foreach ($rows as $index => $row) {
        if ($index === 0) continue;

        $name = trim($row[0] ?? '');

        if ($name !== '') {
            Unit::create([
                'name' => $name,
                'id_groups' => $id // ← KUNCI UTAMA
            ]);
        }
    }

    return back()->with('success', 'Unit berhasil diimport!');
}
}
