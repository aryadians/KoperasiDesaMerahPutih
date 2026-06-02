@extends('layouts.admin')

@section('title', 'Konfigurasi Sistem — KDKMP Digital')
@section('page-title', 'Konfigurasi')

@section('content')

<style>
    /* View-Specific 3D Polish Styles */
    .btn-3d-primary {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all var(--t-fast) var(--ease-out);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-3d-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    }
    .btn-3d-primary:active {
        transform: translateY(0);
    }

    .config-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .config-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }

    .config-info-card {
        background: linear-gradient(135deg, #fffbeb, #fef3c7) !important;
        border: 1px solid rgba(245, 158, 11, 0.2) !important;
        box-shadow: 0 10px 30px -10px rgba(245, 158, 11, 0.12),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .config-info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(245, 158, 11, 0.2), inset 0 1px 0 #ffffff !important;
    }

    /* Form Input Polish */
    .form-group label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--muted);
        margin-bottom: 6px;
        display: block;
    }
    .text-input, .form-select {
        border-radius: var(--r-sm);
        border: 1.5px solid var(--hairline);
        background: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        transition: all var(--t-fast) var(--ease-out);
        height: 44px;
        font-size: 13.5px;
    }
    .text-input:focus, .form-select:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }
    
    @keyframes emoji-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
</style>

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div class="reveal" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Konfigurasi <span style="color: var(--primary);">Sistem (.env)</span></h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">
            ⚙️ Kelola dan ubah parameter environment aplikasi langsung dari dashboard pengurus koperasi.
        </p>
    </div>
</div>

<div class="split-layout">
    
    {{-- Main configuration form --}}
    <div class="main-column">
        <div class="config-card">
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
                <div class="form-group" style="margin-top: 18px;">
                    <label for="session_lifetime">Durasi Masa Sesi (Menit - SESSION_LIFETIME)</label>
                    <input type="number" name="session_lifetime" id="session_lifetime" class="text-input" value="{{ $configs['SESSION_LIFETIME'] }}" min="1" required placeholder="120">
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Batas waktu warga akan otomatis logout jika tidak ada aktivitas di sistem.</span>
                </div>

                <h3 style="font-size: 18px; font-weight: 700; margin-top: 32px; margin-bottom: 20px; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; color: var(--success);">
                    Pengaturan Simpanan &amp; Iuran Anggota
                </h3>

                {{-- IURAN_WAJIB_NOMINAL --}}
                <div class="form-group" style="margin-top: 18px;">
                    <label for="iuran_wajib_nominal">Nominal Iuran Wajib Bulanan (Rp - IURAN_WAJIB_NOMINAL)</label>
                    <input type="number" name="iuran_wajib_nominal" id="iuran_wajib_nominal" class="text-input" value="{{ $configs['IURAN_WAJIB_NOMINAL'] }}" min="0" required placeholder="50000">
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Jumlah nominal yang ditarik otomatis (autodebet) setiap bulannya dari simpanan sukarela anggota.</span>
                </div>

                {{-- IURAN_POKOK_NOMINAL --}}
                <div class="form-group" style="margin-top: 18px; margin-bottom: 24px;">
                    <label for="iuran_pokok_nominal">Nominal Iuran Pokok Pendaftaran (Rp - IURAN_POKOK_NOMINAL)</label>
                    <input type="number" name="iuran_pokok_nominal" id="iuran_pokok_nominal" class="text-input" value="{{ $configs['IURAN_POKOK_NOMINAL'] }}" min="0" required placeholder="100000">
                    <span style="font-size: 12px; color: var(--muted); margin-top: 4px;">Uang pangkal yang dibayarkan satu kali saat pertama kali menjadi anggota koperasi.</span>
                </div>

                <button type="submit" class="btn-3d-primary" style="width: 100%; height: 48px; border-radius: 100px;">
                    💾 Simpan Konfigurasi &amp; Bersihkan Cache
                </button>
            </form>
        </div>
    </div>

    {{-- Info Card Sidebar --}}
    <div class="sticky-rail">
        <div class="config-info-card">
            <div>
                <div style="font-size: 32px; margin-bottom: 12px; animation: emoji-bounce 3s ease-in-out infinite;">⚠️</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: #b45309;">Informasi Penting</h3>
                <p style="font-size: 13px; color: #b45309; opacity: 0.9; line-height: 1.6;">
                    Pengaturan di sini akan langsung menimpa data konfigurasi `.env` Laravel saat aplikasi berjalan (*runtime configuration override*). 
                </p>
                <p style="font-size: 13px; color: #b45309; opacity: 0.9; line-height: 1.6; margin-top: 10px;">
                    Setelah konfigurasi disimpan, sistem akan secara otomatis memicu perintah <strong>optimize:clear</strong> untuk membersihkan data config yang ter-cache agar perubahan langsung aktif seketika.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
