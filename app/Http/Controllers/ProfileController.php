<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\CompleteProfile;
use App\Models\Group;
use App\Models\RespondentCompetitor;
use App\Models\SurveySession;
use App\Models\Unit;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Lengkapi profil pertama kali
    |--------------------------------------------------------------------------
    */
    public function complete()
    {
        $existingProfile = UserProfile::where(
            'user_id',
            auth()->id()
        )->first();

        if ($existingProfile) {
            if (
                ! $existingProfile->group_id ||
                ! $existingProfile->unit_id
            ) {
                return redirect()->route('profile.edit');
            }

            return redirect()->route('profile.show');
        }

        abort_if(
            auth()->user()?->hasRole('surveyor'),
            403,
            'Activity akun Surveyor belum ditentukan oleh administrator.'
        );

        $groups = Group::with('activity')
            ->orderBy('name')
            ->get();

        return view('user.complete-profile', [
            'groups' => $groups,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan profil pertama kali
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $this->validateProfile(
            $request
        );

        $group = Group::findOrFail(
            $validated['group_id']
        );

        DB::transaction(function () use (
            $validated,
            $group
        ) {
            UserProfile::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                ],
                [
                    'activity_id'
                        => $group->activity_id,

                    'group_id'
                        => $validated['group_id'],

                    'unit_id'
                        => $validated['unit_id'],
                ]
            );
        });

        return redirect()
            ->route('user.dashboard')
            ->with(
                'success',
                'Profil berhasil dilengkapi.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Tampilkan profil responden
    |--------------------------------------------------------------------------
    */
    public function show()
    {
        $profile = $this->getUserProfile();

        if ($this->hasCompletedSurvey()) {
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'Profil terkunci karena survei telah selesai. Admin harus melakukan Reset Account untuk membuka pengisian kembali.');
        }

        if (!$profile) {
            return redirect()
                ->route('profile.complete')
                ->with(
                    'error',
                    'Silakan lengkapi profil terlebih dahulu.'
                );
        }

        $completeProfile = CompleteProfile::where(
            'activity_id',
            $profile->activity_id
        )
            ->orderBy('id')
            ->first();

        $surveySession = SurveySession::where(
            'user_id',
            auth()->id()
        )
            ->latest('id')
            ->first();

        /*
        * Profil lengkap hanya jika semua ID terisi.
        */
        $isProfileComplete =
            filled($profile->activity_id) &&
            filled($profile->group_id) &&
            filled($profile->unit_id);

        return view('user.profile.index', [
            'profile' => $profile,
            'completeProfile' => $completeProfile,
            'surveySession' => $surveySession,
            'surveyStatus' => $this->getSurveyStatus(
                $surveySession
            ),
            'isProfileComplete' => $isProfileComplete,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman edit profil
    |--------------------------------------------------------------------------
    */
    public function edit()
    {
        $profile = $this->getUserProfile();

        if ($this->hasCompletedSurvey()) {
            return redirect()
                ->route('user.dashboard')
                ->with('error', 'Profil terkunci karena survei telah selesai. Admin harus melakukan Reset Account untuk membuka pengisian kembali.');
        }

        if (!$profile) {
            return redirect()
                ->route('profile.complete');
        }

        /*
        * Cek kelengkapan profil.
        */
        $isProfileComplete =
            filled($profile->activity_id) &&
            filled($profile->group_id) &&
            filled($profile->unit_id);

        $canSelectActivity = $this->canSelectActivity();
        $canEditProfile = $this->canEditProfile();

        if ($isProfileComplete && ! $canEditProfile) {
            return redirect()
                ->route('profile.show')
                ->with(
                    'error',
                    'Profil sudah lengkap dan tidak dapat diubah.'
                );
        }

        $activities = $canSelectActivity
            ? Activity::query()
                ->with('completeProfile')
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        $groups = Group::query()
            ->when(
                ! $canSelectActivity,
                fn ($query) => $query->where('activity_id', $profile->activity_id)
            )
            ->orderBy('name')
            ->get();

        $units = collect();

        if ($profile->group_id) {
            $units = Unit::where(
                'group_id',
                $profile->group_id
            )
                ->orderBy('name')
                ->get();
        }

        $completeProfile = CompleteProfile::where(
            'activity_id',
            $profile->activity_id
        )
            ->orderBy('id')
            ->first();

        return view('user.profile.edit', [
            'profile' => $profile,
            'groups' => $groups,
            'units' => $units,
            'completeProfile' => $completeProfile,
            'activities' => $activities,
            'canSelectActivity' => $canSelectActivity,
            'canEditProfile' => $canEditProfile,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update profil responden
    |--------------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        $profile = UserProfile::where(
            'user_id',
            auth()->id()
        )->firstOrFail();

        /*
        * Cek kelengkapan profil sebelum update.
        */
        $isProfileComplete =
            filled($profile->activity_id) &&
            filled($profile->group_id) &&
            filled($profile->unit_id);

        $canSelectActivity = $this->canSelectActivity();
        $canEditProfile = $this->canEditProfile();
        $isSurveyor = auth()->user()?->hasRole('surveyor') ?? false;

        if ($isProfileComplete && ! $canEditProfile) {
            return redirect()
                ->route('profile.show')
                ->with(
                    'error',
                    'Profil sudah lengkap dan tidak dapat diubah.'
                );
        }

        if ($this->hasCompletedSurvey()) {
            return redirect()
                ->route('user.dashboard')
                ->with(
                    'error',
                    'Profil tidak dapat diubah karena survei telah selesai. Admin harus melakukan Reset Account terlebih dahulu.'
                );
        }

        $validated = $this->validateProfileUpdate(
            $request,
            $profile
        );

        $selectedActivityId = $canSelectActivity
            ? (int) $validated['activity_id']
            : (int) $profile->activity_id;

        $group = Group::where(
            'id',
            $validated['group_id']
        )
            ->where(
                'activity_id',
                $selectedActivityId
            )
            ->firstOrFail();

        $unit = Unit::where(
            'id',
            $validated['unit_id']
        )
            ->where(
                'group_id',
                $group->id
            )
            ->firstOrFail();

        DB::transaction(function () use (
            $profile,
            $group,
            $unit,
            $canSelectActivity,
            $canEditProfile,
            $selectedActivityId
        ) {
            $selectionChanged =
                (int) $profile->activity_id !== $selectedActivityId ||
                (int) $profile->group_id !== (int) $group->id ||
                (int) $profile->unit_id !== (int) $unit->id;

            if ($canEditProfile && $selectionChanged) {
                Answer::where('user_id', auth()->id())->delete();
                if (Schema::hasTable('respondent_competitors')) {
                    RespondentCompetitor::where('user_id', auth()->id())->delete();
                }
                SurveySession::where('user_id', auth()->id())->delete();
            }

            $profile->update([
                'activity_id' => $selectedActivityId,
                'group_id' => $group->id,
                'unit_id' => $unit->id,
            ]);
        });

        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                $isSurveyor
                    ? 'Profil simulasi Surveyor berhasil diperbarui.'
                    : 'Profil responden berhasil dilengkapi.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil Unit berdasarkan Group
    |--------------------------------------------------------------------------
    */
    public function getUnitsByGroup(Group $group)
    {
        $profile = UserProfile::where(
            'user_id',
            auth()->id()
        )->firstOrFail();

        /*
        * Tolak jika Group bukan bagian Activity responden.
        */
        if (
            ! $this->canSelectActivity() &&
            (int) $group->activity_id !==
            (int) $profile->activity_id
        ) {
            return response()->json([
                'success' => false,
                'message'
                    => 'Bidang kerja tidak sesuai dengan aktivitas responden.',
            ], 403);
        }

        $units = Unit::where(
            'group_id',
            $group->id
        )
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        $completeProfile = CompleteProfile::where(
            'activity_id',
            $group->activity_id
        )
            ->orderBy('id')
            ->first();

        return response()->json([
            'success' => true,

            'data' => [
                'units' => $units,

                'labels' => [
                    'group' => $completeProfile
                            ?->group_question
                        ?? 'Bidang Kerja / Group',

                    'unit' => $completeProfile
                            ?->unit_question
                        ?? 'Unit / Jabatan',
                ],
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi profil responden
    |--------------------------------------------------------------------------
    */
    private function validateProfile(
        Request $request
    ): array {
        $canSelectActivity = $this->canSelectActivity();
        $profileActivityId = UserProfile::query()
            ->where('user_id', auth()->id())
            ->value('activity_id');
        $selectedActivityId = $canSelectActivity
            ? $request->input('activity_id')
            : $profileActivityId;

        return $request->validate([
            'activity_id' => [
                Rule::requiredIf($canSelectActivity),
                'nullable',
                'integer',
                Rule::exists('activities', 'id'),
            ],
            'group_id' => [
                'required',
                'integer',
                Rule::exists('groups', 'id')
                    ->when(
                        $selectedActivityId,
                        fn ($rule) => $rule->where('activity_id', $selectedActivityId)
                    ),
            ],

            /*
             * Unit wajib berasal dari Group yang dipilih.
             */
            'unit_id' => [
                'required',
                'integer',

                Rule::exists('units', 'id')
                    ->where(function ($query) use (
                        $request
                    ) {
                        $query->where(
                            'group_id',
                            $request->input(
                                'group_id'
                            )
                        );
                    }),
            ],
        ], [
            'group_id.required'
                => 'Bidang kerja wajib dipilih.',

            'group_id.integer'
                => 'Bidang kerja tidak valid.',

            'group_id.exists'
                => 'Bidang kerja tidak ditemukan.',

            'unit_id.required'
                => 'Unit atau jabatan wajib dipilih.',

            'unit_id.integer'
                => 'Unit atau jabatan tidak valid.',

            'unit_id.exists'
                => 'Unit tidak sesuai dengan bidang kerja yang dipilih.',
        ]);
    }

    /**
     * Reset mandiri hanya untuk akun Surveyor yang sedang login.
     */
    public function resetOwnAccount()
    {
        abort_unless(
            auth()->user()?->hasRole('surveyor'),
            403,
            'Reset Account mandiri hanya tersedia untuk Surveyor.'
        );

        $userId = (int) auth()->id();

        DB::transaction(function () use ($userId): void {
            Answer::query()
                ->where('user_id', $userId)
                ->delete();

            if (Schema::hasTable('respondent_competitors')) {
                RespondentCompetitor::query()
                    ->where('user_id', $userId)
                    ->delete();
            }

            SurveySession::query()
                ->where('user_id', $userId)
                ->delete();

            UserProfile::query()
                ->where('user_id', $userId)
                ->update([
                    'activity_id' => null,
                    'group_id' => null,
                    'unit_id' => null,
                ]);
        });

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Reset Account berhasil. Seluruh jawaban, progres survei, dan pilihan profil telah dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil profil responden login
    |--------------------------------------------------------------------------
    */
    private function getUserProfile(): ?UserProfile
    {
        return UserProfile::with([
            'activity',
            'group',
            'unit',
        ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Cek survei selesai
    |--------------------------------------------------------------------------
    */
    private function hasCompletedSurvey(): bool
    {
        return SurveySession::where(
            'user_id',
            auth()->id()
        )
            ->where('status', 'completed')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Status survei
    |--------------------------------------------------------------------------
    */
    private function getSurveyStatus(
        ?SurveySession $session
    ): array {
        if (!$session) {
            return [
                'key' => 'not_started',
                'label' => 'Belum Mulai',

                'description'
                    => 'Responden belum memulai pengisian survei.',

                'class'
                    => 'border-gray-200 bg-gray-100 text-gray-700',

                'icon'
                    => 'fa-solid fa-clock',
            ];
        }

        return match ($session->status) {
            'completed' => [
                'key' => 'completed',
                'label' => 'Selesai',

                'description'
                    => 'Seluruh proses pengisian survei telah selesai.',

                'class'
                    => 'border-green-200 bg-green-100 text-green-700',

                'icon'
                    => 'fa-solid fa-circle-check',
            ],

            'in_progress' => [
                'key' => 'in_progress',
                'label' => 'Sedang Berjalan',

                'description'
                    => 'Responden sedang melakukan pengisian survei.',

                'class'
                    => 'border-amber-200 bg-amber-100 text-amber-700',

                'icon'
                    => 'fa-solid fa-spinner',
            ],

            default => [
                'key' => 'not_started',
                'label' => 'Belum Mulai',

                'description'
                    => 'Responden belum memulai pengisian survei.',

                'class'
                    => 'border-gray-200 bg-gray-100 text-gray-700',

                'icon'
                    => 'fa-solid fa-clock',
            ],
        };
    }

    private function validateProfileUpdate(
        Request $request,
        UserProfile $profile
    ): array {
        $canSelectActivity = $this->canSelectActivity();
        $selectedActivityId = $canSelectActivity
            ? $request->input('activity_id')
            : $profile->activity_id;

        return $request->validate([
            'activity_id' => [
                Rule::requiredIf($canSelectActivity),
                'nullable',
                'integer',
                Rule::exists('activities', 'id'),
            ],
            /*
            * Group wajib berasal dari Activity responden.
            */
            'group_id' => [
                'required',
                'integer',

                Rule::exists('groups', 'id')
                    ->where(function ($query) use (
                        $selectedActivityId
                    ) {
                        $query->where(
                            'activity_id',
                            $selectedActivityId
                        );
                    }),
            ],

            /*
            * Unit wajib berasal dari Group yang dipilih.
            */
            'unit_id' => [
                'required',
                'integer',

                Rule::exists('units', 'id')
                    ->where(function ($query) use (
                        $request
                    ) {
                        $query->where(
                            'group_id',
                            $request->input(
                                'group_id'
                            )
                        );
                    }),
            ],
        ], [
            'group_id.required'
                => 'Bidang kerja wajib dipilih.',

            'group_id.integer'
                => 'Bidang kerja tidak valid.',

            'group_id.exists'
                => 'Bidang kerja tidak sesuai dengan aktivitas Anda.',

            'unit_id.required'
                => 'Unit atau jabatan wajib dipilih.',

            'unit_id.integer'
                => 'Unit atau jabatan tidak valid.',

            'unit_id.exists'
                => 'Unit tidak sesuai dengan bidang kerja yang dipilih.',
        ]);
    }

    private function canSelectActivity(): bool
    {
        $roleName = strtolower((string) auth()->user()?->role?->name);

        return $roleName === 'surveyor';
    }

    private function canEditProfile(): bool
    {
        $roleName = strtolower((string) auth()->user()?->role?->name);

        return in_array($roleName, ['user', 'surveyor'], true);
    }
}
