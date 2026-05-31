@extends('layouts.app')

@section('title', 'Reset Kata Sandi - KDKMP Digital')

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
            <h1 style="font-size: 24px; font-weight: 800; color: var(--ink); letter-spacing: -0.5px;">Atur Ulang Sandi</h1>
            <p style="color: var(--muted); font-size: 14px; margin-top: 6px; font-weight: 500; line-height: 1.4;">
                Buat kata sandi baru untuk akun KDKMP Anda.
            </p>
        </div>

        @if($errors->any())
            <div style="background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger); padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.4;">
                ✕ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group" style="margin: 0;">
                <label for="email" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Konfirmasi Alamat Email</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="name@domain.com" required value="{{ $email ?? old('email') }}" style="height: 50px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0; position: relative;">
                <label for="password" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" class="text-input" placeholder="Min. 8 karakter" required style="height: 50px; font-size: 14px; padding-right: 45px;">
                <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 14px; top: 38px; background: none; border: none; color: var(--muted); cursor: pointer; padding: 0;">
                    👁️
                </button>
            </div>

            <div class="form-group" style="margin: 0; position: relative;">
                <label for="password_confirmation" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Ulangi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="text-input" placeholder="Konfirmasi sandi" required style="height: 50px; font-size: 14px; padding-right: 45px;">
                <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" style="position: absolute; right: 14px; top: 38px; background: none; border: none; color: var(--muted); cursor: pointer; padding: 0;">
                    👁️
                </button>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full btn-lg" style="height: 50px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-top: 10px;">
                Perbarui Kata Sandi
            </button>
        </form>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }
</script>
@endsection
