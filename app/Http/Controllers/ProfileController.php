<?php

namespace App\Http\Controllers;

use App\Models\CompleteProfile;
use App\Models\Group;
use App\Models\SurveySession;
use App\Models\Unit;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            return redirect()->route('profile.show');
        }

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

        if ($isProfileComplete) {
            return redirect()
                ->route('profile.show')
                ->with(
                    'error',
                    'Profil sudah lengkap dan tidak dapat diubah.'
                );
        }

        $groups = Group::where(
            'activity_id',
            $profile->activity_id
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

        if ($isProfileComplete) {
            return redirect()
                ->route('profile.show')
                ->with(
                    'error',
                    'Profil sudah lengkap dan tidak dapat diubah.'
                );
        }

        if ($this->hasCompletedSurvey()) {
            return redirect()
                ->route('profile.show')
                ->with(
                    'error',
                    'Profil tidak dapat diubah karena survei telah selesai.'
                );
        }

        $validated = $this->validateProfileUpdate(
            $request,
            $profile
        );

        $group = Group::where(
            'id',
            $validated['group_id']
        )
            ->where(
                'activity_id',
                $profile->activity_id
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
            $unit
        ) {
            $profile->update([
                'activity_id' => $profile->activity_id,
                'group_id' => $group->id,
                'unit_id' => $unit->id,
            ]);
        });

        return redirect()
            ->route('profile.show')
            ->with(
                'success',
                'Profil responden berhasil dilengkapi.'
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
            $profile->activity_id
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
        return $request->validate([
            'group_id' => [
                'required',
                'integer',
                Rule::exists('groups', 'id'),
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
        return $request->validate([
            /*
            * Group wajib berasal dari Activity responden.
            */
            'group_id' => [
                'required',
                'integer',

                Rule::exists('groups', 'id')
                    ->where(function ($query) use (
                        $profile
                    ) {
                        $query->where(
                            'activity_id',
                            $profile->activity_id
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
}