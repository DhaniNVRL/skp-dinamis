<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SurveySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Coba autentikasi user
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            // dd([
            //     'username' => $user->username,
            //     'role_id' => $user->role_id,
            //     'role' => $user->role?->name,
            // ]);
            $role = strtolower($user->role->name ?? '');

            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'surveyor':
                    return redirect()->route('surveyor.dashboard');
                case 'pm':
                    return redirect()->route('pm.dashboard');
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
