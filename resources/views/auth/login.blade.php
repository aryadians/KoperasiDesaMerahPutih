@extends('layouts.app')

@section('title', 'Login - KDKMP Digital')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 70vh; padding: 20px;">
    <div class="card reveal-scale" style="width: 100%; max-width: 420px; box-shadow: var(--shadow-lg); border: 1.5px solid var(--hairline);">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 48px; margin-bottom: 12px;">🔐</div>
            <h1 style="font-size: 24px; font-weight: 800; color: var(--ink);">Masuk ke Akun</h1>
            <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">Gerai Digital Koperasi Desa Merah Putih</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" name="email" id="email" class="text-input" placeholder="contoh@kdkmp.org" required>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" name="password" id="password" class="text-input" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full btn-xl" style="font-weight: 700; margin-top: 16px;">
                Masuk Sekarang
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; font-size: 14px; color: var(--body);">
            Belum punya akun anggota? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
