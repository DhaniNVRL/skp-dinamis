<?php

namespace App\Http\Controllers;

use App\Models\FormType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;


class FormTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formtypes = FormType::all();
        return view('/admin/formtype', compact('formtypes'));
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

        $request -> validate([
            'name.*' => 'required|string|max:255',
            'description.*' => 'required|string',
        ]);

        $nameList = $request->input('name');
        $descriptionList = $request->input('description');

        try {
            DB::transaction(function () use ($nameList, $descriptionList): void {
            for ($i = 0; $i < count($nameList); $i++) {
                FormType::create([
                    'name' => $nameList[$i],
                    'description' => $descriptionList[$i],
                ]);
            }
            });
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (Throwable $error) {
            report($error);
            return redirect()->back()->with('error', 'Tipe form gagal disimpan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FormType $formType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormType $formType, $id)
    {
        $formtypes = FormType::findOrFail($id);
        return view('admin.edit.editformtype', compact('formtypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormType $formType, $id)
    {
        $formtypes = FormType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $formtypes->update($validated);

        return redirect()->route('admin.formtype')->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormType $formType, $id)
    {
        $formtypes = FormType::findOrFail($id);

        if ($formtypes->forms()->exists()) {
            return back()->with('error', 'Tipe form masih digunakan dan tidak dapat dihapus.');
        }

        $formtypes->delete();

        return redirect()->back()->with('successdelete', 'data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct', 'exists:form_types,id'],
        ]);

        if (FormType::query()->whereIn('id', $validated['selected'])->whereHas('forms')->exists()) {
            return back()->with('error', 'Sebagian tipe form masih digunakan dan tidak dapat dihapus.');
        }

        FormType::whereIn('id', $validated['selected'])->delete();
        return redirect()->back()->with('successdelete', 'Data terpilih berhasil dihapus.');
    }

    public function export(Request $request){
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
        $filename = 'template_import_formtypes.xlsx';

        // Buat file untuk download langsung
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request){
         $request -> validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $data = [];

            foreach ($rows as $index => $row) {
                if ($index === 0 || trim((string) ($row[0] ?? '')) === '') {
                    continue;
                }

                $item = [
                    'name' => trim((string) $row[0]),
                    'description' => trim((string) ($row[1] ?? '')),
                ];
                if (Validator::make($item, [
                    'name' => ['required', 'string', 'max:255'],
                    'description' => ['required', 'string'],
                ])->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Tipe form pada baris '.($index + 1).' tidak valid.',
                    ]);
                }
                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada tipe form untuk diimport.']);
            }

            DB::transaction(fn () => collect($data)->each(fn ($item) => FormType::create($item)));
            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($data).' tipe form berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import tipe form gagal. Periksa kembali format file.');
        }
    }
}
