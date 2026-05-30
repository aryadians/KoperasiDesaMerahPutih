<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nik' => 'required|string|size:16|unique:members',
            'alamat_desa' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create User
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'anggota',
                    'status' => 'active',
                ]);

                // 2. Generate Nomor Anggota (e.g. MBR-YYYYMMDD-XXXX)
                $nomorAnggota = 'MBR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                // 3. Create Member
                Member::create([
                    'user_id' => $user->id,
                    'nik' => $request->nik,
                    'nomor_anggota' => $nomorAnggota,
                    'alamat_desa' => $request->alamat_desa,
                    'tanggal_bergabung' => date('Y-m-d'),
                    'total_poin' => 0,
                    'status_aktif' => true,
                ]);

                // Automatically deposit Simpanan Pokok (compulsory joining fee) if needed, 
                // for this demo we'll let them add it later or seed it.
            });

            return redirect()->route('login')->with('success', 'Registrasi anggota berhasil! Silakan login.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan registrasi: ' . $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('catalog.index');
    }
}
