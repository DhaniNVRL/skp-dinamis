<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Unit;
use App\Models\Activity;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $roles = Role::all();
        $activities = Activity::all();
        $users = User::all();
        $userprofiles = UserProfile::with(['user.role', 'activity', 'group', 'unit'])->get();
        return view('/admin/datauser' , compact('users', 'roles', 'activities', 'userprofiles'));
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'username');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'id_roles');
        $sheet->setCellValue('E1', 'id_activity');

        // Kosongkan baris kedua untuk input user nanti
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        $sheet->setCellValue('C2', '');
        $sheet->setCellValue('D2', '');
        $sheet->setCellValue('£2', '');

        // Tambahkan keterangan roles
        $sheet->setCellValue('G1', 'Keterangan id_roles:');
        $sheet->setCellValue('G2', '1 = admin');
        $sheet->setCellValue('G3', '2 = PM');
        $sheet->setCellValue('G4', '3 = surveyor');
        $sheet->setCellValue('G5', '4 = user');

        // Download file
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_user.xlsx';

        // Buat file untuk download langsung
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        
        $request->validate([
            'username' => 'required|array|min:1',
            'username.*' => 'required|string|max:255',

            'email' => 'required|array|min:1',
            'email.*' => 'required|email|max:255|distinct|unique:users,email',

            'password' => 'required|array|min:1',
            'password.*' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],

            'activity' => 'required|array|min:1',
            'activity.*' => 'required|exists:activities,id',

            'role' => 'required|array|min:1',
            'role.*' => 'required|exists:roles,id',
        ]);

        // dd($request->all());
        $usernameList = $request->username;
        $emailList = $request->email;
        $passwordList = $request->password;
        $activityList = $request->activity;
        $roleList = $request->role;

        DB::transaction(function () use (
            $usernameList,
            $emailList,
            $passwordList,
            $activityList,
            $roleList,
        ) {

            for ($i = 0; $i < count($usernameList); $i++) {

                $user = User::create([
                    'username' => $usernameList[$i],
                    'email' => $emailList[$i], // kalau memang ada di tabel users
                    'password' => Hash::make($passwordList[$i]),
                    'id_roles' => $roleList[$i],
                ]);

                UserProfile::create([
                    'user_id' => $user->id,
                    'activity_id' => $activityList[$i],
                    'group_id' => null,
                    'unit_id' => null,
                    'fullname' => null,
                    'no_handphone' => null,
                    'email' => $emailList[$i],
                ]);
            }
        });

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
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
    public function edit($id)
    {
        // Ambil user + relasi lengkap
        $user = User::with([
            'role',
            'profile.activity',
            'profile.group',
            'profile.unit'
        ])->findOrFail($id);

        // Ambil semua data untuk dropdown
        $roles = Role::all();
        $activities = Activity::all();
        $groups = Group::all();
        $units = Unit::all();

        return view('admin.edit.edituser', compact(
            'user',
            'roles',
            'activities',
            'groups',
            'units'
        ));
    }

    public function edit_password($id){
        $user = User::with([
            'role',
            'profile.activity',
            'profile.group',
            'profile.unit'
        ])->findOrFail($id);

        return view('admin.edit.editpassword', compact(
            'user'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'username'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'fullname'      => 'required|string|max:255',
            'no_handphone'  => 'required|string|max:20',
            'activity'      => 'required|exists:activities,id',
            'group'         => 'required|exists:groups,id',
            'unit'          => 'required|exists:units,id',
        ]);

        DB::beginTransaction();

        try {

            // =========================
            // UPDATE TABLE USERS
            // =========================
            $user = User::findOrFail($id);

            $user->update([
                'username' => $request->username,
                'email'    => $request->email,
            ]);

            // =========================
            // UPDATE TABLE USER_PROFILES
            // =========================
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'fullname'      => $request->fullname,
                    'no_handphone'  => $request->no_handphone,
                    'activity_id'   => $request->activity,
                    'group_id'      => $request->group,
                    'unit_id'       => $request->unit,
                ]
            );

            DB::commit();

            return redirect()
                ->route('admin.datauser')
                ->with('success', 'Data user berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update_password(Request $request, $id)
    {
        // dd($request->all());

        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8|max:255',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()
                ->route('admin.datauser')
                ->with('success', 'Data password berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
        $user = User::findOrFail($id);

        // Hapus profile jika ada
        if ($user->profile) {
            $user->profile->delete();
        }

        // Hapus user
        $user->delete();

        return redirect()->back()->with('successdelete', 'Data berhasil dihapus.');
    }
}
