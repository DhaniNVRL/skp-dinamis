<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::query()
            ->orderBy('name')
            ->get();

        return view('admin.activities.index', compact('activities'));
    }

    public function masterdata(){
        $activities = Activity::all();
        return view('admin.masterdata.activity', compact('activities'));
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string',
        ]);

        $nameList = $request->input('name');
        $descriptionList = $request->input('description');

        try {
            for ($i = 0; $i < count($nameList); $i++) {
                Activity::create([
                    'name' => $nameList[$i],
                    'description' => $descriptionList[$i],
                ]);
            }
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
         } catch (\Exception $e) {
            // Jika terjadi error, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function export(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'description');

        // Kosongkan baris kedua untuk input user nanti
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');

        // Download file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_activities.xlsx';

        // Buat file untuk download langsung
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request){
        $request -> validate([
            'file' =>'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Lewati baris pertama (header)
        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $name = $row[0] ?? null;
            $description = $row[1] ?? null;

            // Simpan hanya jika ada data
            if (!empty($name)) {
                Activity::create([
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil diimport dari Excel!');
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return redirect()
            ->route('admin.activity')
            ->with('success', 'Data berhasil dihapus.');
    }

    public function edit($id)
    {
        return response()->json(
            Activity::query()->findOrFail($id)
        );
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (count($ids) > 0) {

            Activity::whereIn('id', $ids)->delete();

            return redirect()
                ->back()
                ->with('successdelete', 'Data terpilih berhasil dihapus.');
        }

        return redirect()
            ->back()
            ->with('error', 'Tidak ada data yang dipilih.');
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activity->update($validated);

        return redirect()
            ->route('admin.activity')
            ->with('success', 'Data berhasil diperbarui.');
    }
}
