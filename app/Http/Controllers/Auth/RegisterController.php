<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'password.regex' => 'Password harus mengandung minimal 1 huruf kapital, 1 angka, dan 1 simbol.',
        ]);

        User::create([
            'name' => $request->username,  // ✅ Ini solusi tepat untuk kolom 'name'
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_rules' => 1, // Otomatis admin
        ]);

        return redirect()->route('login')->with('status', 'Akun berhasil dibuat. Silakan login.');
    }
}
