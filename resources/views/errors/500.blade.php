@extends('layouts.app')

@section('title', 'Terjadi Kesalahan - KDKMP')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center; padding: 40px 20px;">
    <div style="font-size: 80px; margin-bottom: 24px; animation: float-emoji 3s ease-in-out infinite;">🛠️</div>
    <h1 style="font-size: 36px; font-weight: 800; color: var(--ink); margin-bottom: 12px; letter-spacing: -1px;">500 - Gangguan Sistem</h1>
    <p style="font-size: 16px; color: var(--muted); max-width: 500px; margin: 0 auto 32px; line-height: 1.6;">
        Mohon maaf, sedang terjadi gangguan pada server Koperasi Desa. Tim IT kami sedang memperbaikinya. Silakan coba beberapa saat lagi.
    </p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-xl" style="border-radius: 100px;">
        🔄 Segarkan Halaman Utama
    </a>
</div>
@endsection