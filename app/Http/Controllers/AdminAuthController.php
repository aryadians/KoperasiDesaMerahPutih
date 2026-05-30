<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login page.
     * Only accessible if not already logged in as a staff/admin.
     */
    public function showLogin()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'pengurus', 'kasir', 'staff'])) {
            return redirect()->route('staff.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Handle admin login attempt.
     * Only allows roles: admin, pengurus, kasir, staff.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Block non-staff roles from admin panel
            if (!in_array($user->role, ['admin', 'pengurus', 'kasir', 'staff'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Panel ini hanya untuk Admin, Pengurus, dan Staf Koperasi.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak valid. Silakan periksa kembali.',
        ])->onlyInput('email');
    }

    /**
     * Logout admin user and redirect to admin login.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
