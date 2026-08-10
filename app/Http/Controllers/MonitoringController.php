<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\Group;
use App\Models\Unit;
use App\Models\UserProfile;
use App\Services\AnswerReviewFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | User Monitoring Login
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Pastikan Role Monitoring
        |--------------------------------------------------------------------------
        */

        if (
            !$user->hasRole('monitoring')
            && !$user->hasRole('surveyor')
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke halaman monitoring.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Activity Monitoring
        |--------------------------------------------------------------------------
        |
        | Activity tidak diambil dari request.
        | Activity diambil langsung dari profile user Monitoring.
        |
        */

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

        /*
        |--------------------------------------------------------------------------
        | Validasi Filter
        |--------------------------------------------------------------------------
        |
        | activity_id SENGAJA tidak ada.
        |
        | Monitoring tidak boleh memilih Activity sendiri.
        |
        */

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
        ]);

        if ($isSurveyor) {
            $filters['group_id'] = $lockedGroupId;
            $filters['unit_id'] = $lockedUnitId;
        }

        /*
        |--------------------------------------------------------------------------
        | Query Dasar Responden
        |--------------------------------------------------------------------------
        */

        $baseQuery = $this->respondentQuery(
            $filters,
            $activityId
        );

        /*
        |--------------------------------------------------------------------------
        | Total Responden
        |--------------------------------------------------------------------------
        */

        $totalRespondents = (clone $baseQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Sudah Mengisi
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Sedang Mengisi
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Belum Mengisi
        |--------------------------------------------------------------------------
        */

        $notStartedCount = max(
            0,
            $totalRespondents
                - $completedCount
                - $inProgressCount
        );

        /*
        |--------------------------------------------------------------------------
        | Query List Responden
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $respondents = $respondentsQuery
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Mapping Status
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Activity Monitoring
        |--------------------------------------------------------------------------
        |
        | Hanya mengambil satu Activity sesuai akun Monitoring.
        |
        */

        $activity = Activity::query()
            ->select([
                'id',
                'name',
            ])
            ->findOrFail($activityId);

        /*
        |--------------------------------------------------------------------------
        | Group
        |--------------------------------------------------------------------------
        |
        | Hanya group dari Activity Monitoring.
        |
        */

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

        /*
        |--------------------------------------------------------------------------
        | Unit
        |--------------------------------------------------------------------------
        |
        | Unit hanya dari Group yang termasuk Activity Monitoring.
        |
        */

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

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

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
                'filters',
                'dashboardRoute',
                'isSurveyor'
            )
        );
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

    /*
    |--------------------------------------------------------------------------
    | Respondent Query
    |--------------------------------------------------------------------------
    */

    private function respondentQuery(
        array $filters,
        int $activityId
    ): Builder {

        return UserProfile::query()

            /*
            |--------------------------------------------------------------------------
            | WAJIB Activity Monitoring
            |--------------------------------------------------------------------------
            |
            | Ini bagian terpenting.
            |
            | Walaupun user mencoba:
            |
            | ?activity_id=99
            |
            | data tetap hanya berdasarkan activity miliknya.
            |
            */

            ->where(
                'activity_id',
                $activityId
            )

            /*
            |--------------------------------------------------------------------------
            | Hanya Responden
            |--------------------------------------------------------------------------
            */

            ->whereHas(
                'user.role',
                function (Builder $query) {
                    $query->whereRaw(
                        'LOWER(name) = ?',
                        ['user']
                    );
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Group
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Unit
            |--------------------------------------------------------------------------
            */

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


    /*
    |--------------------------------------------------------------------------
    | Resolve Status
    |--------------------------------------------------------------------------
    */

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
