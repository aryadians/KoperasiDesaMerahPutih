@extends('layouts.app')

@section('title', 'KDKMP Digital - Koperasi Desa Merah Putih')

@section('content')

{{-- ═══════════════════════ HERO SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 60%, #8b0e2a 100%); color: white; border-radius: var(--r-xl); padding: 60px 40px; text-align: center; margin-bottom: 48px; position: relative; overflow: hidden; box-shadow: var(--shadow-xl);">
    <!-- Decorative background elements -->
    <div style="position: absolute; top: -50%; left: -10%; width: 60%; height: 200%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent); transform: skewX(-20deg); animation: shimmer 5s ease-in-out infinite;"></div>
    
    <div style="position: relative; z-index: 1; max-width: 800px; margin: 0 auto;">
        <span style="display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 6px 16px; border-radius: var(--r-full); margin-bottom: 24px; backdrop-filter: blur(4px);">
            🚀 Selamat Datang di KDKMP Digital
        </span>
        
        <h1 style="font-size: 48px; font-weight: 800; line-height: 1.15; margin-bottom: 20px; letter-spacing: -1px;">
            Belanja Murah, Petani Sejahtera,<br>
            <span style="color: #ffc4d0;">Semua untuk Warga Desa.</span>
        </h1>
        
        <p style="font-size: 18px; line-height: 1.6; opacity: 0.9; margin-bottom: 32px;">
            Platform modern Koperasi Desa Merah Putih. Belanja kebutuhan sembako harian dengan harga grosir, jual hasil panen langsung ke koperasi, dan nikmati Sisa Hasil Usaha (SHU) setiap tahunnya.
        </p>
        
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('catalog.index') }}" class="btn btn-xl" style="background: white; color: var(--primary); font-weight: 700; border-radius: 100px; padding: 0 32px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                🛒 Mulai Belanja Sembako
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-xl" style="background: rgba(0,0,0,0.25); color: white; border: 1px solid rgba(255,255,255,0.4); font-weight: 600; border-radius: 100px; padding: 0 32px; backdrop-filter: blur(8px);">
                    ✨ Daftar Jadi Anggota
                </a>
            @endguest
        </div>
    </div>
</div>

{{-- ═══════════════════════ VALUE PROPOSITIONS ═══════════════════════ --}}
<div class="grid-3" style="margin-bottom: 64px;">
    <div class="card card-hover reveal-up delay-1" style="text-align: center; padding: 40px 24px;">
        <div style="font-size: 56px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite;">🏷️</div>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Harga Lebih Hemat</h3>
        <p style="font-size: 14px; color: var(--muted); line-height: 1.6;">
            Dapatkan harga spesial yang jauh lebih murah dibanding harga pasaran dengan mendaftar sebagai anggota aktif Koperasi.
        </p>
    </div>
    
    <div class="card card-hover reveal-up delay-2" style="text-align: center; padding: 40px 24px;">
        <div style="font-size: 56px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite; animation-delay: 0.5s;">🌾</div>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Dukung Tani Lokal</h3>
        <p style="font-size: 14px; color: var(--muted); line-height: 1.6;">
            Kami menyerap langsung hasil panen komoditas lokal dari petani desa, memotong rantai pasok panjang untuk untungkan semua pihak.
        </p>
    </div>

    <div class="card card-hover reveal-up delay-3" style="text-align: center; padding: 40px 24px;">
        <div style="font-size: 56px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite; animation-delay: 1s;">🤝</div>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 12px;">Sisa Hasil Usaha (SHU)</h3>
        <p style="font-size: 14px; color: var(--muted); line-height: 1.6;">
            Keuntungan koperasi akan dikembalikan kepada anggota di akhir tahun berdasarkan keaktifan belanja dan kontribusi.
        </p>
    </div>
</div>

{{-- ═══════════════════════ LATEST PRODUCTS ═══════════════════════ --}}
<div class="reveal-up" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid var(--hairline); padding-bottom: 16px;">
    <div>
        <h2 style="font-size: 24px; font-weight: 800; color: var(--ink); margin-bottom: 4px;">Pilihan Koperasi Hari Ini</h2>
        <p style="font-size: 14px; color: var(--muted);">Barang segar dan bahan pokok terlaris di KDKMP</p>
    </div>
    <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 4px;">
        Lihat Semua <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
</div>

<div class="grid-4" style="margin-bottom: 64px;">
    @php
        // Fetch up to 4 latest active products for the landing page
        $featuredProducts = \App\Models\Product::with('category')->latest()->take(4)->get();
    @endphp

    @forelse($featuredProducts as $idx => $product)
        <div class="property-card reveal-rotate delay-{{ ($idx % 4) + 1 }}" onclick="window.location.href='{{ route('catalog.show', $product->id) }}'">
            <div class="property-card-photo">
                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}" loading="lazy">
                @if($product->is_local_product)
                    <span class="local-badge">🌾 Tani Lokal</span>
                @endif
            </div>

            <div class="property-card-meta">
                <div class="property-card-title">
                    <span>{{ $product->name }}</span>
                    <span style="font-size: 11px; color: var(--muted); font-weight: 400; background: var(--surface-md); padding: 1px 8px; border-radius: 100px; flex-shrink: 0; margin-left: 4px;">{{ $product->unit }}</span>
                </div>
                
                <div class="property-card-price">
                    @auth
                        @if(auth()->user()->role === 'anggota')
                            <span style="color: var(--primary);">Rp {{ number_format($product->price_member, 0, ',', '.') }}</span>
                            <span class="price-strike">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            <span class="member-tag" style="color: var(--primary);">Anggota</span>
                        @else
                            <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                        @endif
                    @else
                        <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                        <span style="font-size: 11px; display: block; color: var(--success); font-weight: 600; margin-top: 3px;">
                            💚 Hemat Rp {{ number_format($product->price_non_member - $product->price_member, 0, ',', '.') }} jika anggota
                        </span>
                    @endauth
                </div>

                @if($product->current_stock > 0)
                    <button type="button" class="quick-buy-btn" onclick="event.stopPropagation(); window.location.href='{{ route('catalog.show', $product->id) }}'">
                        <span>Lihat Detail</span>
                    </button>
                @else
                    <button class="quick-buy-btn" style="opacity: 0.45; cursor: not-allowed;" disabled>
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

{{-- ═══════════════════════ CTA SECTION ═══════════════════════ --}}
<div class="reveal-scale" style="background: var(--surface); border: 1.5px solid var(--hairline); border-radius: var(--r-xl); padding: 48px; text-align: center; margin-bottom: 32px;">
    <h2 style="font-size: 28px; font-weight: 800; color: var(--ink); margin-bottom: 16px;">Jadilah Bagian dari Kemajuan Desa</h2>
    <p style="font-size: 15px; color: var(--muted); max-width: 600px; margin: 0 auto 32px; line-height: 1.6;">
        Dengan bergabung sebagai anggota KDKMP, Anda tidak hanya berbelanja untuk diri sendiri, tetapi juga membantu menggerakkan roda ekonomi kerakyatan Desa Merah Putih.
    </p>
    <div style="display: flex; gap: 16px; justify-content: center;">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="border-radius: 100px;">
            📝 Buat Akun Anggota
        </a>
        <a href="{{ route('login') }}" class="btn btn-secondary btn-lg" style="border-radius: 100px;">
            🔐 Masuk ke Sistem
        </a>
    </div>
</div>

@endsection