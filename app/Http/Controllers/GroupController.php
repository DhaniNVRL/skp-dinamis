<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GroupController extends Controller
{
    public function index($id){
        $activity = Activity::findOrFail($id);
        $groups = Group::where('id_activities', $id)->get();

        return view('admin.group', [
            'activity' => $activity,
            'groups' => $groups
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'id_activities' => 'required|exists:activities,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        foreach ($request->name as $name) {
            Group::create([
                'id_activities' => $request->id_activities,
                'name' => $name,
            ]);
        }

        return back()->with('success', 'Group berhasil ditambahkan');
    }



    public function edit($id){
        $group = Group::findOrFail($id);
        return view('admin.edit.editgroup', compact('group'));
    }

    public function update(Request $request, $id){
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($validated);

        return redirect()->route('admin.groups', ['id' => $group->id_activities])
            ->with('success', 'Group berhasil diperbarui.');

    }

    public function destroy($id){
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->back()->with('successdelete', 'Group berhasil dihapus.');
    }

    public function bulkDelete(Request $request){
        $ids = $request->input('selected',[]);
        if (count($ids)>0){
            Group::whereIn('id', $ids)->delete();
            return redirect()->back()->with('successdelete', 'Group terpilih berhasil di hapus');
        }

        return redirect()->back()->with('error', 'Tidak ada group yang dipilih.');
    }

    public function export(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('A2', '');

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_groups.xlsx';

        return response()->streamDownload(function () use ($writer){
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'id_activities' => 'required|exists:activities,id',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            $name = $row[0] ?? null;

            if (!empty($name)) {
                Group::create([
                    'id_activities' => $request->id_activities,
                    'name' => $name,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil diimport dari Excel!');
    }

}
