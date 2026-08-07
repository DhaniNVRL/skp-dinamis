<?php

namespace App\Http\Controllers;
use App\Models\Role;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class RoleController extends Controller
{
    private const SYSTEM_ROLES = ['admin', 'pm', 'surveyor', 'monitoring', 'user'];

    /**
     * Display a listing of the resource.
     */
    public function index()
     {
        $roles = Role::all();
        // return response()->json($roles);

        return view('/admin/masterdata/role', compact('roles'));
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
            'name.*' => ['required', 'string', 'max:191', 'distinct', 'unique:roles,name'],
        ]);

        $nameList = $request->input('name');

        DB::transaction(function () use ($nameList): void {
            foreach ($nameList as $name) {
                Role::create(['name' => trim($name)]);
            }
        });
        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    public function export(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'name');

        // Kosongkan baris kedua untuk input user nanti
        $sheet->setCellValue('A2', '');

        // Download file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_roles.xlsx';

        // Buat file untuk download langsung
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request){
        $request ->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray();
            $names = [];

            foreach ($rows as $index => $row) {
                if ($index === 0 || trim((string) ($row[0] ?? '')) === '') {
                    continue;
                }

                $name = trim((string) $row[0]);
                $validator = Validator::make(['name' => $name], [
                    'name' => ['required', 'string', 'max:191', 'unique:roles,name'],
                ]);

                if ($validator->fails() || isset($names[strtolower($name)])) {
                    throw ValidationException::withMessages([
                        'file' => 'Role pada baris '.($index + 1).' tidak valid atau duplikat.',
                    ]);
                }

                $names[strtolower($name)] = $name;
            }

            if ($names === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada role untuk diimport.']);
            }

            DB::transaction(fn () => collect($names)->each(
                fn (string $name) => Role::create(['name' => $name])
            ));
            $spreadsheet->disconnectWorksheets();

            return back()->with('success', count($names).' role berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);

            return back()->with('error', 'Import role gagal. Periksa kembali format file.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('admin.edit.editrole', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('roles', 'name')->ignore($id),
            ],
        ]);

        if ($this->isSystemRole($role)) {
            return back()->with('error', 'Nama role sistem tidak dapat diubah.');
        }

        $role ->update($validated);

        return redirect()->route('admin.roles')->with('success', 'data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role= Role::findOrFail($id);

        if ($this->isSystemRole($role) || $role->users()->exists()) {
            return back()->with('error', 'Role sistem atau role yang masih digunakan tidak dapat dihapus.');
        }

        $role->delete();

        return redirect()->back()->with('successdelete', 'Data berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['required', 'integer', 'distinct', 'exists:roles,id'],
        ]);

        $roles = Role::query()->withCount('users')->whereIn('id', $validated['selected'])->get();

        if ($roles->contains(fn (Role $role) => $this->isSystemRole($role) || $role->users_count > 0)) {
            return back()->with('error', 'Role sistem atau role yang masih digunakan tidak dapat dihapus.');
        }

        Role::whereIn('id', $validated['selected'])->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    private function isSystemRole(Role $role): bool
    {
        return in_array(strtolower(trim($role->name)), self::SYSTEM_ROLES, true);
    }

}
