<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\Group;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\AnswerReviewFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MonitoringController extends Controller
{
    /**
     * Rekap progres seluruh unit pada activity akun Monitoring.
     *
     * "Sudah mengisi" mengikuti definisi dashboard: survey session dengan
     * status completed. Persentase dihitung dari data tersebut dibagi total
     * responden pada unit yang sama.
     */
    public function unitRecap(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('monitoring'), 403);

        $activityId = (int) ($user->profile?->activity_id ?? 0);
        abort_unless(
            $activityId > 0,
            403,
            'Activity akun monitoring belum ditentukan.'
        );

        $activity = Activity::query()
            ->select('id', 'name')
            ->findOrFail($activityId);

        [$recapFilters, $groups, $units] = $this->unitRecapFilters(
            $request,
            $activityId
        );

        $unitRecaps = Unit::query()
            ->select([
                'units.id',
                'units.name',
                'groups.name as group_name',
            ])
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN LOWER(roles.name) = ? THEN user_profiles.user_id END) as respondent_count',
                ['user']
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN LOWER(roles.name) = ? AND survey_sessions.status = ? THEN user_profiles.user_id END) as completed_count',
                ['user', 'completed']
            )
            ->join('groups', 'groups.id', '=', 'units.group_id')
            ->leftJoin('user_profiles', function ($join) use ($activityId): void {
                $join->on('user_profiles.unit_id', '=', 'units.id')
                    ->where('user_profiles.activity_id', '=', $activityId);
            })
            ->leftJoin('users', 'users.id', '=', 'user_profiles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin(
                'survey_sessions',
                'survey_sessions.user_id',
                '=',
                'user_profiles.user_id'
            )
            ->where('groups.activity_id', $activityId)
            ->when(
                $recapFilters['group_id'] ?? null,
                fn (Builder $query, $groupId) => $query->where('units.group_id', $groupId)
            )
            ->when(
                $recapFilters['unit_id'] ?? null,
                fn (Builder $query, $unitId) => $query->where('units.id', $unitId)
            )
            ->groupBy('units.id', 'units.name', 'groups.name')
            ->orderBy('groups.name')
            ->orderBy('units.name')
            ->get()
            ->map(function (Unit $unit): Unit {
                $unit->respondent_count = (int) $unit->respondent_count;
                $unit->completed_count = (int) $unit->completed_count;
                $unit->completion_percentage = $unit->respondent_count > 0
                    ? round(
                        ($unit->completed_count / $unit->respondent_count) * 100,
                        2
                    )
                    : 0;

                return $unit;
            });

        $totalUnits = $unitRecaps->count();
        $completedUnitCount = $unitRecaps
            ->filter(fn (Unit $unit): bool => $unit->respondent_count > 0
                && $unit->completion_percentage >= 100)
            ->count();
        $inProgressUnitCount = $unitRecaps
            ->filter(fn (Unit $unit): bool => $unit->completion_percentage > 0
                && $unit->completion_percentage < 100)
            ->count();
        $notStartedUnitCount = $unitRecaps
            ->filter(fn (Unit $unit): bool => $unit->completion_percentage <= 0)
            ->count();
        $completionPercentage = $totalUnits > 0
            ? round(($completedUnitCount / $totalUnits) * 100, 2)
            : 0;

        $recapStatus = $recapFilters['unit_status'] ?? 'all';
        $unitRecaps = match ($recapStatus) {
            'completed' => $unitRecaps->filter(
                fn (Unit $unit): bool => $unit->respondent_count > 0
                    && $unit->completion_percentage >= 100
            )->values(),
            'in_progress' => $unitRecaps->filter(
                fn (Unit $unit): bool => $unit->completion_percentage > 0
                    && $unit->completion_percentage < 100
            )->values(),
            'not_started' => $unitRecaps->filter(
                fn (Unit $unit): bool => $unit->completion_percentage <= 0
            )->values(),
            default => $unitRecaps,
        };

        return view('monitoring.unit-recap', compact(
            'activity',
            'unitRecaps',
            'totalUnits',
            'completedUnitCount',
            'inProgressUnitCount',
            'notStartedUnitCount',
            'completionPercentage',
            'recapStatus',
            'groups',
            'units',
            'recapFilters'
        ));
    }

    /**
     * Export detail respondents for the Monitoring unit recap.
     *
     * Sheet one contains completed surveys. Sheet two contains respondents
     * who have not started or are still filling the survey, with their status.
     */
    public function exportUnitRecap(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('monitoring'), 403);

        $activityId = (int) ($user->profile?->activity_id ?? 0);
        abort_unless(
            $activityId > 0,
            403,
            'Activity akun monitoring belum ditentukan.'
        );

        $activity = Activity::query()
            ->select('id', 'name')
            ->findOrFail($activityId);

        [$recapFilters] = $this->unitRecapFilters($request, $activityId);

        $respondents = UserProfile::query()
            ->where('activity_id', $activityId)
            ->whereHas('user.role', function (Builder $query): void {
                $query->whereRaw('LOWER(name) = ?', ['user']);
            })
            ->with([
                'group:id,name',
                'unit:id,name',
                'user' => function ($query): void {
                    $query
                        ->select('id', 'username', 'role_id')
                        ->with('surveySession')
                        ->withCount('answers');
                },
            ])
            ->when(
                $recapFilters['group_id'] ?? null,
                fn (Builder $query, $groupId) => $query->where('group_id', $groupId)
            )
            ->when(
                $recapFilters['unit_id'] ?? null,
                fn (Builder $query, $unitId) => $query->where('unit_id', $unitId)
            )
            ->orderBy('group_id')
            ->orderBy('unit_id')
            ->orderBy('user_id')
            ->get()
            ->map(function (UserProfile $profile): array {
                $session = $profile->user?->surveySession;
                $status = $this->resolveStatus(
                    $session?->status,
                    $session?->started_at,
                    (int) ($profile->user?->answers_count ?? 0)
                );

                return [
                    'id_user' => $profile->user_id,
                    'username' => $profile->user?->username ?? '-',
                    'fullname' => $profile->fullname ?: '-',
                    'email' => $profile->email ?: '-',
                    'group' => $profile->group?->name ?? '-',
                    'unit' => $profile->unit?->name ?? '-',
                    'status' => match ($status) {
                        'completed' => 'Sudah Mengisi',
                        'in_progress' => 'Sedang Mengisi',
                        default => 'Belum Mengisi',
                    },
                    'started_at' => $session?->started_at?->format('d-m-Y H:i') ?? '-',
                    'finished_at' => $session?->finished_at?->format('d-m-Y H:i') ?? '-',
                ];
            });

        $completed = $respondents
            ->filter(fn (array $respondent): bool => $respondent['status'] === 'Sudah Mengisi')
            ->values();

        $uncompleted = $respondents
            ->reject(fn (array $respondent): bool => $respondent['status'] === 'Sudah Mengisi')
            ->values();

        $spreadsheet = new Spreadsheet();
        $this->writeUnitRecapSheet(
            $spreadsheet->getActiveSheet(),
            'Sudah Mengisi',
            $activity->name,
            $completed,
            false
        );

        $this->writeUnitRecapSheet(
            $spreadsheet->createSheet(),
            'Belum & Sedang Mengisi',
            $activity->name,
            $uncompleted,
            true
        );

        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'rekap_responden_unit_'.now()->format('Ymd_His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer, $spreadsheet): void {
            try {
                $writer->save('php://output');
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function unitRecapFilters(Request $request, int $activityId): array
    {
        $activityGroupIds = Group::query()
            ->where('activity_id', $activityId)
            ->pluck('id');

        $recapFilters = $request->validate([
            'group_id' => [
                'nullable',
                'integer',
                Rule::exists('groups', 'id')->where('activity_id', $activityId),
            ],
            'unit_id' => [
                'nullable',
                'integer',
                Rule::exists('units', 'id')->whereIn('group_id', $activityGroupIds->all()),
            ],
            'unit_status' => [
                'nullable',
                Rule::in(['all', 'completed', 'in_progress', 'not_started']),
            ],
        ]);

        if (
            ($recapFilters['group_id'] ?? null)
            && ($recapFilters['unit_id'] ?? null)
            && ! Unit::query()
                ->whereKey($recapFilters['unit_id'])
                ->where('group_id', $recapFilters['group_id'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'unit_id' => 'Unit yang dipilih tidak termasuk dalam group tersebut.',
            ]);
        }

        $groups = Group::query()
            ->select('id', 'name')
            ->where('activity_id', $activityId)
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->select('id', 'group_id', 'name')
            ->whereIn('group_id', $activityGroupIds)
            ->orderBy('name')
            ->get();

        return [$recapFilters, $groups, $units];
    }

    private function writeUnitRecapSheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $title,
        string $activityName,
        Collection $respondents,
        bool $includeStatus
    ): void {
        $headers = [
            'No.',
            'ID User',
            'Username',
            'Nama Lengkap',
            'Email',
            'Group',
            'Unit',
        ];

        if ($includeStatus) {
            $headers[] = 'Status';
        }

        $headers[] = 'Waktu Mulai';
        $headers[] = 'Waktu Selesai';
        $lastColumn = $includeStatus ? 'J' : 'I';

        $sheet->setTitle($title);
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', $title.' - '.$activityName);
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', 'Diekspor pada '.now()->format('d-m-Y H:i'));
        $sheet->fromArray($headers, null, 'A3');

        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowNumber = 4;
        foreach ($respondents as $index => $respondent) {
            $row = [
                $index + 1,
                $respondent['id_user'],
                $respondent['username'],
                $respondent['fullname'],
                $respondent['email'],
                $respondent['group'],
                $respondent['unit'],
            ];

            if ($includeStatus) {
                $row[] = $respondent['status'];
            }

            $row[] = $respondent['started_at'];
            $row[] = $respondent['finished_at'];
            $sheet->fromArray($row, null, "A{$rowNumber}");
            $rowNumber++;
        }

        if ($respondents->isEmpty()) {
            $sheet->mergeCells("A4:{$lastColumn}4");
            $sheet->setCellValue('A4', 'Tidak ada data responden.');
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setAutoFilter("A3:{$lastColumn}".($rowNumber - 1));
        }

        foreach ([
            'A' => 8,
            'B' => 12,
            'C' => 22,
            'D' => 26,
            'E' => 30,
            'F' => 24,
            'G' => 30,
            'H' => 18,
            'I' => 20,
            'J' => 20,
        ] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->freezePane('A4');
        $sheet->getStyle("A3:{$lastColumn}".max(3, $rowNumber - 1))
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    public function index(Request $request)
    {
        // User Monitoring Login

        $user = auth()->user();

        // Pastikan Role Monitoring

        if (
            !$user->hasRole('monitoring')
            && !$user->hasRole('surveyor')
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke halaman monitoring.'
            );
        }

        // Ambil Activity Monitoring
        // Activity tidak diambil dari request.
        // Activity diambil langsung dari profile user Monitoring.

        $monitoringProfile = $user->profile;

        if (!$monitoringProfile || !$monitoringProfile->activity_id) {
            if ($user->hasRole('surveyor')) {
                return redirect()
                    ->route('profile.edit')
                    ->with(
                        'warning',
                        'Pilih Activity, Group, dan Unit sebelum membuka Dashboard Monitoring.'
                    );
            }

            abort(
                403,
                !$monitoringProfile
                    ? 'Profile akun monitoring belum tersedia.'
                    : 'Activity akun monitoring belum ditentukan.'
            );
        }

        $activityId = (int) $monitoringProfile->activity_id;
        $isSurveyor = $user->hasRole('surveyor');
        $lockedGroupId = $isSurveyor
            ? (int) $monitoringProfile->group_id
            : null;
        $lockedUnitId = $isSurveyor
            ? (int) $monitoringProfile->unit_id
            : null;

        if ($isSurveyor && (!$lockedGroupId || !$lockedUnitId)) {
            return redirect()
                ->route('profile.edit')
                ->with('warning', 'Lengkapi Activity, Group, dan Unit sebelum membuka Dashboard Monitoring.');
        }

        // Validasi Filter
        // activity_id SENGAJA tidak ada.
        // Monitoring tidak boleh memilih Activity sendiri.

        $activityGroupIds = Group::query()
            ->where('activity_id', $activityId)
            ->pluck('id');

        $filters = $request->validate([
            'username' => [
                'nullable',
                'string',
                'max:255',
            ],

            'group_id' => [
                'nullable',
                'integer',
                $isSurveyor
                    ? Rule::in([$lockedGroupId])
                    : Rule::exists('groups', 'id')->where('activity_id', $activityId),
            ],

            'unit_id' => [
                'nullable',
                'integer',
                $isSurveyor
                    ? Rule::in([$lockedUnitId])
                    : Rule::exists('units', 'id')->whereIn('group_id', $activityGroupIds->all()),
            ],

            'status' => [
                'nullable',
                Rule::in(['completed', 'in_progress', 'not_started']),
            ],

            'sort_by' => [
                'nullable',
                Rule::in(['id', 'username']),
            ],

            'sort_direction' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
        ]);

        if ($isSurveyor) {
            $filters['group_id'] = $lockedGroupId;
            $filters['unit_id'] = $lockedUnitId;
        }

        // Query Dasar Responden

        $baseQuery = $this->respondentQuery(
            $filters,
            $activityId
        );

        // Total Responden

        $totalRespondents = (clone $baseQuery)
            ->count();

        // Sudah Mengisi

        $completedCount = (clone $baseQuery)
            ->whereHas(
                'user.surveySession',
                function (Builder $query) {
                    $query->where(
                        'status',
                        'completed'
                    );
                }
            )
            ->count();

        // Sedang Mengisi

        $inProgressCount = (clone $baseQuery)
            ->whereDoesntHave(
                'user.surveySession',
                function (Builder $query) {
                    $query->where(
                        'status',
                        'completed'
                    );
                }
            )
            ->where(
                function (Builder $query) {
                    $query
                        ->whereHas(
                            'user.surveySession',
                            function (Builder $sessionQuery) {
                                $sessionQuery->where(
                                    function (Builder $statusQuery) {
                                        $statusQuery
                                            ->where(
                                                'status',
                                                'in_progress'
                                            )
                                            ->orWhereNotNull(
                                                'started_at'
                                            );
                                    }
                                );
                            }
                        )
                        ->orWhereHas('user.answers');
                }
            )
            ->count();

        // Belum Mengisi

        $notStartedCount = max(
            0,
            $totalRespondents
                - $completedCount
                - $inProgressCount
        );
        $completionPercentage = $totalRespondents > 0
            ? round(($completedCount / $totalRespondents) * 100, 2)
            : 0;

        // Query List Responden

        $respondentsQuery = (clone $baseQuery)
            ->with([
                'activity:id,name',

                'group:id,name,activity_id',

                'unit:id,name,group_id',

                'user' => function ($query) {
                    $query
                        ->select([
                            'users.id',
                            'users.username',
                            'users.role_id',
                        ])
                        ->with([
                            'role:id,name',

                            'surveySession' => function ($sessionQuery) {
                                $sessionQuery->select([
                                    'survey_sessions.id',
                                    'survey_sessions.user_id',
                                    'survey_sessions.activity_id',
                                    'survey_sessions.group_id',
                                    'survey_sessions.unit_id',
                                    'survey_sessions.current_form_id',
                                    'survey_sessions.status',
                                    'survey_sessions.started_at',
                                    'survey_sessions.finished_at',
                                    'survey_sessions.reopened_at',
                                    'survey_sessions.updated_at',
                                ]);
                            },
                        ])
                        ->withCount('answers');
                },
            ]);

        $this->applyStatusFilter(
            $respondentsQuery,
            $filters['status'] ?? null
        );

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        if ($sortBy === 'username') {
            $respondentsQuery->orderBy(
                User::query()
                    ->select('users.username')
                    ->whereColumn('users.id', 'user_profiles.user_id')
                    ->limit(1),
                $sortDirection
            );
        } else {
            $respondentsQuery->orderBy('user_profiles.user_id', $sortDirection);
        }

        $respondentsQuery->orderBy('user_profiles.id');

        // Pagination

        $respondents = $respondentsQuery
            ->paginate(20)
            ->withQueryString();

        // Mapping Status

        $respondents
            ->getCollection()
            ->transform(
                function (UserProfile $profile) {

                    $session =
                        $profile->user?->surveySession;

                    $answersCount = (int) (
                        $profile->user?->answers_count
                        ?? 0
                    );

                    $status = $this->resolveStatus(
                        $session?->status,
                        $session?->started_at,
                        $answersCount
                    );

                    $profile->monitoring_status =
                        $status;

                    $profile->monitoring_status_label =
                        match ($status) {
                            'completed'
                                => 'Sudah Mengisi',

                            'in_progress'
                                => 'Sedang Mengisi',

                            default
                                => 'Belum Mengisi',
                        };

                    return $profile;
                }
            );

        // Activity Monitoring
        // Hanya mengambil satu Activity sesuai akun Monitoring.

        $activity = Activity::query()
            ->select([
                'id',
                'name',
            ])
            ->findOrFail($activityId);

        // Group
        // Hanya group dari Activity Monitoring.

        $groups = Group::query()
            ->select([
                'id',
                'activity_id',
                'name',
            ])
            ->where(
                'activity_id',
                $activityId
            )
            ->when(
                $isSurveyor,
                fn (Builder $query) => $query->where('id', $lockedGroupId)
            )
            ->orderBy('name')
            ->get();

        // Unit
        // Unit hanya dari Group yang termasuk Activity Monitoring.

        $units = Unit::query()
            ->select([
                'id',
                'group_id',
                'name',
            ])
            ->whereHas(
                'group',
                function (Builder $query) use ($activityId) {
                    $query->where(
                        'activity_id',
                        $activityId
                    );
                }
            )
            ->when(
                $isSurveyor,
                fn (Builder $query) => $query->where('id', $lockedUnitId)
            )
            ->orderBy('name')
            ->get();

        // View

        $dashboardRoute = $user->hasRole('surveyor')
            ? 'surveyor.dashboard'
            : 'monitoring.dashboard';

        return view(
            'monitoring.index',
            compact(
                'respondents',
                'activity',
                'groups',
                'units',
                'totalRespondents',
                'completedCount',
                'inProgressCount',
                'notStartedCount',
                'completionPercentage',
                'filters',
                'dashboardRoute',
                'isSurveyor'
            )
        );
    }

    /**
     * Export daftar responden Dashboard Monitoring sesuai filter aktif.
     */
    public function exportDashboardRespondents(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('monitoring'), 403);

        $activityId = (int) ($user->profile?->activity_id ?? 0);
        abort_unless(
            $activityId > 0,
            403,
            'Activity akun monitoring belum ditentukan.'
        );

        $activityGroupIds = Group::query()
            ->where('activity_id', $activityId)
            ->pluck('id');

        $filters = $request->validate([
            'username' => ['nullable', 'string', 'max:255'],
            'group_id' => [
                'nullable',
                'integer',
                Rule::exists('groups', 'id')->where('activity_id', $activityId),
            ],
            'unit_id' => [
                'nullable',
                'integer',
                Rule::exists('units', 'id')->whereIn('group_id', $activityGroupIds->all()),
            ],
            'status' => [
                'nullable',
                Rule::in(['completed', 'in_progress', 'not_started']),
            ],
        ]);

        if (
            ($filters['group_id'] ?? null)
            && ($filters['unit_id'] ?? null)
            && ! Unit::query()
                ->whereKey($filters['unit_id'])
                ->where('group_id', $filters['group_id'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'unit_id' => 'Unit yang dipilih tidak termasuk dalam group tersebut.',
            ]);
        }

        $respondentsQuery = $this->respondentQuery($filters, $activityId)
            ->with([
                'group:id,name',
                'unit:id,name',
                'user' => function ($query): void {
                    $query
                        ->select('id', 'username', 'role_id')
                        ->with('surveySession')
                        ->withCount('answers');
                },
            ]);

        $this->applyStatusFilter($respondentsQuery, $filters['status'] ?? null);

        $respondents = $respondentsQuery
            ->orderBy('user_profiles.user_id')
            ->get()
            ->map(function (UserProfile $profile): array {
                $session = $profile->user?->surveySession;
                $status = $this->resolveStatus(
                    $session?->status,
                    $session?->started_at,
                    (int) ($profile->user?->answers_count ?? 0)
                );

                return [
                    'id_user' => $profile->user_id,
                    'username' => $profile->user?->username ?? '-',
                    'fullname' => $profile->fullname ?: '-',
                    'email' => $profile->email ?: '-',
                    'group' => $profile->group?->name ?? '-',
                    'unit' => $profile->unit?->name ?? '-',
                    'status' => match ($status) {
                        'completed' => 'Sudah Mengisi',
                        'in_progress' => 'Sedang Mengisi',
                        default => 'Belum Mengisi',
                    },
                    'started_at' => $session?->started_at?->format('d-m-Y H:i') ?? '-',
                    'finished_at' => $session?->finished_at?->format('d-m-Y H:i') ?? '-',
                ];
            });

        $activity = Activity::query()
            ->select('id', 'name')
            ->findOrFail($activityId);

        $spreadsheet = new Spreadsheet();
        $this->writeUnitRecapSheet(
            $spreadsheet->getActiveSheet(),
            'Daftar Responden',
            $activity->name,
            $respondents,
            true
        );

        $filename = 'daftar_responden_monitoring_'.now()->format('Ymd_His').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer, $spreadsheet): void {
            try {
                $writer->save('php://output');
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }


    public function respondentProfile(int $userId)
    {
        $profile = $this->surveyorRespondent($userId)
            ->with([
                'activity:id,name',
                'group:id,name,activity_id',
                'unit:id,name,group_id',
                'user:id,username,role_id',
                'user.role:id,name',
                'user.surveySession',
            ])
            ->firstOrFail();

        return view('monitoring.respondent-profile', compact('profile'));
    }

    public function respondentAnswers(
        int $userId,
        AnswerReviewFormatter $formatter
    ) {
        $profile = $this->surveyorRespondent($userId)
            ->with([
                'activity:id,name',
                'group:id,name,activity_id',
                'unit:id,name,group_id',
                'user:id,username,role_id',
                'user.role:id,name',
                'user.surveySession',
            ])
            ->firstOrFail();

        $user = $profile->user;
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

        $session = $user->surveySession;
        $status = $this->resolveStatus(
            $session?->status,
            $session?->started_at,
            $answers->count()
        );
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

        return view('monitoring.respondent-answers', compact('user', 'profile', 'answers', 'survey'));
    }

    private function surveyorRespondent(int $userId): Builder
    {
        $surveyor = auth()->user();
        abort_unless($surveyor?->hasRole('surveyor'), 403);

        $profile = $surveyor->profile;
        abort_unless(
            $profile?->activity_id && $profile?->group_id && $profile?->unit_id,
            403,
            'Profil Surveyor belum lengkap.'
        );

        return UserProfile::query()
            ->where('user_id', $userId)
            ->where('activity_id', $profile->activity_id)
            ->where('group_id', $profile->group_id)
            ->where('unit_id', $profile->unit_id)
            ->whereHas('user.role', function (Builder $query) {
                $query->whereRaw('LOWER(name) = ?', ['user']);
            });
    }

    // Respondent Query

    private function respondentQuery(
        array $filters,
        int $activityId
    ): Builder {

        return UserProfile::query()

            // WAJIB Activity Monitoring
            // Ini bagian terpenting.
            // Walaupun user mencoba:
            // ?activity_id=99
            // data tetap hanya berdasarkan activity miliknya.

            ->where(
                'activity_id',
                $activityId
            )

            // Hanya Responden

            ->whereHas(
                'user.role',
                function (Builder $query) {
                    $query->whereRaw(
                        'LOWER(name) = ?',
                        ['user']
                    );
                }
            )

            // Search

            ->when(
                $filters['username'] ?? null,
                function (
                    Builder $query,
                    string $keyword
                ) {
                    $query->where(
                        function (
                            Builder $searchQuery
                        ) use ($keyword) {

                            $searchQuery
                                ->where(
                                    'fullname',
                                    'like',
                                    "%{$keyword}%"
                                )

                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$keyword}%"
                                )

                                ->orWhereHas(
                                    'user',
                                    function (
                                        Builder $userQuery
                                    ) use ($keyword) {

                                        $userQuery->where(
                                            'username',
                                            'like',
                                            "%{$keyword}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            // Group

            ->when(
                $filters['group_id'] ?? null,
                fn (
                    Builder $query,
                    $id
                ) => $query->where(
                    'group_id',
                    $id
                )
            )

            // Unit

            ->when(
                $filters['unit_id'] ?? null,
                fn (
                    Builder $query,
                    $id
                ) => $query->where(
                    'unit_id',
                    $id
                )
            );
    }


    // Resolve Status

    private function resolveStatus(
        ?string $sessionStatus,
        $startedAt,
        int $answersCount
    ): string {

        if ($sessionStatus === 'completed') {
            return 'completed';
        }

        if (
            $sessionStatus === 'in_progress'
            || $startedAt
            || $answersCount > 0
        ) {
            return 'in_progress';
        }

        return 'not_started';
    }

    private function applyStatusFilter(
        Builder $query,
        ?string $status
    ): void {
        if ($status === 'completed') {
            $query->whereHas(
                'user.surveySession',
                fn (Builder $session) => $session->where('status', 'completed')
            );

            return;
        }

        if ($status === 'in_progress') {
            $query
                ->whereDoesntHave(
                    'user.surveySession',
                    fn (Builder $session) => $session->where('status', 'completed')
                )
                ->where(function (Builder $progress) {
                    $progress
                        ->whereHas('user.surveySession', function (Builder $session) {
                            $session->where(function (Builder $started) {
                                $started
                                    ->where('status', 'in_progress')
                                    ->orWhereNotNull('started_at');
                            });
                        })
                        ->orWhereHas('user.answers');
                });

            return;
        }

        if ($status === 'not_started') {
            $query
                ->whereDoesntHave('user.surveySession', function (Builder $session) {
                    $session
                        ->where('status', 'completed')
                        ->orWhere('status', 'in_progress')
                        ->orWhereNotNull('started_at');
                })
                ->whereDoesntHave('user.answers');
        }
    }
}
