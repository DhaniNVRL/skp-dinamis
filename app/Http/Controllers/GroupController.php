<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Activity;
use App\Models\CompleteProfile;
use App\Models\Unit;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GroupController extends Controller
{
    // public function index($id){
    //     $activity = Activity::findOrFail($id);
    //     $groups = Group::where('id_activities', $id)->get();

    //     return view('admin.group', [
    //         'activity' => $activity,
    //         'groups' => $groups,
    
    //     ]);
    // }
    public function index($id){
        $activity = Activity::findOrFail($id);
        $cprofiles = CompleteProfile::where('activity_id', $id)->get();
        $groups = Group::where('activity_id', $id)->get();

        return view('/admin/groups/index', [
            'activity' => $activity,
            'groups' => $groups,
            'cprofiles' => $cprofiles,
        ]);
    }

    public function masterdata(){
        $groups = Group::all();
        return view('/admin/masterdata/group', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_activities' => 'required|exists:activities,id',
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        foreach ($request->name as $name) {
            Group::create([
                'activity_id' => $request->id_activities,
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

        return redirect()
                ->route('admin.groups', [
                    'id' => $group->activity_id
                ])
                ->with('success', 'Group berhasil diperbarui.');

    }

    public function destroy($id){
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->back()->with('successdelete', 'Group berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (count($ids) > 0) {

            Group::whereIn('id', $ids)->delete();

            return redirect()
                ->back()
                ->with('successdelete', 'Group terpilih berhasil dihapus.');
        }

        return redirect()
            ->back()
            ->with('error', 'Tidak ada group yang dipilih.');
    }


    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Input Group');

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue('A1', 'name');

        /*
        |--------------------------------------------------------------------------
        | CONTOH DATA
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A2',
            'Contoh Nama Group'
        );

        /*
        |--------------------------------------------------------------------------
        | STYLE
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A1')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A1')
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        $sheet->getStyle('A1')
            ->getFill()
            ->setFillType(
                \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
            )
            ->getStartColor()
            ->setARGB('D9EAF7');

        $sheet->getColumnDimension('A')
            ->setWidth(40);

        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        $filename = 'template_import_group.xlsx';

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type'
                    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120',
            ],

            'id_activities' => [
                'required',
                'exists:activities,id',
            ],
        ]);

        try {
            $file = $request->file('file');

            $spreadsheet = IOFactory::load(
                $file->getRealPath()
            );

            $sheet = $spreadsheet->getActiveSheet();

            $rows = $sheet->toArray();

            $inserted = 0;
            $skipped = 0;

            foreach ($rows as $index => $row) {
                /*
                | Baris pertama adalah header.
                */
                if ($index === 0) {
                    continue;
                }

                $name = trim(
                    (string) ($row[0] ?? '')
                );

                /*
                | Lewati baris kosong.
                */
                if ($name === '') {
                    $skipped++;

                    continue;
                }

                /*
                | Hindari duplikasi Group dalam Activity yang sama.
                */
                $exists = Group::query()
                    ->where(
                        'activity_id',
                        $validated['id_activities']
                    )
                    ->whereRaw(
                        'LOWER(name) = ?',
                        [mb_strtolower($name)]
                    )
                    ->exists();

                if ($exists) {
                    $skipped++;

                    continue;
                }

                Group::create([
                    'activity_id'
                        => $validated['id_activities'],

                    'name'
                        => $name,
                ]);

                $inserted++;
            }

            if ($inserted === 0) {
                return back()->with(
                    'error',
                    'Tidak ada Group baru yang berhasil diimport. Periksa isi file atau data mungkin sudah tersedia.'
                );
            }

            return back()->with(
                'success',
                $inserted
                    . ' Group berhasil diimport. '
                    . $skipped
                    . ' baris dilewati.'
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'File Group gagal diproses. Pastikan menggunakan template yang benar.'
            );
        }
    }

    public function getGroups($activityID)
    {
        $groups = Group::query()
            ->where('activity_id', $activityID)
            ->orderBy('name')
            ->get();

        return response()->json($groups);
    }

}
