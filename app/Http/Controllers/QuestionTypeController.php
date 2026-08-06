<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuestionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questtypes = QuestionType::all();
        return view('/admin/questionstypes', compact('questtypes'));
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
        // dd(QuestionType::all());

        $request -> validate([
            'name.*' => 'required|string|max:255',
            'description.*' => 'required|string',
        ]);

        $nameList = $request->input('name');
        $descriptionList = $request->input('description');

        try {
            for ($i = 0; $i < count($nameList); $i++) {
                QuestionType::create([
                    'name' => $nameList[$i],
                    'description' => $descriptionList[$i],
                ]);
            }
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionType $questionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionType $questionType, $id)
    {
        $questtypes = QuestionType::findOrFail($id);
        return view('admin.edit.editquesttypes', compact('questtypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionType $questionType, $id)
    {
        $questtypes = QuestionType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $questtypes->update($validated);

        return redirect()->route('admin.questtype')->with('success', 'Data berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionType $questionType, $id)
    {
        $questtypes = QuestionType::findOrFail($id);
        $questtypes->delete();

        return redirect()->back()->with('successdelete', 'data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected', []);

        if(count($ids)>0){
            QuestionType::whereIn('id', $ids)->delete();
            return redirect()->back()->with('successdelete', 'Data terpilih berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
    }

    public function export(Request $request)
    {
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
        $filename = 'template_import_Questiontypes.xlsx';

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
                QuestionType::create([
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil diimport dari Excel!');
    }
}
