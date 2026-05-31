@extends('layouts.app')

@php
    $currentBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
    $currentBranch = \App\Models\Branch::find($currentBranchId) ?? \App\Models\Branch::first();
@endphp
@section('title', 'KDKMP — Gerai Sembako Digital ' . ($currentBranch ? $currentBranch->name : 'Desa'))

@section('content')

{{-- ═══════════════════════ HERO PROMO BANNER ═══════════════════════ --}}
<div class="promo-banner reveal-scale" id="promo-banner" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 60%, #8b0e2a 100%);">
    <div class="promo-content">
        <span class="promo-badge">🔥 PROMO MINGGU INI</span>
        <h2>Belanja Sembako Hemat<br>Langsung dari Koperasi Desa!</h2>
        <p>Harga khusus anggota hingga <strong>20% lebih murah</strong>. Dukung petani lokal, belanja cerdas, sisa hasil usaha dibagi bersama warga.</p>
        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="#retail-section" class="button-primary" style="background: rgba(255,255,255,0.95); color: var(--primary); width: auto; height: 44px; padding: 0 24px; font-size: 14px; border-radius: 100px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); transition: transform 0.3s ease;">
                🛒 Belanja Sekarang
            </a>
            @guest
                <a href="{{ route('register') }}" class="button-secondary" style="border-color: rgba(255,255,255,0.5); color: white; background: rgba(255,255,255,0.12); width: auto; height: 44px; padding: 0 24px; font-size: 14px; border-radius: 100px; backdrop-filter: blur(8px); transition: transform 0.3s ease, background 0.3s ease;">
                    Daftar Anggota →
                </a>
            @endguest
        </div>
        @guest
            <p style="margin-top: 16px; font-size: 13px; opacity: 0.85;">
                💡 Anggota koperasi mendapat harga khusus + bagi SHU tahunan
            </p>
        @endguest
    </div>
    <div class="promo-emoji">🛒</div>
</div>

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
