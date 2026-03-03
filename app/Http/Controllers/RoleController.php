<?php

namespace App\Http\Controllers;
use App\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
     {
        $roles = Role::all();
        // return response()->json($roles);

        return view('/admin/masterdata/role', compact('roles'));
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
            'name.*' => 'required|string|max:255',
        ]);

        $nameList = $request->input('name');

        for ($i = 0; $i < count($nameList); $i++) {
            Role::create([
                'name' => $nameList[$i],
            ]);
        }
        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    public function export(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'name');

        // Kosongkan baris kedua untuk input user nanti
        $sheet->setCellValue('A2', '');

        // Download file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_roles.xlsx';

        // Buat file untuk download langsung
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request){
        $request ->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file =$request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row){
            if($index === 0 ) continue;

            $name = $row[0] ?? null;

            if(!empty($name)){
                Role::create([
                    'name' => $name,
                ]);
            }
        }
        return redirect()->back()->with('success', 'Data berhasil diimport dari Excel!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('admin.edit.editrole', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role ->update($validated);

        return redirect()->route('admin.roles')->with('success', 'data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role= Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('successdelete', 'Data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        Role::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

}
