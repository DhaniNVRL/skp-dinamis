<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $profile,
            'surveySession' => $surveySession,
        ]);
    }
}
