@extends('layouts.app')

@section('title', 'Daftar Anggota - KDKMP Digital')

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
        max-width: 500px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .auth-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 48px rgba(255, 56, 92, 0.06), 0 16px 40px rgba(0, 0, 0, 0.08);
    }

    .form-group input, .form-group textarea, .form-group select {
        border-radius: 12px;
        border-color: var(--hairline);
        transition: all 0.2s ease;
    }

    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
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

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 40px 20px;">
    <div class="auth-card reveal-scale">
        <div style="text-align: center; margin-bottom: 32px;">
            <div class="logo-container">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="currentColor">
                    <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                </svg>
            </div>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); letter-spacing: -0.5px;">Daftar Anggota</h1>
            <p style="color: var(--muted); font-size: 14px; margin-top: 6px; font-weight: 500;">Gabung ke KDKMP Digital & nikmati layanan koperasi modern</p>
        </div>

        <form action="{{ route('register') }}" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
            @csrf
            
            <div class="form-group" style="margin: 0;">
                <label for="name" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="text-input" placeholder="Nama Lengkap Anda" value="{{ old('name') }}" required style="height: 48px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="email" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Alamat Email</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="name@domain.com" value="{{ old('email') }}" required style="height: 48px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="nik" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" name="nik" id="nik" class="text-input" placeholder="16 Digit NIK KTP Anda" value="{{ old('nik') }}" maxlength="16" required style="height: 48px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="no_hp" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">No. WhatsApp</label>
                <input type="text" name="no_hp" id="no_hp" class="text-input" placeholder="Contoh: 081234567890" value="{{ old('no_hp') }}" required style="height: 48px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="branch_id" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Desa Keanggotaan</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <select name="branch_id" id="branch_id" class="form-select" required style="appearance: none; -webkit-appearance: none; width: 100%; height: 48px; padding: 0 32px 0 14px; border: 1.5px solid var(--hairline); border-radius: 12px; background: var(--canvas); font-size: 14px; color: var(--ink); cursor: pointer; outline: none;">
                        <option value="" disabled selected style="color: var(--muted);">Pilih Desa Tempat Tinggal Anda</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    <span style="position: absolute; right: 14px; pointer-events: none; font-size: 10px; color: var(--muted);">▼</span>
                </div>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="alamat_desa" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Alamat Rumah Lengkap</label>
                <textarea name="alamat_desa" id="alamat_desa" class="text-input" style="height: 80px; resize: none; padding: 12px 14px; font-size: 14px;" placeholder="Tuliskan alamat lengkap RT/RW desa Anda" required>{{ old('alamat_desa') }}</textarea>
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="password" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Kata Sandi</label>
                <input type="password" name="password" id="password" class="text-input" placeholder="Minimal 8 karakter" required style="height: 48px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin: 0;">
                <label for="password_confirmation" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; color: var(--ink);">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="text-input" placeholder="Ketik ulang kata sandi" required style="height: 48px; font-size: 14px;">
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="height: 50px; border-radius: 12px; font-weight: 700; font-size: 15px; margin-top: 10px;">
                Daftar Keanggotaan
            </button>
        </form>

        <div style="margin-top: 32px; text-align: center; font-size: 14px; color: var(--muted); font-weight: 500; border-top: 1px solid var(--hairline-soft); padding-top: 24px;">
            Sudah terdaftar sebagai anggota? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-dark)'" onmouseout="this.style.color='var(--primary)'">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
