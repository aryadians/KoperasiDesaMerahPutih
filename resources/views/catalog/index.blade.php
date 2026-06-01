@extends('layouts.app')

@php
    $currentBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
    $currentBranch = \App\Models\Branch::find($currentBranchId) ?? \App\Models\Branch::first();
@endphp
@section('title', 'KDKMP — Gerai Sembako Digital ' . ($currentBranch ? $currentBranch->name : 'Desa'))

@section('content')

{{-- ═══════════════════════ 3D DYNAMIC PROMO CAROUSEL ═══════════════════════ --}}
<style>
    .carousel-3d-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 32px;
        perspective: 1200px;
        border-radius: var(--r-xl);
    }
    .carousel-3d-container {
        position: relative;
        width: 100%;
        height: 280px;
        transform-style: preserve-3d;
        border-radius: var(--r-xl);
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.2),
                    0 15px 25px -10px rgba(0, 0, 0, 0.1);
    }
    .carousel-slide {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        border-radius: var(--r-xl);
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: rotateY(90deg) translateZ(100px);
        transition: opacity 0.6s ease, transform 0.8s cubic-bezier(0.25, 1, 0.5, 1), visibility 0.6s;
        overflow: hidden;
        color: white;
    }
    .carousel-slide.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: rotateY(0deg) translateZ(0px);
        z-index: 2;
    }
    .carousel-slide.prev {
        opacity: 0;
        visibility: visible;
        pointer-events: none;
        transform: rotateY(-90deg) translateZ(100px);
        z-index: 1;
    }
    .carousel-slide.next {
        opacity: 0;
        visibility: visible;
        pointer-events: none;
        transform: rotateY(90deg) translateZ(100px);
        z-index: 1;
    }
    
    .carousel-slide::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.12) 0%, transparent 60%);
        pointer-events: none;
    }

    .voucher-highlight {
        font-family: 'Outfit', 'Courier New', monospace;
        font-size: 1.1em;
        font-weight: 800;
        background: rgba(255, 255, 255, 0.22);
        padding: 2px 10px;
        border-radius: 6px;
        border: 1.5px dashed rgba(255, 255, 255, 0.6);
        color: #ffffff;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    
    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%) translateZ(50px);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1.5px solid rgba(255, 255, 255, 0.3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
    .carousel-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.6);
        transform: translateY(-50%) scale(1.1);
    }
    .carousel-btn:active {
        transform: translateY(-50%) scale(0.95);
    }
    .prev-btn {
        left: 16px;
    }
    .next-btn {
        right: 16px;
    }
    
    .carousel-indicators {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%) translateZ(40px);
        display: flex;
        gap: 8px;
        z-index: 10;
    }
    .indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .indicator.active {
        background: #ffffff;
        width: 24px;
        border-radius: 4px;
    }

    .carousel-slide .promo-content {
        max-width: 60%;
        z-index: 5;
    }
    .carousel-slide .promo-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 100px;
        margin-bottom: 14px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .carousel-slide h2 {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }
    .carousel-slide p {
        font-size: 14px;
        line-height: 1.5;
        opacity: 0.9;
        margin-bottom: 0;
    }
    .carousel-slide .promo-emoji {
        font-size: 110px;
        opacity: 0.9;
        user-select: none;
        animation: float-emoji 4s ease-in-out infinite;
        transform-origin: center bottom;
    }

    @keyframes float-emoji {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-12px) rotate(6deg); }
    }

    @media (max-width: 768px) {
        .carousel-3d-container {
            height: 320px;
        }
        .carousel-slide {
            padding: 24px;
            flex-direction: column;
            text-align: center;
            justify-content: center;
        }
        .carousel-slide .promo-content {
            max-width: 100%;
        }
        .carousel-slide h2 {
            font-size: 20px;
        }
        .carousel-slide p {
            font-size: 12.5px;
        }
        .carousel-slide .promo-emoji {
            display: none !important;
        }
        .prev-btn {
            left: 8px;
        }
        .next-btn {
            right: 8px;
        }
    }
</style>

<div class="carousel-3d-wrapper reveal-scale" id="promo-carousel-wrapper">
    <div class="carousel-3d-container" id="promo-carousel">
        
        <!-- Slide 1: Voucher HEMATTANI -->
        <div class="carousel-slide active" style="background: linear-gradient(135deg, hsl(140, 80%, 16%) 0%, hsl(145, 75%, 24%) 60%, hsl(155, 80%, 30%) 100%);">
            <div class="promo-content">
                <span class="promo-badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">🌾 HASIL TANI DESA</span>
                <h2>Sembako Sehat Hasil Tani!<br>Kode Voucher: <span class="voucher-highlight">HEMATTANI</span></h2>
                <p>Hemat langsung <strong>10%</strong> khusus produk pertanian lokal. Belanja segar sekaligus menyejahterakan petani desa kita.</p>
                <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="#retail-section" class="button-primary" style="background: #ffffff; color: hsl(140, 80%, 20%); width: auto; height: 38px; padding: 0 20px; font-size: 13px; border-radius: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; font-weight: 700;">
                        🌾 Beli Hasil Bumi
                    </a>
                </div>
            </div>
            <div class="promo-emoji">🌾</div>
        </div>

        <!-- Slide 2: Voucher KDKMPMERDEKA -->
        <div class="carousel-slide" style="background: linear-gradient(135deg, hsl(355, 75%, 20%) 0%, hsl(0, 80%, 32%) 60%, hsl(10, 85%, 42%) 100%);">
            <div class="promo-content">
                <span class="promo-badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">🇮🇩 PROMO KEMERDEKAAN</span>
                <h2>Pesta Belanja Merdeka!<br>Kode Voucher: <span class="voucher-highlight">KDKMPMERDEKA</span></h2>
                <p>Nikmati diskon langsung senilai <strong>Rp 17.845</strong> untuk semua produk belanjaan Anda tanpa minimal transaksi.</p>
                <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="#retail-section" class="button-primary" style="background: #ffffff; color: hsl(355, 75%, 25%); width: auto; height: 38px; padding: 0 20px; font-size: 13px; border-radius: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; font-weight: 700;">
                        🇮🇩 Rayakan Promo
                    </a>
                </div>
            </div>
            <div class="promo-emoji">🇮🇩</div>
        </div>

        <!-- Slide 3: Voucher ALFAGIFT3D -->
        <div class="carousel-slide" style="background: linear-gradient(135deg, hsl(210, 80%, 18%) 0%, hsl(220, 75%, 26%) 60%, hsl(235, 80%, 35%) 100%);">
            <div class="promo-content">
                <span class="promo-badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">⚡ SPECIAL LAUNCH</span>
                <h2>Belanja Pintar Ala Alfagift!<br>Kode Voucher: <span class="voucher-highlight">ALFAGIFT3D</span></h2>
                <p>Diskon spektakuler <strong>15%</strong> untuk pengguna pertama hari ini. Dapatkan penawaran terbaik koperasi modern sekarang!</p>
                <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="#retail-section" class="button-primary" style="background: #ffffff; color: hsl(210, 80%, 22%); width: auto; height: 38px; padding: 0 20px; font-size: 13px; border-radius: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; font-weight: 700;">
                        🎁 Gunakan Voucher
                    </a>
                </div>
            </div>
            <div class="promo-emoji">🎁</div>
        </div>

        <!-- Slide 4: Beli 3 Bayar 2 & Tebus Murah -->
        <div class="carousel-slide" style="background: linear-gradient(135deg, hsl(28, 85%, 18%) 0%, hsl(35, 80%, 26%) 60%, hsl(42, 85%, 34%) 100%);">
            <div class="promo-content">
                <span class="promo-badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">📢 PROMO BUNDLE AUTOMATIS</span>
                <h2>Beli 3 Bayar 2 &amp; Tebus Murah!<br>Diskon Otomatis di Keranjang</h2>
                <p>Gratis 1 pcs untuk Mie/Susu tiap kelipatan 3, PLUS Tebus Murah hasil tani lokal hemat <strong>Rp 5.000</strong> per kg (min. belanja Rp 100rb).</p>
                <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="#retail-section" class="button-primary" style="background: #ffffff; color: hsl(28, 85%, 22%); width: auto; height: 38px; padding: 0 20px; font-size: 13px; border-radius: 100px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; font-weight: 700;">
                        🍜 Lihat Sembako
                    </a>
                </div>
            </div>
            <div class="promo-emoji">🍜</div>
        </div>

    </div>

    <!-- Controls -->
    <button class="carousel-btn prev-btn" id="carousel-prev" aria-label="Previous Promo">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button class="carousel-btn next-btn" id="carousel-next" aria-label="Next Promo">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>

    <!-- Indicators -->
    <div class="carousel-indicators">
        <span class="indicator active" data-slide="0"></span>
        <span class="indicator" data-slide="1"></span>
        <span class="indicator" data-slide="2"></span>
        <span class="indicator" data-slide="3"></span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.indicator');
        const prevBtn = document.getElementById('carousel-prev');
        const nextBtn = document.getElementById('carousel-next');
        let currentSlide = 0;
        let slideInterval;

        function updateCarousel(nextIndex) {
            slides[currentSlide].classList.remove('active', 'prev', 'next');
            indicators[currentSlide].classList.remove('active');
            
            if (nextIndex > currentSlide) {
                slides[currentSlide].classList.add('prev');
                slides[nextIndex].classList.add('active');
            } else if (nextIndex < currentSlide) {
                slides[currentSlide].classList.add('next');
                slides[nextIndex].classList.add('active');
            } else {
                slides[nextIndex].classList.add('active');
            }

            slides.forEach((slide, i) => {
                if (i !== currentSlide && i !== nextIndex) {
                    slide.classList.remove('prev', 'next', 'active');
                }
            });

            currentSlide = nextIndex;
            indicators[currentSlide].classList.add('active');
        }

        function showNextSlide() {
            let nextIndex = (currentSlide + 1) % slides.length;
            updateCarousel(nextIndex);
        }

        function showPrevSlide() {
            let nextIndex = (currentSlide - 1 + slides.length) % slides.length;
            updateCarousel(nextIndex);
        }

        function startAutoPlay() {
            stopAutoPlay();
            slideInterval = setInterval(showNextSlide, 5000);
        }

        function stopAutoPlay() {
            if (slideInterval) clearInterval(slideInterval);
        }

        if (nextBtn && prevBtn) {
            nextBtn.addEventListener('click', () => {
                showNextSlide();
                startAutoPlay();
            });

            prevBtn.addEventListener('click', () => {
                showPrevSlide();
                startAutoPlay();
            });
        }

        indicators.forEach(indicator => {
            indicator.addEventListener('click', () => {
                const targetSlide = parseInt(indicator.getAttribute('data-slide'));
                if (targetSlide !== currentSlide) {
                    updateCarousel(targetSlide);
                    startAutoPlay();
                }
            });
        });

        startAutoPlay();

        const wrapper = document.getElementById('promo-carousel-wrapper');
        if (wrapper) {
            wrapper.addEventListener('mouseenter', stopAutoPlay);
            wrapper.addEventListener('mouseleave', startAutoPlay);
        }
    });
</script>

{{-- ═══════════════════════ TRUST BADGES ═══════════════════════ --}}
<div class="reveal-up" style="display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap; justify-content: center;">
    @php
    $badges = [
        ['icon' => '🏪', 'text' => 'Produk Terverifikasi'],
        ['icon' => '🌾', 'text' => 'Komoditas Lokal Desa'],
        ['icon' => '💳', 'text' => 'Harga Transparan'],
        ['icon' => '🤝', 'text' => 'SHU Bagi Anggota'],
        ['icon' => '⚡', 'text' => 'Pengiriman Cepat'],
    ];
    @endphp
    @foreach($badges as $idx => $b)
        <div class="reveal-rotate delay-{{ ($idx % 5) + 1 }}" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: var(--surface-soft); border: 1px solid var(--hairline-soft); border-radius: 100px; font-size: 13px; font-weight: 500; color: var(--body); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: default;" onmouseover="this.style.background='var(--canvas)';this.style.borderColor='var(--primary-muted)';this.style.transform='translateY(-4px) scale(1.05)';this.style.boxShadow='0 8px 16px rgba(0,0,0,0.06)'" onmouseout="this.style.background='var(--surface-soft)';this.style.borderColor='var(--hairline-soft)';this.style.transform='';this.style.boxShadow='none'">
            <span style="font-size: 16px;">{{ $b['icon'] }}</span>
            <span>{{ $b['text'] }}</span>
        </div>
    @endforeach
</div>

{{-- ═══════════════════════ SEARCH BAR ═══════════════════════ --}}
<div class="search-bar-wrapper reveal-scale">
    <form action="{{ route('catalog.index') }}" method="GET" class="search-bar-pill">
        <div class="search-field-segment">
            <label for="search">Cari Barang</label>
            <input type="text" name="search" id="search" placeholder="Indomie, beras, minyak, sabun..." value="{{ request('search') }}" autocomplete="off">
        </div>
        <div class="search-field-segment">
            <label for="category">Kategori</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="search-field-segment">
            <label for="local">Asal Komoditas</label>
            <select name="local" id="local" onchange="this.form.submit()">
                <option value="">Semua Komoditas</option>
                <option value="1" {{ request('local') == '1' ? 'selected' : '' }}>🌾 Hasil Tani Lokal Desa</option>
            </select>
        </div>
        <button type="submit" class="search-orb" aria-label="Cari">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </form>
</div>

{{-- ═══════════════════════ CATEGORY STRIP ═══════════════════════ --}}
<div class="category-strip reveal-up" id="retail-section">
    <a href="{{ route('catalog.index') }}" class="category-tab {{ !request('category') && !request('local') ? 'active' : '' }}">
        <span>🏪 Semua</span>
    </a>
    @foreach($categories as $category)
        <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="category-tab {{ request('category') == $category->id ? 'active' : '' }}">
            <span>{{ $category->name }}</span>
        </a>
    @endforeach
    <a href="{{ route('catalog.index', ['local' => 1]) }}" class="category-tab {{ request('local') == 1 ? 'active' : '' }}">
        <span style="color: var(--success); font-weight: 600;">🌾 Hasil Panen Tani</span>
    </a>
</div>

{{-- ═══════════════════════ RESULTS HEADER ═══════════════════════ --}}
@if(request('search') || request('category') || request('local'))
    <div class="reveal-right" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding: 14px 20px; background: var(--surface-soft); border-radius: var(--r-md);">
        <div style="font-size: 14px; font-weight: 600; color: var(--ink);">
            📦 Menampilkan <span style="color: var(--primary);">{{ $products->count() }}</span> produk
            @if(request('search')) untuk "<em>{{ request('search') }}</em>" @endif
        </div>
        <a href="{{ route('catalog.index') }}" style="font-size: 13px; color: var(--muted); font-weight: 500; padding: 6px 14px; border: 1px solid var(--hairline); border-radius: 100px; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--ink)'" onmouseout="this.style.borderColor='var(--hairline)'">
            ✕ Reset Filter
        </a>
    </div>
@endif

{{-- ═══════════════════════ SKELETON LOADER ═══════════════════════ --}}
<div class="grid-4" id="skeleton-grid">
    @for($i = 0; $i < 8; $i++)
        <div class="skeleton-card">
            <div class="skeleton-image"></div>
            <div class="skeleton-title"></div>
            <div class="skeleton-desc"></div>
            <div class="skeleton-price"></div>
            <div class="skeleton-btn"></div>
        </div>
    @endfor
</div>

{{-- ═══════════════════════ PRODUCT GRID ═══════════════════════ --}}
@if($products->isEmpty())
    <div id="product-results-empty" style="display: none; text-align: center; padding: 72px 32px; border: 2px dashed var(--hairline); border-radius: var(--r-lg); color: var(--muted); animation: page-enter 0.5s var(--ease-decel);">
        <div style="font-size: 64px; margin-bottom: 16px; animation: emoji-bounce 3s ease-in-out infinite;">🔍</div>
        <h3 style="font-size: 20px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Produk Tidak Ditemukan</h3>
        <p style="margin-bottom: 24px;">Coba ubah kata kunci pencarian atau pilih kategori yang berbeda.</p>
        <a href="{{ route('catalog.index') }}" class="button-primary" style="width: auto; padding: 0 28px; border-radius: 100px;">
            Tampilkan Semua Produk
        </a>
    </div>
@else
    <div class="grid-4" id="actual-product-grid" style="display: none; opacity: 0; transition: opacity 0.5s ease;">
        @foreach($products as $idx => $product)
            <div class="property-card reveal-rotate delay-{{ ($idx % 4) + 1 }}" onclick="navigateToDetail(event, '{{ route('catalog.show', $product->id) }}')">

                {{-- Product Image --}}
                <div class="property-card-photo">
                    <img
                        src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                    >
                    @if($product->is_local_product)
                        <span class="local-badge">🌾 Tani Lokal</span>
                    @endif
                    @if($product->current_stock > 0 && $product->current_stock <= 5)
                        <span class="guest-favorite-badge" style="background: var(--primary); color: white;">🔥 Stok Tipis</span>
                    @elseif($product->current_stock === 0)
                        <span class="guest-favorite-badge" style="background: rgba(100,100,100,0.85); color: white;">Habis</span>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="property-card-meta">
                    <div class="property-card-title">
                        <span>{{ $product->name }}</span>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 400; background: var(--surface-soft); padding: 1px 8px; border-radius: 100px; flex-shrink: 0; margin-left: 4px;">{{ $product->unit }}</span>
                    </div>
                    <p class="property-card-description">{{ $product->description ?? 'Produk berkualitas dari koperasi desa.' }}</p>

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

                    {{-- Buy Button --}}
                    @if($product->current_stock > 0)
                        @auth
                            <button type="button" class="quick-buy-btn" onclick="quickAddToCart(event, {{ $product->id }}, '{{ addslashes($product->name) }}')">
                                <span style="position: relative; z-index: 1;">🛒 Beli Langsung</span>
                            </button>
                        @else
                            <a href="{{ route('register') }}" class="quick-buy-btn" style="text-align: center; text-decoration: none;">
                                <span style="position: relative; z-index: 1;">🛒 Daftar &amp; Beli</span>
                            </a>
                        @endauth
                    @else
                        <button class="quick-buy-btn" style="opacity: 0.45; cursor: not-allowed;" disabled>
                            Stok Habis
                        </button>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@endif

{{-- Hidden Cart Form --}}
<form id="quick-cart-form" action="{{ route('cart.add') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="product_id" id="quick-cart-product-id">
    <input type="hidden" name="quantity" value="1">
</form>

<script>
    // ── Card click navigation (skip button clicks)
    function navigateToDetail(event, url) {
        if (!event.target.closest('.quick-buy-btn')) {
            document.body.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            document.body.style.opacity = '0';
            document.body.style.transform = 'scale(0.98)';
            setTimeout(() => window.location.href = url, 200);
        }
    }

    // ── AJAX Quick Add to Cart
    function quickAddToCart(event, productId, productName) {
        event.stopPropagation();
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const btn = event.target.closest('.quick-buy-btn');
        const originalContent = btn.innerHTML;

        btn.innerHTML = '<span style="position:relative;z-index:1;">⏳ Menambahkan...</span>';
        btn.disabled = true;
        btn.style.opacity = '0.7';

        fetch("{{ route('cart.add') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            btn.style.opacity = '1';

            if (data.success) {
                // Update cart badge
                let badge = document.getElementById('cart-badge');
                if (!badge) {
                    // Create badge if not exists
                    const cartLink = document.querySelector('.cart-icon-wrap');
                    if (cartLink) {
                        badge = document.createElement('span');
                        badge.id = 'cart-badge';
                        badge.className = 'cart-badge';
                        cartLink.appendChild(badge);
                    }
                }
                if (badge) badge.textContent = data.cart_count;

                // Success animation on button
                btn.innerHTML = '<span style="position:relative;z-index:1;">✅ Ditambahkan!</span>';
                btn.style.background = 'var(--success)';
                btn.style.color = 'white';
                btn.style.borderColor = 'var(--success)';
                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }, 1500);

                window.showSweetAlert('Berhasil Ditambahkan! 🛒', '"' + productName + '" sudah masuk ke keranjang belanja Anda.', 'success');
            } else {
                window.showSweetAlert('Gagal Menambahkan', data.message || 'Stok tidak mencukupi atau terjadi kesalahan.', 'error');
            }
        })
        .catch(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            btn.style.opacity = '1';
            // Fallback to form submit
            document.getElementById('quick-cart-product-id').value = productId;
            document.getElementById('quick-cart-form').submit();
        });
    }

    // ── Skeleton → Product Grid Transition
    document.addEventListener('DOMContentLoaded', function() {
        const skeleton  = document.getElementById('skeleton-grid');
        const grid      = document.getElementById('actual-product-grid');
        const emptyMsg  = document.getElementById('product-results-empty');

        // Simulate loading for realistic UX (min 400ms)
        const minDelay = 400;
        const start    = Date.now();

        function showContent() {
            const elapsed = Date.now() - start;
            const remaining = Math.max(0, minDelay - elapsed);
            setTimeout(() => {
                if (skeleton) {
                    skeleton.style.transition = 'opacity 0.3s ease';
                    skeleton.style.opacity = '0';
                    setTimeout(() => { skeleton.style.display = 'none'; }, 300);
                }
                if (grid) {
                    grid.style.display = 'grid';
                    setTimeout(() => { grid.style.opacity = '1'; }, 50);
                }
                if (emptyMsg) {
                    emptyMsg.style.display = 'block';
                }
            }, remaining);
        }

        // Show after images start loading OR after timeout
        showContent();
    });

    // ── Promo banner parallax
    window.addEventListener('scroll', () => {
        const banner = document.getElementById('promo-banner');
        if (banner) {
            const y = window.scrollY * 0.3;
            banner.style.backgroundPosition = `center ${y}px`;
        }
    }, { passive: true });
</script>

@endsection
