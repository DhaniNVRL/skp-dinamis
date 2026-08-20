<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Group;
use App\Models\Form;
use App\Models\FormType;
use App\Models\QuestionType;
use App\Models\Question;
use App\Models\Competitor;
use App\Models\Description;
use App\Models\SurveyBranchRule;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class UnitController extends Controller
{

    public function index($id)
    {
        $group = Group::with('activity')->findOrFail($id);

        $units = Unit::where('group_id', $id)->get();

        $forms = Form::where('group_id', $id)
            ->with([
                'formtype',
                'description',
                'questions.questiontype',
                'questions.options' => function ($query) {
                    $query->orderBy('no', 'asc');
                }
            ])
            ->orderBy('no_urut', 'asc')
            ->get();

        $formTypes = FormType::all();

        $questionTypes = QuestionType::all();

        $competitors = Competitor::where('group_id', $id)->get();
        $branchRules = Schema::hasTable('survey_branch_rules')
            ? SurveyBranchRule::query()
                ->where('group_id', $id)
                ->with(['parentQuestion.options', 'affirmativeOption', 'dependentQuestions', 'skippedQuestions', 'skippedForms', 'skipForm'])
                ->get()
            : collect();

        return view('admin.units.index', [
            'groups' => $group,
            'units' => $units,
            'forms' => $forms,
            'formTypes' => $formTypes,
            'questionTypes' => $questionTypes,
            'competitors' => $competitors,
            'branchRules' => $branchRules,
        ]);
    }

    public function masterdata(){
        $units = Unit::all();
        return view('/admin/masterdata/unit', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['name'] as $name) {
                Unit::create([
                    'group_id' => $validated['group_id'],
                    'name' => trim($name),
                ]);
            }
        });

        return redirect()
            ->route('admin.units', $validated['group_id'])
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama Unit wajib diisi.',
            'name.string' => 'Nama Unit harus berupa teks.',
            'name.max' => 'Nama Unit maksimal 255 karakter.',
        ]);

        $unit->update([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('admin.units', ['id' => $unit->group_id])
            ->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        if ($this->hasDependencies([$unit->id])) {
            return back()->with('error', 'Unit tidak dapat dihapus karena masih digunakan.');
        }

        $unit->delete();
        return redirect()
            ->back()
            ->with('successdelete', 'Unit berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:units,id'],
        ]);

        if ($this->hasDependencies($validated['ids'])) {
            return back()->with('error', 'Sebagian unit masih digunakan dan tidak dapat dihapus.');
        }

        Unit::whereIn('id', $validated['ids'])->delete();

        return redirect()
            ->back()
            ->with('successdelete', count($validated['ids']) . ' unit berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'name');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_unit.xlsx';
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        try {
            $spreadsheet = IOFactory::load($validated['file']->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $names = [];

            foreach ($rows as $index => $row) {
                if ($index === 0 || trim((string) ($row[0] ?? '')) === '') {
                    continue;
                }

                $name = trim((string) $row[0]);
                $validator = Validator::make(['name' => $name], [
                    'name' => ['required', 'string', 'max:255'],
                ]);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'file' => 'Unit pada baris '.($index + 1).' tidak valid.',
                    ]);
                }

                $names[mb_strtolower($name)] = $name;
            }

            if ($names === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada unit untuk diimport.']);
            }

            DB::transaction(function () use ($names, $validated): void {
                foreach ($names as $name) {
                    Unit::firstOrCreate([
                        'group_id' => $validated['group_id'],
                        'name' => $name,
                    ]);
                }
            });

            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($names).' unit berhasil diproses.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import unit gagal. Periksa kembali format file.');
        }
    }

    public function getUnits($groupID)
    {
        // sesuaikan nama foreign key di table units
        $units = Unit::where('group_id', $groupID)->get();
        return response()->json($units);
    }

    private function hasDependencies(array $ids): bool
    {
        return DB::table('subunits')->whereIn('unit_id', $ids)->exists()
            || DB::table('user_profiles')->whereIn('unit_id', $ids)->exists()
            || DB::table('survey_sessions')->whereIn('unit_id', $ids)->exists();
    }
}
