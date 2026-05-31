@extends('layouts.app')

@php
    $currentBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
    $currentBranch = \App\Models\Branch::find($currentBranchId) ?? \App\Models\Branch::first();
@endphp

@section('title', 'KDKMP Digital — Koperasi ' . $currentBranch->name)

@section('content')

{{-- ═══════════════════════ CUSTOM STYLES & ANIMATIONS ═══════════════════════ --}}
<style>
    /* 2D Float & Shimmer Animations */
    @keyframes float-slow {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(2deg); }
    }
    @keyframes float-medium {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(-3deg); }
    }
    @keyframes pulse-soft {
        0%, 100% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.03); opacity: 1; }
    }
    @keyframes shimmer {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .animated-float-slow {
        animation: float-slow 6s ease-in-out infinite;
    }
    .animated-float-medium {
        animation: float-medium 4.5s ease-in-out infinite;
    }
    .pulse-glow {
        animation: pulse-soft 3s ease-in-out infinite;
    }

    /* Glassmorphism Classes */
    .glass-panel {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .glass-card-dark {
        background: rgba(17, 24, 39, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }

    /* Interactive Calculator Styling */
    .range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        border-radius: var(--r-full);
        background: var(--surface-md);
        outline: none;
        transition: background 0.3s;
    }
    .range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary);
        cursor: pointer;
        box-shadow: 0 0 10px rgba(228, 0, 43, 0.4);
        transition: transform 0.1s var(--ease-bounce);
    }
    .range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.25);
    }

    /* Map card active state */
    .map-branch-card {
        cursor: pointer;
        transition: all 0.3s var(--ease-bounce);
        border-left: 4px solid transparent;
    }
    .map-branch-card.active {
        border-left-color: var(--primary);
        background: var(--primary-light) !important;
        transform: translateX(6px);
    }
</style>

{{-- ═══════════════════════ HERO SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 50%, #450a0a 100%); color: white; border-radius: var(--r-xl); padding: 75px 40px 60px 40px; text-align: center; margin-bottom: 48px; position: relative; overflow: hidden; box-shadow: var(--shadow-xl);">
    <!-- Decorative background blobs -->
    <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; border-radius: 50%; background: rgba(228, 0, 43, 0.25); filter: blur(50px); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -80px; left: -80px; width: 320px; height: 320px; border-radius: 50%; background: rgba(0, 91, 170, 0.2); filter: blur(60px); pointer-events: none;"></div>
    
    <div style="position: relative; z-index: 2; max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
        <span class="pulse-glow" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: white; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; padding: 6px 20px; border-radius: var(--r-full); margin-bottom: 24px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            🇮🇩 Koperasi Desa Merah Putih Digital Engine
        </span>
        
        <h1 style="font-size: 46px; font-weight: 800; line-height: 1.15; margin-bottom: 20px; letter-spacing: -1.5px; text-shadow: 0 4px 16px rgba(0,0,0,0.2);">
            Modernisasi Ritel Desa &amp;<br>
            <span style="background: linear-gradient(90deg, #ffc4d0 0%, #ffffff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Kedaulatan Pangan Mandiri</span>
        </h1>
        
        <p style="font-size: 17px; line-height: 1.6; opacity: 0.92; margin-bottom: 36px; max-width: 720px; font-weight: 400;">
            Platform ekosistem digital Koperasi {{ $currentBranch->name }}. Memadukan efisiensi belanja harian swalayan ritel modern dengan pembiayaan mikro anggota serta penyerapan komoditas pertanian lokal.
        </p>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 48px;">
            <a href="{{ route('catalog.index') }}" class="btn btn-xl" style="background: white; color: var(--primary); font-weight: 800; border-radius: 100px; padding: 0 36px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); transition: transform 0.2s var(--ease-bounce); display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05) translateY(-2px)'" onmouseout="this.style.transform='scale(1) translateY(0)'">
                🛒 Mulai Belanja Sembako
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-xl" style="background: rgba(255,255,255,0.08); color: white; border: 1.5px solid rgba(255,255,255,0.3); font-weight: 600; border-radius: 100px; padding: 0 36px; backdrop-filter: blur(8px); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.18)'; this.style.transform='scale(1.05) translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'; this.style.transform='scale(1) translateY(0)'">
                    ✨ Gabung Anggota
                </a>
            @endguest
        </div>
    </div>

    {{-- HERO STATISTICS PANEL --}}
    <div class="glass-panel" style="border-radius: var(--r-lg); padding: 24px 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 20px; text-align: center; color: var(--ink); margin-top: 10px; max-width: 900px; margin-left: auto; margin-right: auto; box-shadow: var(--shadow-lg);">
        <div>
            <div style="font-size: 26px; font-weight: 800; color: var(--primary); margin-bottom: 2px;" data-counter data-target="1420" data-suffix="+">0</div>
            <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--muted); letter-spacing: 0.5px;">Anggota Aktif</div>
        </div>
        <div style="border-left: 1px solid var(--hairline);">
            <div style="font-size: 26px; font-weight: 800; color: var(--secondary); margin-bottom: 2px;" data-counter data-target="45" data-suffix=" Ton">0</div>
            <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--muted); letter-spacing: 0.5px;">Komoditas Terserap</div>
        </div>
        <div style="border-left: 1px solid var(--hairline);">
            <div style="font-size: 26px; font-weight: 800; color: var(--success); margin-bottom: 2px;" data-counter data-target="152000000" data-prefix="Rp ">Rp 0</div>
            <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--muted); letter-spacing: 0.5px;">Dana SHU Dibagikan</div>
        </div>
        <div style="border-left: 1px solid var(--hairline);">
            <div style="font-size: 26px; font-weight: 800; color: var(--ink); margin-bottom: 2px;" data-counter data-target="2" data-suffix=" Desa">0</div>
            <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--muted); letter-spacing: 0.5px;">Gerai Cabang</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ INTERACTIVE LOYALTY & SHU CALCULATOR ═══════════════════════ --}}
<div class="reveal-up" style="background: linear-gradient(135deg, var(--surface) 0%, var(--surface-md) 100%); border: 1px solid var(--hairline); border-radius: var(--r-xl); padding: 48px 40px; margin-bottom: 64px;">
    <div class="grid-2" style="gap: 40px; align-items: center;">
        <div>
            <span class="animated-float-slow" style="font-size: 28px; display: inline-block; margin-bottom: 12px;">📊</span>
            <h2 style="font-size: 30px; font-weight: 800; color: var(--ink); line-height: 1.25; letter-spacing: -0.8px; margin-bottom: 16px;">
                Simulasikan Dividen SHU &amp; Poin Loyalitas Belanja Anda
            </h2>
            <p style="font-size: 14.5px; color: var(--body); line-height: 1.6; margin-bottom: 20px;">
                Sebagai koperasi berdikari, kami menerapkan prinsip transparansi. Keuntungan dari pembelanjaan ritel sembako harian Anda di gerai KDKMP akan dialokasikan kembali kepada Anda di akhir tahun sebagai dana <strong>Sisa Hasil Usaha (SHU)</strong>.
            </p>
            <div style="background: white; border-radius: var(--r-md); padding: 20px; border: 1px solid var(--hairline); box-shadow: var(--shadow-sm);">
                <h4 style="font-weight: 700; color: var(--ink); margin-top: 0; margin-bottom: 12px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    🛡️ Keuntungan Eksklusif Keanggotaan:
                </h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: var(--muted); display: flex; flex-direction: column; gap: 8px;">
                    <li><strong>Harga Spesial Anggota:</strong> Diskon hingga 15% dari harga konsumen umum.</li>
                    <li><strong>Loyalty Point Multiplier:</strong> Kumpulkan poin belanja yang dapat ditukarkan voucer.</li>
                    <li><strong>Pemberdayaan SHU:</strong> Hak suara penuh dalam Rapat Anggota Tahunan (RAT).</li>
                </ul>
            </div>
        </div>

        {{-- CALCULATOR WIDGET CARD --}}
        <div class="glass-panel" style="border-radius: var(--r-lg); padding: 32px; box-shadow: var(--shadow-xl); border: 1.5px solid white;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-top: 0; margin-bottom: 24px; text-align: center; border-bottom: 1px solid var(--hairline); padding-bottom: 14px;">
                🧮 KDKMP Benefits Calculator
            </h3>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="simulation-range" style="font-size: 13px; font-weight: 700; color: var(--ink);">Estimasi Belanja Sembako/Bulan</label>
                    <span id="range-val-label" style="font-size: 14px; font-weight: 800; color: var(--primary);">Rp 1.000.000</span>
                </div>
                <input type="range" id="simulation-range" class="range-slider" min="100000" max="3000000" step="50000" value="1000000" oninput="calculateSimulator(this.value)">
                <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--muted); margin-top: 6px;">
                    <span>Rp 100 Ribu</span>
                    <span>Rp 3 Juta</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div style="background: rgba(228, 0, 43, 0.04); border: 1px solid rgba(228, 0, 43, 0.1); border-radius: var(--r-sm); padding: 14px; text-align: center;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Poin Belanja/Bulan</div>
                    <div id="sim-points" style="font-size: 20px; font-weight: 800; color: var(--primary);">100 Pts</div>
                </div>
                <div style="background: rgba(5, 150, 105, 0.04); border: 1px solid rgba(5, 150, 105, 0.1); border-radius: var(--r-sm); padding: 14px; text-align: center;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Dividen SHU/Tahun</div>
                    <div id="sim-shu" style="font-size: 20px; font-weight: 800; color: var(--success);">Rp 240.000</div>
                </div>
            </div>

            <div style="background: var(--surface); border: 1px solid var(--hairline); border-radius: var(--r-sm); padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--body);">Tingkat Loyalitas:</span>
                    <span id="sim-tier" style="font-size: 11px; font-weight: 800; color: white; background: #c2410c; padding: 2px 10px; border-radius: 100px; text-transform: uppercase; letter-spacing: 0.5px;">🥈 Silver Member</span>
                </div>
                <div id="sim-benefits" style="font-size: 12px; color: var(--muted); line-height: 1.5; font-style: italic;">
                    "Mendapatkan diskon member reguler + Voucer ulang tahun Koperasi."
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ PROGRAM DESCRIPTION & PHOTO ═══════════════════════ --}}
<div class="grid-2 reveal-up" style="gap: 40px; margin-bottom: 64px; align-items: center;">
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div style="font-weight: 800; font-size: 13px; text-transform: uppercase; color: var(--primary); letter-spacing: 1px;">
            📢 Mengenal Program KDKMP Digital
        </div>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); line-height: 1.3; letter-spacing: -0.5px; margin: 0;">
            Pilar Ekonomi Desa Digital Merah Putih
        </h2>
        <p style="font-size: 15px; color: var(--body); line-height: 1.7; margin: 0;">
            Program Koperasi Desa Merah Putih (KDKMP) Digital adalah inisiatif modernisasi ekonomi pedesaan dengan memadukan <strong>ekosistem ritel terpadu ala minimarket modern</strong> dengan layanan keuangan mikro warga.
        </p>
        <p style="font-size: 15px; color: var(--body); line-height: 1.7; margin: 0;">
            Sistem kami memotong rantai distribusi pangan yang panjang dengan menyerap hasil tani lokal secara langsung, mendistribusikannya kembali sebagai sembako murah bagi warga, serta membagikan keuntungan bersih kembali kepada seluruh anggota dalam bentuk dividen <strong>Sisa Hasil Usaha (SHU)</strong> tahunan.
        </p>
        <div style="display: flex; gap: 24px; margin-top: 8px;">
            <div style="flex: 1; border-left: 3px solid var(--primary); padding-left: 16px;">
                <h4 style="font-weight: 700; color: var(--ink); margin-bottom: 4px;">Ritel Modern</h4>
                <p style="font-size: 13px; color: var(--muted); margin: 0;">Belanja sembako online dengan harga khusus anggota &amp; antar ke rumah.</p>
            </div>
            <div style="flex: 1; border-left: 3px solid var(--success); padding-left: 16px;">
                <h4 style="font-weight: 700; color: var(--success); margin-bottom: 4px;">Pemberdayaan Tani</h4>
                <p style="font-size: 13px; color: var(--muted); margin: 0;">Jaminan penyerapan panen hortikultura dengan timbangan transparan.</p>
            </div>
        </div>
    </div>
    <div class="animated-float-slow" style="position: relative; border-radius: var(--r-lg); overflow: hidden; box-shadow: var(--shadow-xl); border: 1px solid var(--hairline);">
        <img src="{{ asset('images/koperasi_kdkmp.png') }}" alt="Gedung Koperasi KDKMP" style="width: 100%; object-fit: cover; aspect-ratio: 4/3; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 24px; color: white; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h4 style="margin: 0; font-weight: 700; font-size: 16px;">Kantor Pusat KDKMP</h4>
                <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.85;">Pusat Ritel &amp; Simpan Pinjam Desa</p>
            </div>
            <span style="font-size: 12px; background: var(--primary); padding: 4px 12px; border-radius: 100px; font-weight: 700;">Aktif 📍</span>
        </div>
    </div>
</div>

{{-- ═══════════════════════ VISI & MISI SECTION ═══════════════════════ --}}
<div class="reveal-up" style="background: var(--surface); border: 1px solid var(--hairline); border-radius: var(--r-xl); padding: 50px 40px; margin-bottom: 64px;">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 40px auto;">
        <div style="font-weight: 800; font-size: 13px; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; margin-bottom: 12px;">
            🎯 Landasan Nilai Kami
        </div>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Visi &amp; Misi Koperasi</h2>
        <p style="font-size: 14px; color: var(--muted); margin-top: 8px; line-height: 1.5;">Menuju kemandirian ekonomi desa yang adil, sejahtera, dan terdigitalisasi secara merata.</p>
    </div>

    <div class="grid-3" style="gap: 24px;">
        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 56, 92, 0.08); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 24px;">
                👁️
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;">Visi Koperasi</h3>
            <p style="font-size: 13.5px; color: var(--body); line-height: 1.6; margin: 0;">
                Menjadi lembaga keuangan dan distribusi pangan perdesaan terdepan yang profesional, transparan, dan berbasis teknologi guna menyejahterakan seluruh lapisan masyarakat desa.
            </p>
        </div>
        
        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.08); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                🌾
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;">Misi Agro &amp; Hasil Bumi</h3>
            <p style="font-size: 13.5px; color: var(--body); line-height: 1.6; margin: 0;">
                Menjamin kedaulatan pangan desa dengan menyerap hasil panen tani lokal pada harga wajar, menghilangkan ketergantungan pada tengkulak, dan mendampingi pembiayaan usaha tani.
            </p>
        </div>

        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(0, 91, 170, 0.08); display: flex; align-items: center; justify-content: center; color: var(--info); font-size: 24px;">
                ⚖️
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;">Misi Pemerataan SHU</h3>
            <p style="font-size: 13.5px; color: var(--body); line-height: 1.6; margin: 0;">
                Membangun ekosistem keanggotaan aktif di mana keuntungan transaksi belanja harian dikembalikan secara adil berimbang bagi seluruh anggota yang berpartisipasi.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════ LATEST PRODUCTS ═══════════════════════ --}}
<div class="reveal-up" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid var(--hairline); padding-bottom: 16px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 800; color: var(--ink); margin-bottom: 4px;">Pilihan Koperasi Hari Ini</h2>
        <p style="font-size: 14px; color: var(--muted); margin: 0;">Barang segar dan bahan pokok terlaris di KDKMP</p>
    </div>
    <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 4px;">
        Lihat Semua <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
</div>

<div class="grid-4" style="margin-bottom: 64px;">
    @php
        $featuredProducts = \App\Models\Product::with('category')->where('branch_id', $currentBranchId)->latest()->take(4)->get();
    @endphp

    @forelse($featuredProducts as $idx => $product)
        <div class="property-card reveal-rotate delay-{{ ($idx % 4) + 1 }}" onclick="window.location.href='{{ route('catalog.show', $product->id) }}'" style="cursor: pointer;">
            <div class="property-card-photo">
                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}" loading="lazy">
                @if($product->is_local_product)
                    <span class="local-badge">🌾 Tani Lokal</span>
                @endif
            </div>

            <div class="property-card-meta">
                <div class="property-card-title">
                    <span style="font-weight: 700; color: var(--ink);">{{ $product->name }}</span>
                    <span style="font-size: 10px; color: var(--muted); font-weight: 600; background: var(--surface-md); padding: 1px 8px; border-radius: 100px; flex-shrink: 0; margin-left: 4px;">{{ $product->unit }}</span>
                </div>
                
                <div class="property-card-price" style="margin-top: 6px;">
                    @auth
                        @if(auth()->user()->role === 'anggota')
                            <span style="color: var(--primary); font-weight: 700;">Rp {{ number_format($product->price_member, 0, ',', '.') }}</span>
                            <span class="price-strike" style="font-size: 11px; text-decoration: line-through; color: var(--muted); margin-left: 6px;">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                        @else
                            <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                        @endif
                    @else
                        <span style="font-weight: 700;">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                        <span style="font-size: 11px; display: block; color: var(--success); font-weight: 600; margin-top: 3px;">
                            💚 Hemat Rp {{ number_format($product->price_non_member - $product->price_member, 0, ',', '.') }} jika anggota
                        </span>
                    @endauth
                </div>

                @if($product->current_stock > 0)
                    <button type="button" class="quick-buy-btn" onclick="event.stopPropagation(); window.location.href='{{ route('catalog.show', $product->id) }}'" style="margin-top: 12px; width: 100%;">
                        <span>Lihat Detail</span>
                    </button>
                @else
                    <button class="quick-buy-btn" style="opacity: 0.45; cursor: not-allowed; margin-top: 12px; width: 100%;" disabled>
                        Stok Habis
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: var(--surface); border-radius: var(--r-lg); color: var(--muted);">
            Katalog saat ini sedang diperbarui.
        </div>
    @endforelse
</div>

{{-- ═══════════════════════ MAPS INTEGRATION ═══════════════════════ --}}
<div class="reveal-up" style="margin-bottom: 64px;">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 32px auto;">
        <div style="font-weight: 800; font-size: 13px; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; margin-bottom: 12px;">
            🗺️ Jaringan Sebaran Desa
        </div>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Lokasi Gerai KDKMP</h2>
        <p style="font-size: 14px; color: var(--muted); margin-top: 8px; line-height: 1.5;">Kunjungi gerai fisik KDKMP terdekat di desa Anda atau klik gerai di bawah ini untuk memfokuskan peta secara interaktif.</p>
    </div>
    
    <div class="grid-2" style="gap: 32px; align-items: stretch;">
        <div style="display: flex; flex-direction: column; justify-content: center; gap: 16px;">
            <div id="branch-card-1" class="map-branch-card active" onclick="focusBranch(1, -7.2278, 107.9087)" style="background: white; border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 20px; box-shadow: var(--shadow-sm); display: flex; gap: 16px; align-items: flex-start;">
                <span style="font-size: 24px; background: var(--primary-light); padding: 8px; border-radius: 10px; color: var(--primary); flex-shrink: 0;">📍</span>
                <div>
                    <h4 style="font-weight: 700; color: var(--ink); margin: 0 0 4px 0;">Cabang 1: Desa Merah Putih (Kantor Pusat)</h4>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 6px 0; line-height: 1.4;">Balai Desa Merah Putih, Kec. Karangpawitan, Garut, Jawa Barat</p>
                    <span style="font-size: 11px; font-weight: 600; color: var(--primary); background: var(--primary-light); padding: 2px 8px; border-radius: 100px;">Unit Utama &amp; Pusat Timbangan</span>
                </div>
            </div>
            
            <div id="branch-card-2" class="map-branch-card" onclick="focusBranch(2, -7.2420, 107.8820)" style="background: white; border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 20px; box-shadow: var(--shadow-sm); display: flex; gap: 16px; align-items: flex-start;">
                <span style="font-size: 24px; background: var(--info-bg); padding: 8px; border-radius: 10px; color: var(--info); flex-shrink: 0;">📍</span>
                <div>
                    <h4 style="font-weight: 700; color: var(--ink); margin: 0 0 4px 0;">Cabang 2: Desa Gotong Royong</h4>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 6px 0; line-height: 1.4;">Jalan Raya Gotong Royong No. 45, Garut, Jawa Barat</p>
                    <span style="font-size: 11px; font-weight: 600; color: var(--success); background: var(--success-bg); padding: 2px 8px; border-radius: 100px;">Gudang Agro Cabang</span>
                </div>
            </div>
        </div>
        
        <div style="position: relative; height: 350px; border-radius: var(--r-lg); overflow: hidden; border: 1.5px solid var(--hairline); box-shadow: var(--shadow-lg);">
            <div id="map" style="height: 100%; width: 100%; z-index: 1;"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ CTA SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: linear-gradient(135deg, var(--surface) 0%, var(--surface-md) 100%); border: 1.5px solid var(--hairline); border-radius: var(--r-xl); padding: 48px; text-align: center; margin-bottom: 32px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -30%; left: -10%; width: 250px; height: 250px; border-radius: 50%; background: rgba(228, 0, 43, 0.03); filter: blur(40px); pointer-events: none;"></div>
    
    <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin-bottom: 16px;">Jadilah Bagian dari Kemajuan Desa</h2>
    <p style="font-size: 15px; color: var(--muted); max-width: 600px; margin: 0 auto 32px auto; line-height: 1.6;">
        Dengan bergabung sebagai anggota aktif KDKMP, Anda turut memajukan ekosistem ekonomi pedesaan yang berdaulat, mandiri, dan berkeadilan sosial.
    </p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2;">
        @guest
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="border-radius: 100px; padding: 0 28px; background-color: var(--primary);">
                📝 Daftar Akun Anggota
            </a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg" style="border-radius: 100px; padding: 0 28px;">
                🔐 Masuk ke Sistem
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg" style="border-radius: 100px; padding: 0 28px; background-color: var(--primary);">
                🏠 Masuk ke Dasbor Saya
            </a>
        @endguest
    </div>
</div>

{{-- --- Leaflet OpenStreetMap --- --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let map;
    let markers = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map centered around coordinates in Garut
        map = L.map('map', {
            scrollWheelZoom: false
        }).setView([-7.2278, 107.9087], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marker Desa Merah Putih
        markers[1] = L.marker([-7.2278, 107.9087]).addTo(map)
            .bindPopup('<b>KDKMP Desa Merah Putih</b><br>Kantor Pusat, Gudang &amp; Minimarket Sembako.');

        // Marker Desa Gotong Royong
        markers[2] = L.marker([-7.2420, 107.8820]).addTo(map)
            .bindPopup('<b>KDKMP Desa Gotong Royong</b><br>Depot Ritel Baru &amp; Pos Penyerapan Tani Desa.');

        // Initialize calculator with default value
        calculateSimulator(document.getElementById('simulation-range').value);
    });

    // Interactive map focus function
    window.focusBranch = function(id, lat, lng) {
        // Update active class on cards
        document.querySelectorAll('.map-branch-card').forEach(card => card.classList.remove('active'));
        document.getElementById('branch-card-' + id).classList.add('active');

        // Focus map and pop open the marker popup
        map.setView([lat, lng], 15);
        if (markers[id]) {
            markers[id].openPopup();
        }
    };

    // Loyalty points and SHU calculator logic
    window.calculateSimulator = function(val) {
        const parsedVal = parseInt(val);
        
        // Format value to rupiah label
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });
        document.getElementById('range-val-label').innerText = formatter.format(parsedVal).replace("IDR", "Rp");

        // Calculations
        // 1. Points: 1 point for every Rp 10.000 spent
        const monthlyPoints = Math.floor(parsedVal / 10000);
        document.getElementById('sim-points').innerText = monthlyPoints.toLocaleString('id-ID') + ' Pts';

        // 2. Annual SHU Dividends: estimated 2% return of annual belanja (monthly belanja * 12 * 0.02)
        const annualShu = Math.floor(parsedVal * 12 * 0.02);
        document.getElementById('sim-shu').innerText = formatter.format(annualShu).replace("IDR", "Rp");

        // 3. Tiers & Benefits
        let tier = '🥉 Bronze Member';
        let benefits = '"Mendapatkan harga khusus anggota pada semua komoditas utama."';
        let tierColor = '#c2410c'; // bronze

        if (monthlyPoints >= 150) {
            tier = '🥇 Gold Member';
            benefits = '"Akses prioritas penyerapan tani + pengantaran gratis belanja sembako + dividen SHU ekstra."';
            tierColor = '#d97706'; // gold
        } else if (monthlyPoints >= 50) {
            tier = '🥈 Silver Member';
            benefits = '"Diskon belanja reguler + voucer belanja bulanan + prioritas antrean kasir."';
            tierColor = '#4b5563'; // silver
        }

        const tierEl = document.getElementById('sim-tier');
        tierEl.innerText = tier;
        tierEl.style.backgroundColor = tierColor;
        document.getElementById('sim-benefits').innerText = benefits;
    };
</script>

@endsection