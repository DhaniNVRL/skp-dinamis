<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserProfile
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && in_array(
            strtolower($user->role->name ?? ''),
            ['user', 'surveyor'],
            true
        )) {

            $profile = $user->profile;

            $isIncomplete =
                !$profile ||
                is_null($profile->group_id) ||
                is_null($profile->unit_id);

            if (
                $isIncomplete &&
                !$request->routeIs('profile.complete') &&
                !$request->routeIs('profile.update')
            ) {
                return redirect()->route('profile.complete');
            }
        }

        return $next($request);
    }
}
