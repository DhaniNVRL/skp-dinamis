<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            $role = strtolower($user->role->name ?? '');

            switch ($role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'surveyor':
                    return redirect()->intended('/surveyor/dashboard');
                case 'pm':
                    return redirect()->intended('/pm/dashboard');
                case 'user':
                    return redirect()->intended('/home');
                default:
                    Auth::logout();
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
