@extends('layouts.app')

@section('title', 'Masuk Ke KDKMP Digital')

@section('content')
<div style="max-width: 450px; margin: 40px auto; padding: 32px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); box-shadow: var(--shadow-tier);">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 24px; text-align: center;">Masuk KDKMP</h2>
    
    <form action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" name="email" id="email" class="text-input" placeholder="nama@email.com" value="{{ old('email') }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label for="password">Kata Sandi</label>
            <input type="password" name="password" id="password" class="text-input" placeholder="Masukkan kata sandi" required>
        </div>

        <button type="submit" class="button-primary" style="margin-bottom: 16px;">Masuk</button>
    </form>
    
    <div style="text-align: center; font-size: 14px; color: var(--colors-muted); margin-top: 16px;">
        Belum mendaftar sebagai anggota? <a href="{{ route('register') }}" style="color: var(--colors-primary); font-weight: 600; text-decoration: underline;">Daftar Sekarang</a>
    </div>
</div>
@endsection
