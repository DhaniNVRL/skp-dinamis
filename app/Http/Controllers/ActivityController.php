<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;


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
            DB::transaction(function () use ($nameList, $descriptionList): void {
            for ($i = 0; $i < count($nameList); $i++) {
                Activity::create([
                    'name' => $nameList[$i],
                    'description' => $descriptionList[$i],
                ]);
            }
            });
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
         } catch (Throwable $error) {
            report($error);
            return redirect()->back()->with('error', 'Data activity gagal disimpan.');
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
                $validator = Validator::make($item, [
                    'name' => ['required', 'string', 'max:255'],
                    'description' => ['required', 'string'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Activity pada baris '.($index + 1).' tidak valid.',
                    ]);
                }

                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada activity untuk diimport.']);
            }

            DB::transaction(fn () => collect($data)->each(fn ($item) => Activity::create($item)));
            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($data).' activity berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import activity gagal. Periksa kembali format file.');
        }
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        if ($this->hasDependencies([$activity->id])) {
            return back()->with('error', 'Activity tidak dapat dihapus karena masih digunakan.');
        }

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
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:activities,id'],
        ]);

        if ($this->hasDependencies($validated['ids'])) {
            return back()->with('error', 'Sebagian activity masih digunakan dan tidak dapat dihapus.');
        }

        Activity::whereIn('id', $validated['ids'])->delete();

        return back()->with('successdelete', 'Data terpilih berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $activity->update($validated);

        return redirect()
            ->route('admin.activity')
            ->with('success', 'Data berhasil diperbarui.');
    }

    private function hasDependencies(array $ids): bool
    {
        return DB::table('groups')->whereIn('activity_id', $ids)->exists()
            || DB::table('user_profiles')->whereIn('activity_id', $ids)->exists()
            || DB::table('survey_sessions')->whereIn('activity_id', $ids)->exists()
            || DB::table('complete_profiles')->whereIn('activity_id', $ids)->exists();
    }
}
