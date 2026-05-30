@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem — KDKMP Digital')

@section('content')

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin-bottom: 6px;">
        ⚙️ Konfigurasi Sistem (.env)
    </h1>
    <p style="color: var(--muted); font-size: 15px;">
        Kelola dan ubah parameter environment aplikasi langsung dari dashboard pengurus koperasi.
    </p>
</div>

<div class="split-layout">
    
    {{-- Main configuration form --}}
    <div class="main-column">
        <div class="standard-card">
            <form action="{{ route('staff.config.update') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Menyimpan...';">
                @csrf
                
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px;">
                    Pengaturan Umum (.env)
                </h3>

                {{-- APP_NAME --}}
                <div class="form-group">
                    <label for="app_name">Nama Aplikasi (APP_NAME)</label>
                    <input type="text" name="app_name" id="app_name" class="text-input" value="{{ $configs['APP_NAME'] }}" required placeholder="Koperasi Desa Merah Putih">
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Nama resmi platform yang tampil di judul tab browser dan kop surat/cetakan.</span>
                </div>

                {{-- APP_ENV --}}
                <div class="form-group" style="margin-top: 18px;">
                    <label for="app_env">Lingkungan Sistem (APP_ENV)</label>
                    <select name="app_env" id="app_env" class="form-select" required>
                        <option value="local" {{ $configs['APP_ENV'] === 'local' ? 'selected' : '' }}>local (Pengembangan Lokal)</option>
                        <option value="staging" {{ $configs['APP_ENV'] === 'staging' ? 'selected' : '' }}>staging (Server Uji Coba)</option>
                        <option value="production" {{ $configs['APP_ENV'] === 'production' ? 'selected' : '' }}>production (Server Live/Produksi)</option>
                    </select>
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Status operasional server saat ini.</span>
                </div>

                {{-- APP_DEBUG --}}
                <div class="form-group" style="margin-top: 18px;">
                    <label for="app_debug">Mode Debugging (APP_DEBUG)</label>
                    <select name="app_debug" id="app_debug" class="form-select" required>
                        <option value="true" {{ $configs['APP_DEBUG'] === 'true' ? 'selected' : '' }}>Aktif (Tampilkan Detail Error Developer)</option>
                        <option value="false" {{ $configs['APP_DEBUG'] === 'false' ? 'selected' : '' }}>Mati (Sembunyikan Detail Error untuk Keamanan)</option>
                    </select>
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">PENTING: Selalu matikan mode debug (false) di lingkungan produksi untuk keamanan.</span>
                </div>

                {{-- SESSION_DRIVER --}}
                <div class="form-group" style="margin-top: 18px;">
                    <label for="session_driver">Penyimpanan Sesi (SESSION_DRIVER)</label>
                    <select name="session_driver" id="session_driver" class="form-select" required>
                        <option value="file" {{ $configs['SESSION_DRIVER'] === 'file' ? 'selected' : '' }}>file (Berkas Lokal - Direkomendasikan)</option>
                        <option value="database" {{ $configs['SESSION_DRIVER'] === 'database' ? 'selected' : '' }}>database (MySQL database table)</option>
                        <option value="cookie" {{ $configs['SESSION_DRIVER'] === 'cookie' ? 'selected' : '' }}>cookie (Enkripsi di Browser Warga)</option>
                    </select>
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Lokasi tempat penyimpanan sesi login dan data keranjang belanja warga.</span>
                </div>

                {{-- SESSION_LIFETIME --}}
                <div class="form-group" style="margin-top: 18px; margin-bottom: 24px;">
                    <label for="session_lifetime">Durasi Masa Sesi (Menit - SESSION_LIFETIME)</label>
                    <input type="number" name="session_lifetime" id="session_lifetime" class="text-input" value="{{ $configs['SESSION_LIFETIME'] }}" min="1" required placeholder="120">
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Batas waktu warga akan otomatis logout jika tidak ada aktivitas di sistem.</span>
                </div>

                <button type="submit" class="button-primary" style="height: 48px; border-radius: 100px;">
                    💾 Simpan Konfigurasi &amp; Bersihkan Cache
                </button>
            </form>
        </div>
    </div>

    {{-- Info Card Sidebar --}}
    <div class="sticky-rail">
        <div class="reservation-card" style="border-color: var(--warning-border); background: var(--warning-bg);">
            <div>
                <div style="font-size: 32px; margin-bottom: 12px; animation: emoji-bounce 3s ease-in-out infinite;">⚠️</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--warning);">Informasi Penting</h3>
                <p style="font-size: 13px; color: var(--warning); opacity: 0.9; line-height: 1.6;">
                    Pengaturan di sini akan langsung menimpa data konfigurasi `.env` Laravel saat aplikasi berjalan (*runtime configuration override*). 
                </p>
                <p style="font-size: 13px; color: var(--warning); opacity: 0.9; line-height: 1.6; margin-top: 10px;">
                    Setelah konfigurasi disimpan, sistem akan secara otomatis memicu perintah <strong>optimize:clear</strong> untuk membersihkan data config yang ter-cache agar perubahan langsung aktif seketika.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
