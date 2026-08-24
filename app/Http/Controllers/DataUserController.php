<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Unit;
use App\Models\Activity;
use App\Models\Answer;
use App\Models\RespondentCompetitor;
use App\Models\Form;
use App\Models\UserProfile;
use App\Services\AnswerReviewFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
        $sheet->setCellValue('E1','id_group (opsional)');
        $sheet->setCellValue('F1','id_unit (opsional)');
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
                $role->id.' | Role: '. $role->name
            );
            $master->setCellValue(
                'B'.$row,
                $role->name
            );
            $row++;
        }
        $lastRole = $row - 1;
        $spreadsheet->addDefinedName(DefinedName::createInstance(
            'ROLE_LIST',
            $master,
            "'Master Data'!\$A\$3:\$A\$".max(3, $lastRole)
        ));
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

        $master->setCellValue('G1','GROUP');
        $master->setCellValue('G2','ID');
        $master->setCellValue('H2','Nama Group');
        $master->setCellValue('I2','ID Activity');
        $master->setCellValue('J2','Nama Activity');
        $groups = Group::query()->with('activity')->orderBy('activity_id')->orderBy('id')->get();
        $row = 3;
        foreach ($groups as $group) {
            $master->setCellValue('G'.$row, $group->id);
            $master->setCellValue('H'.$row, $group->name);
            $master->setCellValue('I'.$row, $group->activity_id);
            $master->setCellValue('J'.$row, $group->activity?->name);
            $row++;
        }
        $lastGroup = $row - 1;

        $master->setCellValue('L1','UNIT');
        $master->setCellValue('L2','ID');
        $master->setCellValue('M2','Nama Unit');
        $master->setCellValue('N2','ID Group');
        $master->setCellValue('O2','Nama Group');
        $master->setCellValue('P2','ID Activity');
        $master->setCellValue('Q2','Nama Activity');
        $units = Unit::query()->with('group.activity')->orderBy('group_id')->orderBy('id')->get();
        $row = 3;
        foreach ($units as $unit) {
            $master->setCellValue('L'.$row, $unit->id);
            $master->setCellValue('M'.$row, $unit->name);
            $master->setCellValue('N'.$row, $unit->group_id);
            $master->setCellValue('O'.$row, $unit->group?->name);
            $master->setCellValue('P'.$row, $unit->group?->activity_id);
            $master->setCellValue('Q'.$row, $unit->group?->activity?->name);
            $row++;
        }
        $lastUnit = $row - 1;

        // Named ranges power the Activity -> Group -> Unit dependent dropdowns.
        $dropdown = $spreadsheet->createSheet();
        $dropdown->setTitle('Dropdown Data');
        $dropdown->setCellValue('A1', '');
        $spreadsheet->addDefinedName(DefinedName::createInstance(
            'EMPTY_LIST', $dropdown, "'Dropdown Data'!\$A\$1:\$A\$1"
        ));
        foreach ($activities->values() as $index => $activity) {
            $dropdown->setCellValue(
                'A'.($index + 2),
                $activity->id.' | Activity: '.$activity->name
            );
        }
        $spreadsheet->addDefinedName(DefinedName::createInstance(
            'ACTIVITY_LIST',
            $dropdown,
            "'Dropdown Data'!\$A\$2:\$A\$".max(2, $activities->count() + 1)
        ));

        $helperColumn = 2;
        foreach ($activities as $activity) {
            $column = Coordinate::stringFromColumnIndex($helperColumn++);
            $activityGroups = $groups->where('activity_id', $activity->id)->values();
            $dropdown->setCellValue($column.'1', 'Group untuk '.$activity->name);
            foreach ($activityGroups as $index => $group) {
                $dropdown->setCellValue(
                    $column.($index + 2),
                    $group->id.' | Group: '. $group->name
                );
            }
            $lastDropdownRow = max(2, $activityGroups->count() + 1);
            $spreadsheet->addDefinedName(DefinedName::createInstance(
                'ACT_'.$activity->id,
                $dropdown,
                "'Dropdown Data'!\$".$column.'$2:$'.$column.'$'.$lastDropdownRow
            ));
        }

        foreach ($groups as $group) {
            $column = Coordinate::stringFromColumnIndex($helperColumn++);
            $groupUnits = $units->where('group_id', $group->id)->values();
            $dropdown->setCellValue($column.'1', 'Unit untuk '.$group->name);
            foreach ($groupUnits as $index => $unit) {
                $dropdown->setCellValue(
                    $column.($index + 2),
                    $unit->id.' | Unit: '. $unit->name
                );
            }
            $lastDropdownRow = max(2, $groupUnits->count() + 1);
            $spreadsheet->addDefinedName(DefinedName::createInstance(
                'GRP_'.$group->id,
                $dropdown,
                "'Dropdown Data'!\$".$column.'$2:$'.$column.'$'.$lastDropdownRow
            ));
        }
        $dropdown->setSheetState(Worksheet::SHEETSTATE_VERYHIDDEN);
        $roleValidation = $sheet->getCell('C2')->getDataValidation();
        $roleValidation->setType(DataValidation::TYPE_LIST);
        $roleValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $roleValidation->setAllowBlank(true);
        $roleValidation->setShowDropDown(true);
        $roleValidation->setShowErrorMessage(true);
        $roleValidation->setFormula1('ROLE_LIST');
        $roleValidation->setSqref('C2:C1000');

        $activityValidation = $sheet->getCell('D2')->getDataValidation();
        $activityValidation->setType(DataValidation::TYPE_LIST);
        $activityValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $activityValidation->setAllowBlank(true);
        $activityValidation->setShowDropDown(true);
        $activityValidation->setShowErrorMessage(true);
        $activityValidation->setFormula1('ACTIVITY_LIST');
        $activityValidation->setSqref('D2:D1000');

        $groupValidation = $sheet->getCell('E2')->getDataValidation();
        $groupValidation->setType(DataValidation::TYPE_LIST);
        $groupValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $groupValidation->setAllowBlank(true);
        $groupValidation->setShowDropDown(true);
        $groupValidation->setShowErrorMessage(true);
        $groupValidation->setErrorTitle('Group tidak sesuai');
        $groupValidation->setError('Pilih Group yang tersedia untuk Activity pada baris ini.');
        $groupValidation->setFormula1(
            'INDIRECT(IF($D2="","EMPTY_LIST","ACT_"&IFERROR(LEFT($D2,FIND(" |",$D2)-1),$D2)))'
        );
        $groupValidation->setSqref('E2:E1000');

        $unitValidation = $sheet->getCell('F2')->getDataValidation();
        $unitValidation->setType(DataValidation::TYPE_LIST);
        $unitValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $unitValidation->setAllowBlank(true);
        $unitValidation->setShowDropDown(true);
        $unitValidation->setShowErrorMessage(true);
        $unitValidation->setErrorTitle('Unit tidak sesuai');
        $unitValidation->setError('Pilih Unit yang tersedia untuk Group pada baris ini.');
        $unitValidation->setFormula1(
            'INDIRECT(IF($E2="","EMPTY_LIST","GRP_"&IFERROR(LEFT($E2,FIND(" |",$E2)-1),$E2)))'
        );
        $unitValidation->setSqref('F2:F1000');
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (['A','B','D','E','G','H','I','J','L','M','N','O','P','Q'] as $column) {
            $master->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_user.xlsx';
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240']]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
            $rows = $spreadsheet->getSheet(0)->toArray();
            $data = [];
            $seenUsernames = [];

            foreach ($rows as $index => $row) {
                if ($index === 0 || empty(array_filter($row, fn ($value) => $value !== null && $value !== ''))) {
                    continue;
                }

                $item = [
                    'username' => trim((string) ($row[0] ?? '')),
                    'password' => (string) ($row[1] ?? ''),
                    'role_id' => $this->templateReferenceId($row[2] ?? null),
                    'activity_id' => $this->templateReferenceId($row[3] ?? null),
                    'group_id' => $this->templateReferenceId($row[4] ?? null),
                    'unit_id' => $this->templateReferenceId($row[5] ?? null),
                ];

                $validator = Validator::make($item, [
                    'username' => ['required', 'string', 'max:191', 'unique:users,username'],
                    'password' => ['required', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
                    'role_id' => ['required', 'integer', 'exists:roles,id'],
                    'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
                    'group_id' => ['nullable', 'integer', 'exists:groups,id'],
                    'unit_id' => ['nullable', 'integer', 'exists:units,id'],
                ]);

                $roleId = (int) $item['role_id'];
                $normalizedUsername = strtolower($item['username']);
                $invalid = $validator->fails()
                    || (! in_array($roleId, [1, 2], true) && empty($item['activity_id']))
                    || isset($seenUsernames[$normalizedUsername]);

                if (! in_array($roleId, [2, 4], true)) {
                    $item['group_id'] = null;
                    $item['unit_id'] = null;
                }
                if ($roleId === 1) {
                    $item['activity_id'] = null;
                }

                if ($item['group_id'] && (! $item['activity_id'] || ! Group::query()->whereKey($item['group_id'])->where('activity_id', $item['activity_id'])->exists())) {
                    $invalid = true;
                }
                if ($item['unit_id'] && (! $item['group_id'] || ! Unit::query()->whereKey($item['unit_id'])->where('group_id', $item['group_id'])->exists())) {
                    $invalid = true;
                }

                if ($invalid) {
                    throw ValidationException::withMessages([
                        'file' => 'Data user pada baris '.($index + 1).' tidak valid. Pastikan Activity, Group, dan Unit saling sesuai.',
                    ]);
                }

                $seenUsernames[$normalizedUsername] = true;
                $data[] = $item;
            }

            if ($data === []) {
                throw ValidationException::withMessages(['file' => 'Tidak ada data user yang dapat diimport.']);
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
                        'group_id' => $item['group_id'],
                        'unit_id' => $item['unit_id'],
                        'fullname' => null,
                        'no_handphone' => null,
                        'email' => 'pending-user-'.$user->id.'@invalid.local',
                        'activity_id' => $item['activity_id'],
                    ]);
                }
            });

            $spreadsheet->disconnectWorksheets();
            return redirect()->back()->with('success', count($data).' user berhasil diimport.');
        } catch (ValidationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            report($error);
            return redirect()->back()->with('error', 'Import user gagal. Periksa kembali format file.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'array', 'min:1'],
            'username.*' => ['required', 'string', 'max:191', 'distinct', 'unique:users,username'],
            'password' => ['required', 'array', 'min:1'],
            'password.*' => ['required', 'string', Password::min(10)->mixedCase()->numbers()->symbols()],
            'role_id' => ['required', 'array', 'min:1'],
            'role_id.*' => ['required', 'exists:roles,id'],
            'activity_id' => ['nullable', 'array'],
            'activity_id.*' => ['nullable', 'exists:activities,id'],
            'group_id' => ['nullable', 'array'],
            'group_id.*' => ['nullable', 'exists:groups,id'],
            'unit_id' => ['nullable', 'array'],
            'unit_id.*' => ['nullable', 'exists:units,id'],
        ]);

        $rowCount = count($request->username);
        if (count($request->password) !== $rowCount || count($request->role_id) !== $rowCount) {
            throw ValidationException::withMessages(['username' => 'Jumlah username, password, dan role harus sama.']);
        }

        $activities = array_pad($request->input('activity_id', []), $rowCount, null);
        $groups = array_pad($request->input('group_id', []), $rowCount, null);
        $units = array_pad($request->input('unit_id', []), $rowCount, null);

        foreach ($request->role_id as $index => $roleId) {
            $roleId = (int) $roleId;
            if (! in_array($roleId, [1, 2], true) && empty($activities[$index])) {
                throw ValidationException::withMessages(["activity_id.$index" => 'Activity wajib dipilih untuk role ini.']);
            }
            if (! in_array($roleId, [2, 4], true)) {
                $groups[$index] = null;
                $units[$index] = null;
            }
            if ($roleId === 1) {
                $activities[$index] = null;
            }
            if ($groups[$index] && (! $activities[$index] || ! Group::query()->whereKey($groups[$index])->where('activity_id', $activities[$index])->exists())) {
                throw ValidationException::withMessages(["group_id.$index" => 'Group tidak sesuai dengan Activity yang dipilih.']);
            }
            if ($units[$index] && (! $groups[$index] || ! Unit::query()->whereKey($units[$index])->where('group_id', $groups[$index])->exists())) {
                throw ValidationException::withMessages(["unit_id.$index" => 'Unit tidak sesuai dengan Group yang dipilih.']);
            }
        }

        DB::transaction(function () use ($request, $activities, $groups, $units, $rowCount): void {
            for ($index = 0; $index < $rowCount; $index++) {
                $user = User::create([
                    'username' => $request->username[$index],
                    'password' => Hash::make($request->password[$index]),
                    'role_id' => $request->role_id[$index],
                ]);
                UserProfile::create([
                    'user_id' => $user->id,
                    'activity_id' => $activities[$index],
                    'group_id' => $groups[$index],
                    'unit_id' => $units[$index],
                    'fullname' => null,
                    'no_handphone' => null,
                    'email' => 'pending-user-'.$user->id.'@invalid.local',
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
                'respondentCompetitor:id,name',
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

    public function downloadAnswersPdf(
        string $id,
        AnswerReviewFormatter $formatter
    ) {
        $user = User::query()
            ->with([
                'role',
                'profile.activity',
                'profile.group',
                'profile.unit',
                'surveySession',
            ])
            ->findOrFail($id);

        abort_unless(
            $user->surveySession?->status === 'completed',
            422,
            'PDF review jawaban hanya tersedia setelah survey selesai.'
        );

        $answers = Answer::query()
            ->where('user_id', $user->id)
            ->with([
                'form:id,name',
                'question:id,no_header,no,name,questiontype_id',
                'question.options:id,question_id,no,answer_text,answer_text2,has_child',
                'competitor:id,name',
                'respondentCompetitor:id,name',
                'subunit:id,name',
            ])
            ->orderBy('form_id')
            ->orderBy('question_id')
            ->orderBy('id')
            ->get()
            ->each(function (Answer $answer) use ($formatter): void {
                $answer->setAttribute('review_details', $formatter->format($answer));
            });

        $filename = 'review-jawaban-'.str($user->username)
            ->slug('-')
            ->append('.pdf')
            ->toString();

        return Pdf::loadView('admin.users.answers-pdf', [
            'user' => $user,
            'profile' => $user->profile,
            'session' => $user->surveySession,
            'answers' => $answers,
            'generatedAt' => now(),
        ])
            ->setPaper('a4', 'landscape')
            ->download($filename);
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

    public function deleteAnswers(string $id)
    {
        $result = DB::transaction(function () use ($id): array {
            $user = User::query()->lockForUpdate()->findOrFail($id);
            $answerCount = Answer::query()->where('user_id', $user->id)->count();
            Answer::query()->where('user_id', $user->id)->delete();

            return [$user->username, $answerCount];
        });

        return redirect()->route('admin.datauser')->with(
            'successdelete',
            $result[1].' jawaban milik '.$result[0].' berhasil dihapus. Profil, status survey, dan akun tetap dipertahankan.'
        );
    }

    public function reopenSurvey(string $id)
    {
        $result = DB::transaction(function () use ($id): array {
            $user = User::query()
                ->with(['profile', 'surveySession'])
                ->lockForUpdate()
                ->findOrFail($id);
            $profile = $user->profile;
            $session = $user->surveySession;

            if (! $profile?->activity_id || ! $profile?->group_id || ! $profile?->unit_id) {
                throw ValidationException::withMessages([
                    'user' => 'Akses survey belum dapat dibuka karena Activity, Group, atau Unit user belum lengkap.',
                ]);
            }

            if (! $session || $session->status !== 'completed') {
                throw ValidationException::withMessages([
                    'user' => 'Akses hanya dapat dibuka kembali untuk akun yang surveinya sudah selesai.',
                ]);
            }

            $firstForm = Form::query()
                ->where('group_id', $profile->group_id)
                ->orderBy('no_urut')
                ->orderBy('id')
                ->first();

            if (! $firstForm) {
                throw ValidationException::withMessages([
                    'user' => 'Akses belum dapat dibuka karena Group user belum memiliki form survey.',
                ]);
            }

            $session->update([
                'activity_id' => $profile->activity_id,
                'group_id' => $profile->group_id,
                'unit_id' => $profile->unit_id,
                'current_form_id' => $firstForm->id,
                'status' => 'in_progress',
                'finished_at' => null,
                'reopened_at' => now(),
            ]);

            return [$user->username, $session->fresh()];
        });

        return redirect()->route('admin.datauser')->with(
            'success',
            'Akses survey '.$result[0].' berhasil dibuka kembali. Jawaban yang sudah ada tetap dipertahankan.'
        );
    }
    public function clearProfileAssignment(string $id)
    {
        $user = User::query()
            ->with('profile')
            ->findOrFail($id);

        if (! $user->profile) {
            throw ValidationException::withMessages([
                'profile' => 'Profile user tidak ditemukan.',
            ]);
        }

        $user->profile->update([
            'group_id' => null,
            'unit_id' => null,
        ]);

        return redirect()
            ->route('admin.datauser')
            ->with(
                'successdelete',
                'Group dan Unit profile '.$user->username.' berhasil dikosongkan. Jawaban, Activity, progres survey, dan akun tetap dipertahankan.'
            );
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

            if (Schema::hasTable('respondent_competitors')) {
                RespondentCompetitor::query()
                    ->where('user_id', $user->id)
                    ->delete();
            }

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
                'Reset Profile berhasil. '.$result[0].' jawaban dan '.$result[1].' progres survey dihapus; Activity, Group, dan Unit dikosongkan.'
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

            if (Schema::hasTable('respondent_competitors')) {
                RespondentCompetitor::query()
                    ->where('user_id', $user->id)
                    ->delete();
            }

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
    private function templateReferenceId(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $text = trim((string) $value);

        return preg_match('/^(\d+)(?:\s*\||\s*$)/', $text, $matches) === 1
            ? (int) $matches[1]
            : null;
    }
}
