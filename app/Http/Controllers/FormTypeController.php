<?php

namespace App\Http\Controllers;

use App\Models\FormType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class FormTypeController extends Controller
{
    public function index()
    {
        $formTypes = FormType::query()->orderBy('id')->get();

        return view('admin.formtypes.index', compact('formTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:255'],
            'description' => ['required', 'array'],
            'description.*' => ['required', 'string', 'max:1000'],
        ]);

        if (count($validated['name']) !== count($validated['description'])) {
            throw ValidationException::withMessages([
                'description' => 'Jumlah nama dan deskripsi tipe form harus sama.',
            ]);
        }

        DB::transaction(function () use ($validated): void {
            foreach ($validated['name'] as $index => $name) {
                FormType::create([
                    'name' => trim($name),
                    'description' => trim($validated['description'][$index]),
                ]);
            }
        });

        return back()->with('success', 'Tipe form berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $formtypes = FormType::query()->findOrFail($id);

        return view('admin.edit.editformtype', compact('formtypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        FormType::query()->findOrFail($id)->update($validated);

        return redirect()->route('admin.formtype')->with('success', 'Tipe form berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $formType = FormType::query()->findOrFail($id);

        if ($formType->forms()->exists()) {
            return back()->with('error', 'Tipe form masih digunakan dan tidak dapat dihapus.');
        }

        $formType->delete();

        return back()->with('successdelete', 'Tipe form berhasil dihapus.');
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

        FormType::query()->whereIn('id', $validated['selected'])->delete();

        return back()->with('successdelete', count($validated['selected']).' tipe form berhasil dihapus.');
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Form Type');
        $sheet->fromArray([
            ['name', 'description'],
            ['', ''],
        ]);
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            'template_import_form_types.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $spreadsheet = IOFactory::load($validated['file']->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $headers = array_map(
                fn ($value) => strtolower(trim((string) $value)),
                array_slice($rows[0] ?? [], 0, 2)
            );

            if ($headers !== ['name', 'description']) {
                throw ValidationException::withMessages([
                    'file' => 'Judul kolom harus: name, description.',
                ]);
            }

            $data = [];
            foreach (array_slice($rows, 1) as $index => $row) {
                if (trim((string) ($row[0] ?? '')) === '' && trim((string) ($row[1] ?? '')) === '') {
                    continue;
                }

                $item = [
                    'name' => trim((string) ($row[0] ?? '')),
                    'description' => trim((string) ($row[1] ?? '')),
                ];
                $validator = Validator::make($item, [
                    'name' => ['required', 'string', 'max:255'],
                    'description' => ['required', 'string', 'max:1000'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Baris '.($index + 2).' tidak valid: '.$validator->errors()->first(),
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

            return back()->with('error', 'Import tipe form gagal. Periksa kembali format file Excel.');
        }
    }
}
