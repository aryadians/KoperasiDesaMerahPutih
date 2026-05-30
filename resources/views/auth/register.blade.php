@extends('layouts.app')

@section('title', 'Daftar Anggota KDKMP')

@section('content')
<div style="max-width: 500px; margin: 40px auto; padding: 32px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); box-shadow: var(--shadow-tier);">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 24px; text-align: center;">Daftar Anggota Koperasi</h2>
    
    <form action="{{ route('register') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="text-input" placeholder="Nama Lengkap Anda" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" class="text-input" placeholder="nama@email.com" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="nik">Nomor Induk Kependudukan (NIK)</label>
            <input type="text" name="nik" id="nik" class="text-input" placeholder="16 Digit NIK KTP Anda" value="{{ old('nik') }}" maxlength="16" required>
        </div>

        <div class="form-group">
            <label for="alamat_desa">Alamat Rumah / Desa</label>
            <textarea name="alamat_desa" id="alamat_desa" class="text-input" style="height: 100px; resize: none; padding: 12px 16px;" placeholder="Tuliskan alamat lengkap RT/RW desa Anda" required>{{ old('alamat_desa') }}</textarea>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" name="password" id="password" class="text-input" placeholder="Minimal 8 karakter" required>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="text-input" placeholder="Ketik ulang kata sandi" required>
        </div>

        <button type="submit" class="button-primary" style="margin-bottom: 16px;">Daftar Keanggotaan</button>
    </form>
    
    <div style="text-align: center; font-size: 14px; color: var(--colors-muted); margin-top: 16px;">
        Sudah terdaftar sebagai anggota? <a href="{{ route('login') }}" style="color: var(--colors-primary); font-weight: 600; text-decoration: underline;">Masuk di sini</a>
    </div>
</div>
@endsection
