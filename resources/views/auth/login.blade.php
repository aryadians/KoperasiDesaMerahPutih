@extends('layouts.app')

@section('title', 'Masuk Anggota - KDKMP Digital')

@section('content')
<style>
    :root {
        --primary: #ff385c;
        --primary-dark: #d70f38;
        --primary-glow: rgba(255, 56, 92, 0.18);
        --primary-light: #fff0f2;
        --primary-muted: #ffb3c0;
    }
    
    .auth-card {
        background: var(--canvas);
        border: 1px solid var(--hairline);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
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
            <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.5px;">Selamat Datang</h1>
            <p style="color: var(--muted); font-size: 14px; margin-top: 6px; font-weight: 500;">Masuk ke portal anggota KDKMP Digital</p>
        </div>

        @if(session('success'))
            <div style="background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success); padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.4;">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger); padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.4;">
                ✕ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            <div class="form-group" style="margin: 0;">
                <label for="email" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Alamat Email</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="name@domain.com" required style="height: 50px; font-size: 14px;">
            </div>
            <div class="form-group" style="margin: 0; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" style="font-weight: 600; font-size: 13px; color: var(--ink); margin: 0;">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" style="font-size: 12px; font-weight: 600; color: var(--primary); transition: color 0.2s;" onmouseover="this.style.color='var(--primary-dark)'" onmouseout="this.style.color='var(--primary)'">Lupa Kata Sandi?</a>
                </div>
                <input type="password" name="password" id="password" class="text-input" placeholder="••••••••" required style="height: 50px; font-size: 14px; padding-right: 45px;">
                <button type="button" onclick="togglePassword()" style="position: absolute; right: 14px; top: 38px; background: none; border: none; color: var(--muted); cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; height: 24px; width: 24px;">
                    <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--muted);">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full btn-lg" style="height: 50px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-top: 10px;">
                Masuk Sekarang
            </button>
        </form>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eye-icon');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            }
        </script>

        <div style="margin-top: 32px; text-align: center; font-size: 14px; color: var(--muted); font-weight: 500; border-top: 1px solid var(--hairline-soft); padding-top: 24px;">
            Belum terdaftar sebagai anggota? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-dark)'" onmouseout="this.style.color='var(--primary)'">Daftar sekarang</a>
        </div>
    </div>
</div>
@endsection
