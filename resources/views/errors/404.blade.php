@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - KDKMP')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center; padding: 40px 20px;">
    <div style="font-size: 80px; margin-bottom: 24px; animation: float-emoji 3s ease-in-out infinite;">🧭</div>
    <h1 style="font-size: 36px; font-weight: 800; color: var(--ink); margin-bottom: 12px; letter-spacing: -1px;">404 - Halaman Tidak Ditemukan</h1>
    <p style="font-size: 16px; color: var(--muted); max-width: 500px; margin: 0 auto 32px; line-height: 1.6;">
        Maaf, halaman atau produk yang Anda cari sepertinya sudah dipindahkan, dihapus, atau tidak pernah ada di gerai digital KDKMP.
    </p>
    <div style="display: flex; gap: 16px; flex-wrap: wrap; justify-content: center;">
        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-xl" style="border-radius: 100px;">
            🛒 Kembali Belanja
        </a>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('catalog.index') }}" class="btn btn-secondary btn-xl" style="border-radius: 100px;">
            &larr; Halaman Sebelumnya
        </a>
    </div>
</div>
@endsection
