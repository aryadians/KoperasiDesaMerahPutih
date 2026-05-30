@extends('layouts.app')

@section('title', 'Masuk Anggota — KDKMP Digital')

@section('content')
<div style="max-width: 460px; margin: 40px auto;">

    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 32px;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--primary), #ff7c9e); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 8px 24px rgba(255,56,92,0.25);">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="white">
                <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
            </svg>
        </div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Masuk sebagai Anggota</h1>
        <p style="font-size: 14px; color: var(--muted);">Masuk untuk menikmati harga khusus anggota dan layanan koperasi</p>
    </div>

    {{-- Form Card --}}
    <div style="background: var(--canvas); border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 32px; box-shadow: var(--shadow-tier);">

        {{-- Error display --}}
        @if($errors->any())
            <div class="alert-notification alert-danger" style="display: flex; align-items: center; gap: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="member-login-form">
            @csrf

            <div class="form-group">
                <label for="email">Alamat Email Anggota</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="nama@email.com" value="{{ old('email') }}" autocomplete="email" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="password">Kata Sandi</label>
                <input type="password" name="password" id="password" class="text-input" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
            </div>

            <button type="submit" class="button-primary" style="margin-bottom: 16px;">Masuk ke Akun Saya</button>
        </form>

        <div style="text-align: center; font-size: 14px; color: var(--muted);">
            Belum punya akun anggota?
            <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: underline; margin-left: 4px;">Daftar Sekarang</a>
        </div>
    </div>

    {{-- Admin link hint --}}
    <div style="text-align: center; margin-top: 24px;">
        <p style="font-size: 13px; color: var(--muted);">
            Anda Admin / Pengurus / Staf Koperasi?
            <a href="{{ route('admin.login') }}" style="color: var(--ink); font-weight: 600; text-decoration: underline;">Masuk ke Panel Admin →</a>
        </p>
    </div>

</div>
@endsection
