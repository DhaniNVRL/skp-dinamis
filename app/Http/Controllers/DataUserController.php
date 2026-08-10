<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Unit;
use App\Models\Activity;
use App\Models\Answer;
use App\Models\Form;
use App\Models\UserProfile;
use App\Services\AnswerReviewFormatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class DataUserController extends Controller
{

    public function index(Request $request)
    {
        $query = UserProfile::query()
            ->whereHas('user')
            ->with([
                'user' => fn ($userQuery) => $userQuery
                    ->with(['role', 'surveySession'])
                    ->withCount('answers'),
                'activity',
                'group',
                'unit',
            ]);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%");

                });
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('user.role', function ($role) use ($request) {
                $role->where('id', $request->role);
            });
        }

        if ($request->filled('activity')) {
            $query->where('activity_id', $request->activity);
        }

        if ($request->filled('group')) {
            $query->where('group_id', $request->group);
        }

        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }

        $roles = Role::orderBy('name')->get();
        $activities = Activity::orderBy('name')->get();
        $groups = Group::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $userProfiles = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact(
            'userProfiles',
            'roles',
            'activities',
            'groups',
            'units',
        ));
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');
        $sheet->setCellValue('A1','username');
        $sheet->setCellValue('B1','password');
        $sheet->setCellValue('C1','id_role');
        $sheet->setCellValue('D1','id_activity');
        $master = $spreadsheet->createSheet();
        $master->setTitle('Master Data');
        $master->setCellValue('A1','ROLE');
        $master->setCellValue('A2','ID');
        $master->setCellValue('B2','Nama Role');
        $roles = Role::orderBy('id')->get();
        $row = 3;
        foreach ($roles as $role) {

            $master->setCellValue(
                'A'.$row,
                $role->id
            );
            $master->setCellValue(
                'B'.$row,
                $role->name
            );
            $row++;
        }
        $lastRole = $row - 1;
        $master->setCellValue('D1','ACTIVITY');
        $master->setCellValue('D2','ID');
        $master->setCellValue('E2','Nama Activity');
        $activities = Activity::orderBy('id')->get();
        $row = 3;
        foreach ($activities as $activity) {
            $master->setCellValue(
                'D'.$row,
                $activity->id
            );
            $master->setCellValue(
                'E'.$row,
                $activity->name
            );
            $row++;
        }
        $lastActivity = $row - 1;
        for ($i = 2; $i <= 1000; $i++) {
            $validation = $sheet
                ->getCell('C'.$i)
                ->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowErrorMessage(true);
            $validation->setFormula1(
                "'Master Data'!\$A\$3:\$A\$$lastRole"
            );
        }
        for ($i = 2; $i <= 1000; $i++) {
            $validation = $sheet
                ->getCell('D'.$i)
                ->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowErrorMessage(true);
            $validation->setFormula1(
                "'Master Data'!\$D\$3:\$D\$$lastActivity"
            );
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_user.xlsx';
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);
        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray();
            $data = [];
            $seenUsernames = [];

            foreach ($rows as $index => $row) {
                if ($index == 0) {
                    continue;
                }

                if (empty(array_filter($row))) {
                    continue;
                }

                $item = [
                    'username' => trim((string) ($row[0] ?? '')),
                    'password' => (string) ($row[1] ?? ''),
                    'role_id' => $row[2] ?? null,
                    'activity_id' => $row[3] ?? null,
                ];

                $validator = Validator::make($item, [
                    'username' => ['required', 'string', 'max:191', 'unique:users,username'],
                    'password' => ['required', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
                    'role_id' => ['required', 'integer', 'exists:roles,id'],
                    'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
                ]);

                $normalizedUsername = strtolower($item['username']);

                $activityIsOptional = in_array((int) $item['role_id'], [1, 2], true);

                if (
                    $validator->fails() ||
                    (! $activityIsOptional && empty($item['activity_id'])) ||
                    isset($seenUsernames[$normalizedUsername])
                ) {
                    throw ValidationException::withMessages([
                        'file' => 'Data user pada baris '.($index + 1).' tidak valid atau duplikat.',
                    ]);
                }

                if ($activityIsOptional) {
                    $item['activity_id'] = null;
                }

                $seenUsernames[$normalizedUsername] = true;
                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages([
                    'file' => 'Tidak ada data user yang dapat diimport.',
                ]);
            }

            DB::transaction(function () use ($data): void {
                foreach ($data as $item) {
                    $user = User::create([
                        'username' => $item['username'],
                        'password' => Hash::make($item['password']),
                        'role_id' => $item['role_id'],
                    ]);

                    UserProfile::create([
                        'user_id' => $user->id,
                        'group_id' => null,
                        'unit_id' => null,
                        'fullname' => null,
                        'no_handphone' => null,
                        'email' => 'pending-user-'.$user->id.'@invalid.local',
                        'activity_id' => $item['activity_id'],
                    ]);
                }
            });

            $spreadsheet->disconnectWorksheets();

            return redirect()
                ->back()
                ->with('success', count($data).' user berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            report($error);

            return redirect()
                ->back()
                ->with('error', 'Import user gagal. Periksa kembali format file.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|array|min:1',
            'username.*' => ['required', 'string', 'max:191', 'distinct', 'unique:users,username'],
            'password' => 'required|array|min:1',
            'password.*' => ['required', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
            'activity_id' => 'nullable|array',
            'activity_id.*' => 'nullable|exists:activities,id',
            'role_id' => 'required|array|min:1',
            'role_id.*' => 'required|exists:roles,id',
        ]);

        $rowCount = count($request->username);
        if (count($request->password) !== $rowCount
            || count($request->role_id) !== $rowCount) {
            throw ValidationException::withMessages([
                'username' => 'Jumlah username, password, dan role harus sama.',
            ]);
        }

        $usernameList = $request->username;
        $passwordList = $request->password;
        $activityList = array_pad(
            $request->input('activity_id', []),
            $rowCount,
            null
        );
        $roleList = $request->role_id;

        foreach ($roleList as $index => $roleId) {
            if (
                ! in_array((int) $roleId, [1, 2], true) &&
                empty($activityList[$index])
            ) {
                throw ValidationException::withMessages([
                    "activity_id.$index" => 'Activity wajib dipilih untuk role ini.',
                ]);
            }
        }
        DB::transaction(function () use (
            $usernameList,
            $passwordList,
            $activityList,
            $roleList
        ) {
            for ($i = 0; $i < count($usernameList); $i++) {
                $user = User::create([
                    'username' => $usernameList[$i],
                    // 'email' => $emailList[$i], // kalau memang ada di tabel users
                    'password' => Hash::make($passwordList[$i]),
                    'role_id' => $roleList[$i],
                ]);
                UserProfile::create([
                    'user_id' => $user->id,
                    'activity_id' => in_array((int) $roleList[$i], [1, 2], true)
                        ? null
                        : $activityList[$i],
                    'group_id' => null,
                    'unit_id' => null,
                    'fullname' => null,
                    'no_handphone' => null,
                    'email' => 'pending-user-' . $user->id . '@invalid.local',
                ]);
            }
        });

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $role = Role::all();
        $activity = Activity::all();
        $group = Group::all();
        $unit = Unit::all();

        $user = User::with([
            'role',
            'profile.activity',
            'profile.group',
            'profile.unit'
        ])->findOrFail($id);

        return view('admin.detailuserd', compact('user','role','activity','group','unit'));
    }

    public function answers(string $id, AnswerReviewFormatter $formatter)
    {
        $user = User::query()
            ->with([
                'role',
                'profile.activity',
                'profile.group',
                'profile.unit',
                'surveySession',
            ])
            ->findOrFail($id);

        $answers = Answer::query()
            ->where('user_id', $user->id)
            ->with([
                'form:id,name',
                'question:id,no_header,no,name,questiontype_id',
                'question.options:id,question_id,no,answer_text,answer_text2,has_child',
                'competitor:id,name',
                'subunit:id,name',
            ])
            ->orderBy('form_id')
            ->orderBy('question_id')
            ->orderBy('id')
            ->get()
            ->each(function (Answer $answer) use ($formatter): void {
                $answer->setAttribute(
                    'review_details',
                    $formatter->format($answer)
                );
            });

        $session = $user->surveySession;
        $status = $session?->status === 'completed'
            ? 'completed'
            : (($session?->status === 'in_progress' || $session?->started_at || $answers->isNotEmpty())
                ? 'in_progress'
                : 'not_started');

        $survey = [
            'status' => $status,
            'status_label' => match ($status) {
                'completed' => 'Sudah Mengisi',
                'in_progress' => 'Sedang Mengisi',
                default => 'Belum Mengisi',
            },
            'started_at' => $session?->started_at?->format('d-m-Y H:i'),
            'finished_at' => $session?->finished_at?->format('d-m-Y H:i'),
            'reopened_at' => $session?->reopened_at?->format('d-m-Y H:i'),
            'answers_count' => $answers->count(),
        ];

        return view('admin.users.answers', compact('user', 'answers', 'survey'));
    }

    public function edit(string $id)
    {
        $user = User::query()
            ->with('profile')
            ->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role_id' => $user->role_id,
            'activity_id' => $user->profile?->activity_id,
        ]);
    }

    public function edit_password(string $id)
    {
        $user = User::query()->findOrFail($id);

        return view('admin.edit.editpassword', compact('user'));
    }

    public function update_password(Request $request, string $id)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'password' => ['required', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::query()->findOrFail($id);
        $user->update([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.datauser')
            ->with('success', 'Password user berhasil diperbarui.');
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username'      => [
                'required',
                'string',
                'max:191',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'password'      => ['nullable', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
            'role_id'       => 'required|exists:roles,id',
            'activity_id'   => 'nullable|exists:activities,id',
        ]);
        $user = User::findOrFail($id);

        $activityIsOptional = in_array((int) $validated['role_id'], [1, 2], true);

        if (! $activityIsOptional && empty($validated['activity_id'])) {
            throw ValidationException::withMessages([
                'activity_id' => 'Activity wajib dipilih untuk role ini.',
            ]);
        }

        if ((int) $user->id === (int) auth()->id()
            && (int) $validated['role_id'] !== (int) $user->role_id) {
            return back()->with('error', 'Role akun yang sedang digunakan tidak dapat diubah.');
        }

        $data = [
            'username' => $validated['username'],
            'role_id'  => $validated['role_id'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'activity_id' => $activityIsOptional
                    ? null
                    : $validated['activity_id'],
                'group_id' => $activityIsOptional
                    ? null
                    : $user->profile?->group_id,
                'unit_id' => $activityIsOptional
                    ? null
                    : $user->profile?->unit_id,
                'email' => $user->profile?->email
                    ?? 'pending-user-' . $user->id . '@invalid.local',
            ]
        );

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function resetAccount($id)
    {
        $result = DB::transaction(function () use ($id): array {
            $user = User::query()
                ->with('profile')
                ->findOrFail($id);

            $answerCount = Answer::query()
                ->where('user_id', $user->id)
                ->count();

            Answer::query()
                ->where('user_id', $user->id)
                ->delete();

            $sessionCount = $user->surveySessions()->count();
            $user->surveySessions()->delete();

            $user->profile?->update([
                'activity_id' => null,
                'group_id' => null,
                'unit_id' => null,
            ]);

            return [$answerCount, $sessionCount];
        });

        return redirect()
            ->route('admin.datauser')
            ->with(
                'successdelete',
                'Reset Account berhasil. '.$result[0].' jawaban dan '.$result[1].' progres survey dihapus; Activity, Group, dan Unit dikosongkan.'
            );
    }

    public function destroy(string $id)
    {
        abort_if((int) $id === (int) auth()->id(), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');

        $deletedAnswers = DB::transaction(function () use ($id): int {
            $user = User::query()->findOrFail($id);

            $answerCount = Answer::query()
                ->where('user_id', $user->id)
                ->count();

            Answer::query()
                ->where('user_id', $user->id)
                ->delete();

            $user->surveySessions()->delete();

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }

            $user->profile()->delete();
            $user->delete();

            return $answerCount;
        });

        return redirect()->back()->with(
            'successdelete',
            'Akun, profil, dan '.$deletedAnswers.' data jawaban berhasil dihapus.'
        );
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
        ]);

        if (in_array((int) auth()->id(), array_map('intval', $validated['ids']), true)) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        $result = DB::transaction(function () use ($validated): array {
            $ids = array_map('intval', $validated['ids']);
            $answerCount = Answer::query()
                ->whereIn('user_id', $ids)
                ->count();

            Answer::query()
                ->whereIn('user_id', $ids)
                ->delete();

            if (Schema::hasTable('survey_sessions')) {
                DB::table('survey_sessions')
                    ->whereIn('user_id', $ids)
                    ->delete();
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')
                    ->whereIn('user_id', $ids)
                    ->delete();
            }

            UserProfile::query()
                ->whereIn('user_id', $ids)
                ->delete();

            User::query()
                ->whereIn('id', $ids)
                ->delete();

            return [count($ids), $answerCount];
        });

        return redirect()->back()->with(
            'successdelete',
            $result[0].' akun beserta '.$result[1].' data jawaban berhasil dihapus.'
        );
    }
}
