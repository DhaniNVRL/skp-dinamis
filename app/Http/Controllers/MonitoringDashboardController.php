<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use App\Models\Unit;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringDashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'username'    => ['nullable', 'string', 'max:255'],
            'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
            'group_id'    => ['nullable', 'integer', 'exists:groups,id'],
            'unit_id'     => ['nullable', 'integer', 'exists:units,id'],
            'status'      => ['nullable', 'in:completed,in_progress,not_started'],
        ]);

        $baseQuery = $this->respondentQuery($filters);

        $totalRespondents = (clone $baseQuery)->count();

        $completedCount = (clone $baseQuery)
            ->whereHas('user.surveySession', function (Builder $query) {
                $query->where('status', 'completed');
            })
            ->count();

        $inProgressCount = (clone $baseQuery)
            ->whereDoesntHave('user.surveySession', function (Builder $query) {
                $query->where('status', 'completed');
            })
            ->where(function (Builder $query) {
                $query
                    ->whereHas('user.surveySession', function (Builder $sessionQuery) {
                        $sessionQuery->where(function (Builder $statusQuery) {
                            $statusQuery
                                ->where('status', 'in_progress')
                                ->orWhereNotNull('started_at');
                        });
                    })
                    ->orWhereHas('user.answers');
            })
            ->count();

        $notStartedCount = max(
            0,
            $totalRespondents - $completedCount - $inProgressCount
        );

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
                                    'survey_sessions.updated_at',
                                ]);
                            },

                            'answers' => function ($answerQuery) {
                                $answerQuery->select([
                                    'answers.id',
                                    'answers.user_id',
                                    'answers.form_id',
                                    'answers.question_id',
                                ]);
                            },
                        ])
                        ->withCount('answers');
                },
            ]);

        $this->applyStatusFilter($respondentsQuery, $filters['status'] ?? null);

        $respondents = $respondentsQuery
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $respondents->getCollection()->transform(function (UserProfile $profile) {
            $session = $profile->user?->surveySession;
            $answersCount = (int) ($profile->user?->answers_count ?? 0);

            $status = $this->resolveStatus($session?->status, $session?->started_at, $answersCount);

            $profile->monitoring_status = $status;
            $profile->monitoring_status_label = match ($status) {
                'completed'   => 'Sudah Mengisi',
                'in_progress' => 'Sedang Mengisi',
                default       => 'Belum Mengisi',
            };

            return $profile;
        });

        $activities = Activity::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $groups = Group::query()
            ->select('id', 'activity_id', 'name')
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->select('id', 'group_id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard.index', compact(
            'respondents',
            'activities',
            'groups',
            'units',
            'totalRespondents',
            'completedCount',
            'inProgressCount',
            'notStartedCount',
            'filters'
        ));
    }

    public function respondentDetail(int $userId): JsonResponse
    {
        $profile = UserProfile::query()
            ->where('user_id', $userId)
            ->whereHas('user.role', function (Builder $query) {
                $query->whereRaw('LOWER(name) = ?', ['user']);
            })
            ->with([
                'activity:id,name',
                'group:id,name',
                'unit:id,name',
                'user' => function ($query) {
                    $query
                        ->select('id', 'username', 'role_id')
                        ->with([
                            'role:id,name',
                            'surveySession',
                            'answers' => function ($answerQuery) {
                                $answerQuery
                                    ->with([
                                        'form:id,name',
                                        'question:id,no_header,no,name',
                                        'competitor:id,name',
                                        'subunit:id,name',
                                    ])
                                    ->latest('id');
                            },
                        ]);
                },
            ])
            ->firstOrFail();

        $session = $profile->user?->surveySession;
        $answers = $profile->user?->answers ?? collect();
        $status = $this->resolveStatus(
            $session?->status,
            $session?->started_at,
            $answers->count()
        );

        return response()->json([
            'profile' => [
                'user_id'       => $profile->user_id,
                'username'      => $profile->user?->username,
                'fullname'      => $profile->fullname,
                'email'         => $profile->email,
                'no_handphone'  => $profile->no_handphone,
                'role'          => $profile->user?->role?->name,
                'activity'      => $profile->activity?->name,
                'group'         => $profile->group?->name,
                'unit'          => $profile->unit?->name,
            ],
            'survey' => [
                'status'       => $status,
                'status_label' => match ($status) {
                    'completed'   => 'Sudah Mengisi',
                    'in_progress' => 'Sedang Mengisi',
                    default       => 'Belum Mengisi',
                },
                'started_at'    => optional($session?->started_at)->format('d-m-Y H:i'),
                'finished_at'   => optional($session?->finished_at)->format('d-m-Y H:i'),
                'answers_count' => $answers->count(),
            ],
            'answers' => $answers->map(function ($answer) {
                return [
                    'id'         => $answer->id,
                    'form'       => $answer->form?->name,
                    'question'   => $answer->question?->name,
                    'question_no'=> trim(($answer->question?->no_header ?? '') . ($answer->question?->no ?? '')),
                    'competitor' => $answer->competitor?->name,
                    'subunit'    => $answer->subunit?->name,
                    'answer'     => $this->normalizeAnswer($answer->answer),
                    'updated_at' => optional($answer->updated_at)->format('d-m-Y H:i'),
                ];
            })->values(),
        ]);
    }

    private function respondentQuery(array $filters): Builder
    {
        return UserProfile::query()
            ->whereHas('user.role', function (Builder $query) {
                $query->whereRaw('LOWER(name) = ?', ['user']);
            })
            ->when($filters['username'] ?? null, function (Builder $query, string $keyword) {
                $query->where(function (Builder $searchQuery) use ($keyword) {
                    $searchQuery
                        ->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($keyword) {
                            $userQuery->where('username', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filters['activity_id'] ?? null, fn (Builder $query, $id) => $query->where('activity_id', $id))
            ->when($filters['group_id'] ?? null, fn (Builder $query, $id) => $query->where('group_id', $id))
            ->when($filters['unit_id'] ?? null, fn (Builder $query, $id) => $query->where('unit_id', $id));
    }

    private function applyStatusFilter(Builder $query, ?string $status): void
    {
        if ($status === 'completed') {
            $query->whereHas('user.surveySession', fn (Builder $session) => $session->where('status', 'completed'));
            return;
        }

        if ($status === 'in_progress') {
            $query
                ->whereDoesntHave('user.surveySession', fn (Builder $session) => $session->where('status', 'completed'))
                ->where(function (Builder $progress) {
                    $progress
                        ->whereHas('user.surveySession', function (Builder $session) {
                            $session->where(function (Builder $started) {
                                $started->where('status', 'in_progress')->orWhereNotNull('started_at');
                            });
                        })
                        ->orWhereHas('user.answers');
                });
            return;
        }

        if ($status === 'not_started') {
            $query
                ->whereDoesntHave('user.surveySession', function (Builder $session) {
                    $session->where('status', 'completed')
                        ->orWhere('status', 'in_progress')
                        ->orWhereNotNull('started_at');
                })
                ->whereDoesntHave('user.answers');
        }
    }

    private function resolveStatus(?string $sessionStatus, $startedAt, int $answersCount): string
    {
        if ($sessionStatus === 'completed') {
            return 'completed';
        }

        if ($sessionStatus === 'in_progress' || $startedAt || $answersCount > 0) {
            return 'in_progress';
        }

        return 'not_started';
    }

    private function normalizeAnswer($answer)
    {
        if (!is_string($answer)) {
            return $answer;
        }

        $decoded = json_decode($answer, true);

        return json_last_error() === JSON_ERROR_NONE
            ? $decoded
            : $answer;
    }
}
