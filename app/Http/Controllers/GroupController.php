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

    public function store(Request $request){
        $validated = $request->validate([
            'id_activities' => 'required|integer|exists:activities,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        foreach ($validated['name'] as $name) {
            Group::create([
                'id_activities' => $validated['id_activities'],
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.groups', $validated['id_activities'])
                        ->with('success', 'Groups berhasil ditambahkan!');
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

    public function import(Request $request){
        $request -> validate([
            'file'=> 'required|mimes::xlsx,xls'
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach($row as $index =>$row){
            if ($index === 0) continue;

            $name = $row[0] ?? null;

            if(!empty($name)){
                Group::create([
                    'name' => $name
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil diimport dari Excel!');
    }
}
