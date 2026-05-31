@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - KDKMP Digital')

@section('content')
<style>
    :root {
        --primary: #ff385c;
        --primary-dark: #d70f38;
        --primary-glow: rgba(255, 56, 92, 0.18);
        --primary-light: #fff0f2;
    }
    
    .auth-card {
        background: var(--canvas);
        border: 1px solid var(--hairline);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: var(--shadow-lg);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .auth-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 48px rgba(255, 56, 92, 0.06), 0 16px 40px rgba(0, 0, 0, 0.08);
    }

    .form-group input {
        border-radius: 12px;
        border-color: var(--hairline);
        transition: all 0.2s ease;
    }

    .form-group input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-glow);
    }
    
    .logo-container {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px auto;
        color: var(--primary);
        font-size: 28px;
        box-shadow: 0 8px 16px rgba(255, 56, 92, 0.1);
    }
</style>

<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 40px 20px;">
    <div class="auth-card reveal-scale">
        <div style="text-align: center; margin-bottom: 32px;">
            <div class="logo-container">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                </svg>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--ink); letter-spacing: -0.5px;">Lupa Kata Sandi?</h1>
            <p style="color: var(--muted); font-size: 14px; margin-top: 6px; font-weight: 500; line-height: 1.4;">
                Masukkan email terdaftar Anda untuk memulihkan kata sandi.
            </p>
        </div>

        @if(session('status'))
            <div style="background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success); padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.4;">
                ✓ {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger); padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.4;">
                ✕ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            <div class="form-group" style="margin: 0;">
                <label for="email" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Alamat Email</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="name@domain.com" required value="{{ old('email') }}" style="height: 50px; font-size: 14px;">
            </div>
            
            <button type="submit" class="btn btn-primary btn-full btn-lg" style="height: 50px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-top: 10px;">
                Kirim Tautan Pemulihan
            </button>
        </form>

        <!-- Simulated link for developers (local environment helper) -->
        @if(session('simulated_link'))
            <div style="margin-top: 24px; background: var(--info-bg); border: 1px solid var(--info-border); padding: 16px; border-radius: 16px; text-align: left; animation: reveal-scale 0.4s var(--ease-spring);">
                <h4 style="font-size: 13px; font-weight: 700; color: var(--info); display: flex; align-items: center; gap: 6px; margin: 0 0 6px 0;">📱 Simulasi Email (Local Testing)</h4>
                <p style="font-size: 11px; color: var(--muted); margin: 0 0 12px 0; line-height: 1.4;">Di server lokal, email ditulis ke log driver. Klik tombol di bawah untuk langsung membuka form reset password:</p>
                <a href="{{ session('simulated_link') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; width: 100%; justify-content: center; height: 38px; border-radius: 10px; font-weight: 700; font-size: 12px; border-color: var(--info-border); color: var(--info);">🔑 Reset Password Sekarang</a>
            </div>
        @endif

        <div style="margin-top: 32px; text-align: center; font-size: 14px; color: var(--muted); font-weight: 500; border-top: 1px solid var(--hairline-soft); padding-top: 24px;">
            Kembali ke <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-dark)'" onmouseout="this.style.color='var(--primary)'">Halaman Login</a>
        </div>
    </div>
</div>
@endsection
