<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SurveySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::lower($credentials['username']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'username' => 'Terlalu banyak percobaan login. Coba lagi dalam '
                    .RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        // Coba autentikasi user
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();
            $role = strtolower($user->role->name ?? '');

            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'surveyor':
                    return redirect()->route('surveyor.dashboard');
                case 'pm':
                    return redirect()->route('pm.dashboard');
                case 'monitoring':
                    return redirect()->route('admin.dashboard');
                case 'user':
                     $profile = $user->profile;

                    if (
                        !$profile ||
                        is_null($profile->group_id) ||
                        is_null($profile->unit_id)
                    ) {
                        return redirect()->route('profile.complete');
                    }

                    // ==========================
                    // Cek status survey
                    // ==========================
                    $session = SurveySession::where('user_id', $user->id)->first();

                    if ($session && $session->status === 'completed') {

                        Auth::logout();

                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()
                            ->route('login')
                            ->withErrors([
                                'username' => 'Survey Anda telah selesai. Akun ini sudah tidak dapat digunakan kembali.'
                            ]);
                    }

                    return redirect()->route('user.dashboard');
                default:
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'username' => 'Role tidak dikenali.',
                    ]);
            }
        }

        RateLimiter::hit($throttleKey, 60);

        // Kalau gagal login
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
