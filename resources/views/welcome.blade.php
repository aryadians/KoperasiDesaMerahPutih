@extends('layouts.app')

@php
    $currentBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
    $currentBranch = \App\Models\Branch::find($currentBranchId) ?? \App\Models\Branch::first();
@endphp

@section('title', 'KDKMP Digital — Koperasi ' . $currentBranch->name)

@section('content')

{{-- ═══════════════════════ HERO SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 60%, #8b0e2a 100%); color: white; border-radius: var(--r-xl); padding: 70px 40px; text-align: center; margin-bottom: 48px; position: relative; overflow: hidden; box-shadow: var(--shadow-xl);">
    <!-- Decorative background elements -->
    <div style="position: absolute; top: -50%; left: -10%; width: 60%; height: 200%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent); transform: skewX(-20deg); animation: shimmer 6s ease-in-out infinite;"></div>
    
    <div style="position: relative; z-index: 1; max-width: 850px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
        <span class="animated-float" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 6px 18px; border-radius: var(--r-full); margin-bottom: 24px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
            🚀 Selamat Datang di KDKMP Digital
        </span>
        
        <h1 style="font-size: 44px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; letter-spacing: -1px; text-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            Modernisasi Ritel Desa &amp; Pertanian Berkelanjutan<br>
            <span style="color: #ffc4d0;">Maju Bersama Koperasi Desa.</span>
        </h1>
        
        <p style="font-size: 17px; line-height: 1.6; opacity: 0.95; margin-bottom: 32px; max-width: 720px; font-weight: 400;">
            Platform digital terintegrasi Koperasi {{ $currentBranch->name }}. Mengadopsi teknologi ritel modern untuk belanja kebutuhan harian, pembiayaan mikro simpan pinjam warga, serta penyerapan langsung komoditas pertanian lokal.
        </p>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('catalog.index') }}" class="btn btn-xl" style="background: white; color: var(--primary); font-weight: 700; border-radius: 100px; padding: 0 32px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); transition: transform 0.2s; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                🛒 Mulai Belanja Sembako
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-xl" style="background: rgba(0,0,0,0.2); color: white; border: 1.5px solid rgba(255,255,255,0.4); font-weight: 600; border-radius: 100px; padding: 0 32px; backdrop-filter: blur(8px); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='scale(1.05)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'; this.style.transform='scale(1)'">
                    ✨ Daftar Anggota
                </a>
            @endguest
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
    <div class="animated-float" style="position: relative; border-radius: var(--r-lg); overflow: hidden; box-shadow: var(--shadow-xl); border: 1px solid var(--hairline);">
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
        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 56, 92, 0.08); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 24px;">
                👁️
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;">Visi Koperasi</h3>
            <p style="font-size: 13.5px; color: var(--body); line-height: 1.6; margin: 0;">
                Menjadi lembaga keuangan dan distribusi pangan perdesaan terdepan yang profesional, transparan, dan berbasis teknologi guna menyejahterakan seluruh lapisan masyarakat desa.
            </p>
        </div>
        
        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.08); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                🌾
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;">Misi Agro &amp; Hasil Bumi</h3>
            <p style="font-size: 13.5px; color: var(--body); line-height: 1.6; margin: 0;">
                Menjamin kedaulatan pangan desa dengan menyerap hasil panen tani lokal pada harga wajar, menghilangkan ketergantungan pada tengkulak, dan mendampingi pembiayaan usaha tani.
            </p>
        </div>

        <div class="card card-hover" style="background: white; border-radius: var(--r-md); padding: 32px 24px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 16px;">
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
        <p style="font-size: 14px; color: var(--muted); margin-top: 8px; line-height: 1.5;">Kunjungi gerai fisik KDKMP terdekat di desa Anda untuk pengambilan barang langsung (*Store Pick-Up*).</p>
    </div>
    
    <div class="grid-2" style="gap: 32px; align-items: stretch;">
        <div style="display: flex; flex-direction: column; justify-content: center; gap: 16px;">
            <div style="background: white; border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 20px; box-shadow: var(--shadow-sm); display: flex; gap: 16px; align-items: flex-start; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
                <span style="font-size: 24px; background: var(--primary-light); padding: 8px; border-radius: 10px; color: var(--primary);">📍</span>
                <div>
                    <h4 style="font-weight: 700; color: var(--ink); margin: 0 0 4px 0;">Cabang 1: Desa Merah Putih (Kantor Pusat)</h4>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 6px 0; line-height: 1.4;">Balai Desa Merah Putih, Kec. Karangpawitan, Garut, Jawa Barat</p>
                    <span style="font-size: 11px; font-weight: 600; color: var(--primary); background: var(--primary-light); padding: 2px 8px; border-radius: 100px;">Unit Utama</span>
                </div>
            </div>
            
            <div style="background: white; border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 20px; box-shadow: var(--shadow-sm); display: flex; gap: 16px; align-items: flex-start; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
                <span style="font-size: 24px; background: var(--info-bg); padding: 8px; border-radius: 10px; color: var(--info);">📍</span>
                <div>
                    <h4 style="font-weight: 700; color: var(--ink); margin: 0 0 4px 0;">Cabang 2: Desa Gotong Royong</h4>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 6px 0; line-height: 1.4;">Jalan Raya Gotong Royong No. 45, Garut, Jawa Barat</p>
                    <span style="font-size: 11px; font-weight: 600; color: var(--success); background: var(--success-bg); padding: 2px 8px; border-radius: 100px;">Cabang Baru</span>
                </div>
            </div>
        </div>
        
        <div style="position: relative; height: 350px; border-radius: var(--r-lg); overflow: hidden; border: 1.5px solid var(--hairline); box-shadow: var(--shadow-lg);">
            <div id="map" style="height: 100%; width: 100%; z-index: 1;"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ CTA SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: var(--surface); border: 1.5px solid var(--hairline); border-radius: var(--r-xl); padding: 48px; text-align: center; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
    <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin-bottom: 16px;">Jadilah Bagian dari Kemajuan Desa</h2>
    <p style="font-size: 15px; color: var(--muted); max-width: 600px; margin: 0 auto 32px auto; line-height: 1.6;">
        Dengan bergabung sebagai anggota aktif KDKMP, Anda turut memajukan ekosistem ekonomi pedesaan yang berdaulat, mandiri, dan berkeadilan sosial.
    </p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        @guest
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="border-radius: 100px; padding: 0 28px;">
                📝 Daftar Akun Anggota
            </a>
            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg" style="border-radius: 100px; padding: 0 28px;">
                🔐 Masuk ke Sistem
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg" style="border-radius: 100px; padding: 0 28px;">
                🏠 Masuk ke Dasbor Saya
            </a>
        @endguest
    </div>
</div>

{{-- --- Leaflet OpenStreetMap --- --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Map centered around coordinates in Garut
        var map = L.map('map', {
            scrollWheelZoom: false
        }).setView([-7.2278, 107.9087], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marker Desa Merah Putih
        L.marker([-7.2278, 107.9087]).addTo(map)
            .bindPopup('<b>KDKMP Desa Merah Putih</b><br>Kantor Pusat, Gudang &amp; Minimarket Sembako.');

        // Marker Desa Gotong Royong
        L.marker([-7.2420, 107.8820]).addTo(map)
            .bindPopup('<b>KDKMP Desa Gotong Royong</b><br>Depot Ritel Baru &amp; Pos Penyerapan Tani Desa.');
    });
</script>

@endsection