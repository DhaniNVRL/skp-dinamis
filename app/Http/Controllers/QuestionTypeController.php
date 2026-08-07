<?php

namespace App\Http\Controllers;

use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class QuestionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questtypes = QuestionType::query()->orderBy('id')->get();

        return view('admin.questiontypes.index', compact('questtypes'));
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
            'name' => ['required', 'array', 'min:1'],
            'name.*' => ['required', 'string', 'max:255'],
            'description' => ['required', 'array'],
            'description.*' => ['required', 'string'],
        ]);

        if (count($validated['name']) !== count($validated['description'])) {
            throw ValidationException::withMessages([
                'description' => 'Jumlah nama dan deskripsi tipe pertanyaan harus sama.',
            ]);
        }

        try {
            DB::transaction(function () use ($validated): void {
                foreach ($validated['name'] as $index => $name) {
                    QuestionType::create([
                        'name' => $name,
                        'description' => $validated['description'][$index],
                    ]);
                }
            });

            return back()->with('success', 'Data berhasil disimpan!');
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Tipe pertanyaan gagal disimpan.');
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
            'description' => 'required|string',
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

        if ($questtypes->isTitleOnly()) {
            return back()->with('error', 'Tipe sistem Judul (Tanpa Jawaban) tidak dapat dihapus.');
        }

        if ($questtypes->questions()->exists()) {
            return back()->with('error', 'Tipe pertanyaan masih digunakan dan tidak dapat dihapus.');
        }

        $questtypes->delete();

        return redirect()->back()->with('successdelete', 'data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct', 'exists:question_types,id'],
        ]);

        if (QuestionType::query()->whereIn('id', $validated['selected'])->whereHas('questions')->exists()) {
            return back()->with('error', 'Sebagian tipe pertanyaan masih digunakan dan tidak dapat dihapus.');
        }

        if (QuestionType::query()
            ->whereIn('id', $validated['selected'])
            ->get()
            ->contains(fn (QuestionType $type) => $type->isTitleOnly())) {
            return back()->with('error', 'Tipe sistem Judul (Tanpa Jawaban) tidak dapat dihapus.');
        }

        QuestionType::whereIn('id', $validated['selected'])->delete();

        return back()->with('successdelete', 'Data terpilih berhasil dihapus.');
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

    public function import(Request $request)
    {
        $request->validate([
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
                        'file' => 'Tipe pertanyaan pada baris '.($index + 1).' tidak valid.',
                    ]);
                }

                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages([
                    'file' => 'Tidak ada tipe pertanyaan untuk diimport.',
                ]);
            }

            DB::transaction(fn () => collect($data)->each(fn ($item) => QuestionType::create($item)));
            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($data).' tipe pertanyaan berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import tipe pertanyaan gagal. Periksa kembali format file.');
        }
    }
}
