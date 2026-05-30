@extends('layouts.app')

@section('title', 'KDKMP Digital Sembako Desa - Ekosistem Retail Rakyat')

@section('content')

<!-- Promo Hero Banner Carousel (Indomaret/Alfamart style) -->
<div class="promo-banner" id="promo-banner-slide">
    <div class="promo-content">
        <span class="promo-badge">PROMO JSM DESA</span>
        <h2>Belanja Hemat Warga Desa Merah Putih</h2>
        <p>Diskon khusus hingga 15% untuk anggota koperasi aktif. Belanja sembako murah, untung melimpah, sisa hasil usaha dibagi rata!</p>
        <div style="margin-top: 20px; display: flex; gap: 12px;">
            <a href="#retail-section" class="button-primary" style="background-color: white; color: var(--colors-primary); font-size: 14px; height: 38px; width: auto; padding: 0 20px;">
                Belanja Sekarang
            </a>
            @guest
                <a href="{{ route('register') }}" class="button-secondary" style="border-color: white; color: white; background: transparent; font-size: 14px; height: 38px; width: auto; padding: 0 20px;">
                    Daftar Anggota
                </a>
            @endguest
        </div>
    </div>
    <div style="font-size: 80px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15)); user-select: none;">
        🛒
    </div>
</div>

<!-- Global Pill Search Bar (DESIGN.md search-bar-pill) -->
<div class="search-bar-wrapper">
    <form action="{{ route('catalog.index') }}" method="GET" class="search-bar-pill">
        <!-- Segment 1: Search Name -->
        <div class="search-field-segment">
            <label for="search">Cari Barang</label>
            <input type="text" name="search" id="search" placeholder="Indomie, beras, minyak, sabun..." value="{{ request('search') }}">
        </div>
        
        <!-- Segment 2: Category Filter -->
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
        
        <!-- Segment 3: Local Commodities filter -->
        <div class="search-field-segment">
            <label for="local">Asal Komoditas</label>
            <select name="local" id="local" onchange="this.form.submit()">
                <option value="">Semua Komoditas</option>
                <option value="1" {{ request('local') == '1' ? 'selected' : '' }}>Hasil Tani Lokal Desa</option>
            </select>
        </div>
        
        <!-- Search Orb -->
        <button type="submit" class="search-orb">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </form>
</div>

<!-- Category Strip (DESIGN.md category-strip) -->
<div class="category-strip" id="retail-section">
    <a href="{{ route('catalog.index') }}" class="category-tab {{ !request('category') && !request('local') ? 'active' : '' }}">
        <span>Semua Kategori</span>
    </a>
    @foreach($categories as $category)
        <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="category-tab {{ request('category') == $category->id ? 'active' : '' }}">
            <span>{{ $category->name }}</span>
        </a>
    @endforeach
    <a href="{{ route('catalog.index', ['local' => 1]) }}" class="category-tab {{ request('local') == 1 ? 'active' : '' }}">
        <span style="color: #1a7f5a; font-weight: 600;">🌾 Hasil Panen Tani</span>
    </a>
</div>

<!-- SKELETON LOADERS (Shown initially) -->
<div class="grid-4" id="skeleton-grid">
    @for($i = 0; $i < 8; $i++)
        <div class="skeleton-card">
            <div class="skeleton-image"></div>
            <div class="skeleton-title"></div>
            <div class="skeleton-desc"></div>
            <div class="skeleton-price"></div>
        </div>
    @endfor
</div>

<!-- ACTUAL E-COMMERCE PRODUCT GRID (Revealed by JS) -->
@if($products->isEmpty())
    <div id="product-results-empty" style="display: none; text-align: center; padding: 48px; border: 1px dashed var(--colors-hairline); border-radius: var(--rounded-md); color: var(--colors-muted);">
        <p style="font-size: 16px;">Produk sembako tidak ditemukan.</p>
        <a href="{{ route('catalog.index') }}" style="color: var(--colors-primary); font-weight: 600; text-decoration: underline; margin-top: 12px; display: inline-block;">Reset Pencarian</a>
    </div>
@else
    <div class="grid-4" id="actual-product-grid" style="display: none; opacity: 0; transition: opacity 0.4s ease;">
        @foreach($products as $product)
            <div class="property-card" onclick="navigateToDetail(event, '{{ route('catalog.show', $product->id) }}')">
                
                <!-- Product Image -->
                <div class="property-card-photo">
                    <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $product->name }}">
                    @if($product->is_local_product)
                        <span class="local-badge">Tani Lokal</span>
                    @endif
                    @if($product->current_stock > 0 && $product->current_stock <= 5)
                        <span class="guest-favorite-badge" style="background-color: #ffebeb; color: #c13515;">Promo Stok Tipis</span>
                    @elseif($product->current_stock === 0)
                        <span class="guest-favorite-badge" style="background-color: #f2f2f2; color: var(--colors-muted);">Habis</span>
                    @endif
                </div>
                
                <!-- Product Meta -->
                <div class="property-card-meta">
                    <div class="property-card-title">
                        <span>{{ $product->name }}</span>
                        <span style="font-size: 11px; color: var(--colors-muted);">{{ $product->unit }}</span>
                    </div>
                    <p class="property-card-description">{{ $product->description ?? 'Bahan makanan berkualitas dari koperasi.' }}</p>
                    
                    <div class="property-card-price">
                        @auth
                            @if(auth()->user()->role === 'anggota')
                                <span>Rp {{ number_format($product->price_member, 0, ',', '.') }}</span>
                                <span class="price-strike">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                                <span class="member-tag">Anggota</span>
                            @else
                                <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            @endif
                        @else
                            <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            <span style="font-size: 11px; display: block; color: var(--colors-primary); font-weight: 500; margin-top: 2px;">
                                Hemat Rp {{ number_format($product->price_non_member - $product->price_member, 0, ',', '.') }} (Anggota)
                            </span>
                        @endauth
                    </div>

                    <!-- Quick Add To Cart Button (Indomaret/Alfamart direct purchase flow) -->
                    @if($product->current_stock > 0)
                        @auth
                            <button type="button" class="quick-buy-btn" onclick="quickAddToCart(event, {{ $product->id }}, '{{ $product->name }}')">
                                🛒 Beli Langsung
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="quick-buy-btn">
                                🔑 Masuk untuk Beli
                            </a>
                        @endauth
                    @else
                        <button class="quick-buy-btn" style="background-color: var(--colors-surface-soft) !important; color: var(--colors-muted) !important; cursor: not-allowed;" disabled>
                            Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Hidden Form for Quick Add to Cart -->
<form id="quick-cart-form" action="{{ route('cart.add') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="product_id" id="quick-cart-product-id">
    <input type="hidden" name="quantity" value="1">
</form>

<script>
    // Prevent quick buy button click from navigating to detail page
    function navigateToDetail(event, url) {
        // Only navigate if we didn't click inside a button
        if (!event.target.closest('.quick-buy-btn')) {
            window.location.href = url;
        }
    }

    // Quick Add to Cart AJAX simulation / Submit
    function quickAddToCart(event, productId, productName) {
        event.stopPropagation();
        
        // Get CSRF Token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Disable button visually
        const btn = event.target.closest('.quick-buy-btn');
        btn.textContent = 'Memproses...';
        btn.style.opacity = '0.7';
        btn.disabled = true;

        // Perform AJAX Request
        fetch("{{ route('cart.add') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            btn.textContent = '🛒 Beli Langsung';
            btn.style.opacity = '1';
            btn.disabled = false;

            if (data.success) {
                // Update badge count
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = data.cart_count;
                } else {
                    // Refresh page or inject badge element dynamically
                    window.location.reload();
                    return;
                }

                // Trigger SweetAlert popup
                window.showSweetAlert('Berhasil Belanja', productName + ' telah ditambahkan ke keranjang belanja Anda!', 'success');
            } else {
                window.showSweetAlert('Gagal Belanja', data.message || 'Stok tidak mencukupi.', 'error');
            }
        })
        .catch(error => {
            btn.textContent = '🛒 Beli Langsung';
            btn.style.opacity = '1';
            btn.disabled = false;
            
            // Fallback to normal form submit if AJAX endpoint is not supported yet
            document.getElementById('quick-cart-product-id').value = productId;
            document.getElementById('quick-cart-form').submit();
        });
    }

    // SKELETON LAZY LOADING TRANSITION
    document.addEventListener('DOMContentLoaded', function() {
        const skeleton = document.getElementById('skeleton-grid');
        const grid = document.getElementById('actual-product-grid');
        const emptyAlert = document.getElementById('product-results-empty');

        setTimeout(function() {
            if (skeleton) skeleton.style.display = 'none';
            if (grid) {
                grid.style.display = 'grid';
                setTimeout(() => {
                    grid.style.opacity = '1';
                }, 50);
            }
            if (emptyAlert) emptyAlert.style.display = 'block';
        }, 400); // 400ms delay to make loader visible and transition smooth
    });
</script>

@endsection
