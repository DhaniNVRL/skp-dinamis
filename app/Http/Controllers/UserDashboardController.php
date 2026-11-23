<?php

namespace App\Http\Controllers;

use App\Models\CompleteProfile;
use App\Models\SurveySession;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $profile = UserProfile::query()
            ->with([
                'activity',
                'group',
                'unit',
            ])
            ->where('user_id', $user->id)
            ->first();

        $surveySession = SurveySession::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $completeProfile = $profile?->activity_id
            ? Cache::remember(
                $this->completeProfileCacheKey((int) $profile->activity_id),
                now()->addHours(6),
                fn () => CompleteProfile::query()
                    ->where('activity_id', $profile->activity_id)
                    ->first() ?: false
            )
            : null;

        // Cache stores cannot distinguish a missing key from a cached null.
        // A false sentinel keeps activities without a custom label from
        // querying the database on every respondent dashboard request.
        $completeProfile = $completeProfile === false
            ? null
            : $completeProfile;

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'surveySession' => $surveySession,
            'completeProfile' => $completeProfile,
        ]);
    }

    private function completeProfileCacheKey(int $activityId): string
    {
        return "respondent-dashboard:complete-profile:activity:{$activityId}";
    }
}
