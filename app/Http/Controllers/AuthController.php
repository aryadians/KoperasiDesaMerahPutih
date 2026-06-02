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
        $branches = \App\Models\Branch::all();
        return view('auth.register', compact('branches'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nik' => 'required|string|size:16|unique:members',
            'alamat_desa' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'no_hp' => 'required|string|max:20',
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
                    'branch_id' => $request->branch_id,
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
                    'no_hp' => $request->no_hp,
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

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Send a simulated reset link email to the user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Alamat email tidak terdaftar dalam sistem kami.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $token = \Illuminate\Support\Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => \Carbon\Carbon::now()
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        \Illuminate\Support\Facades\Log::info("Simulasi reset password link untuk {$request->email}: " . $resetUrl);

        // Flash message and simulated link to session for local dev testing
        return redirect()->back()
            ->with('status', 'Tautan untuk meriset kata sandi telah dikirim ke email Anda.')
            ->with('simulated_link', $resetUrl);
    }

    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset_password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Reset the user's password in the database.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'email.exists' => 'Alamat email tidak terdaftar.',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau telah kedaluwarsa.']);
        }

        // Update the password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk.');
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllNotificationsRead(Request $request)
    {
        if (Auth::check()) {
            \App\Models\Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }
}
