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
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                'questions.options' => function ($query) {
                    $query->orderBy('no', 'asc');
                }
            ])
            ->orderBy('no_urut', 'asc')
            ->get();

        $formTypes = FormType::all();

        $questionTypes = QuestionType::all();

        $competitors = Competitor::where('group_id', $id)->get();

        return view('admin.units.index', [
            'groups' => $group,
            'units' => $units,
            'forms' => $forms,
            'formTypes' => $formTypes,
            'questionTypes' => $questionTypes,
            'competitors' => $competitors,
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

        foreach ($validated['name'] as $name) {

            Unit::create([
                'group_id' => $validated['group_id'],
                'name' => $name,
            ]);

        }

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
        $unit->delete();
        return redirect()
            ->back()
            ->with('successdelete', 'Unit berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()
                ->back()
                ->with('error', 'Tidak ada unit yang dipilih.');
        }

        Unit::whereIn('id', $ids)->delete();

        return redirect()
            ->back()
            ->with('successdelete', count($ids) . ' unit berhasil dihapus.');
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
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        foreach ($rows as $index => $row) {
            // Lewati header
            if ($index === 0) {
                continue;
            }
            $name = trim($row[0] ?? '');
            if ($name === '') {
                continue;
            }
            Unit::create([
                'group_id' => $request->group_id,
                'name'     => $name,
            ]);
        }
        return back()->with('success', 'Unit berhasil diimport!');
    }

    public function getUnits($groupID)
    {
        // sesuaikan nama foreign key di table units
        $units = Unit::where('group_id', $groupID)->get();
        return response()->json($units);
    }
}
