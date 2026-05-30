@extends('layouts.app')

@section('title', 'KDKMP Gerai Retail - Belanja Sembako Desa')

@section('content')

<!-- Global Pill Search Bar (DESIGN.md search-bar-pill) -->
<div class="search-bar-wrapper">
    <form action="{{ route('catalog.index') }}" method="GET" class="search-bar-pill">
        <!-- Segment 1: Search Name -->
        <div class="search-field-segment">
            <label for="search">Cari Barang</label>
            <input type="text" name="search" id="search" placeholder="Minyak, beras, gula..." value="{{ request('search') }}">
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
            <label for="local">Komoditas Lokal</label>
            <select name="local" id="local" onchange="this.form.submit()">
                <option value="">Semua Hasil Bumi</option>
                <option value="1" {{ request('local') == '1' ? 'selected' : '' }}>Khusus Petani Lokal</option>
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
<div class="category-strip">
    <a href="{{ route('catalog.index') }}" class="category-tab {{ !request('category') ? 'active' : '' }}">
        <span>Semua Sembako</span>
    </a>
    @foreach($categories as $category)
        <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="category-tab {{ request('category') == $category->id ? 'active' : '' }}">
            <span>{{ $category->name }}</span>
        </a>
    @endforeach
    <a href="{{ route('catalog.index', ['local' => 1]) }}" class="category-tab {{ request('local') == 1 ? 'active' : '' }}">
        <span style="color: #1a7f5a; font-weight: 600;">🌾 Hasil Tani Lokal</span>
    </a>
</div>

<!-- E-Commerce Product Grid (DESIGN.md grid-4 & property-card) -->
@if($products->isEmpty())
    <div style="text-align: center; padding: 48px; border: 1px dashed var(--colors-hairline); border-radius: var(--rounded-md); color: var(--colors-muted);">
        <p style="font-size: 16px;">Tidak ada produk yang cocok dengan pencarian Anda.</p>
        <a href="{{ route('catalog.index') }}" style="color: var(--colors-primary); font-weight: 600; text-decoration: underline; margin-top: 12px; display: inline-block;">Reset Filter</a>
    </div>
@else
    <div class="grid-4">
        @foreach($products as $product)
            <div class="property-card" onclick="window.location.href='{{ route('catalog.show', $product->id) }}'">
                <!-- Product Image Placeholder -->
                <div class="property-card-photo">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80" alt="{{ $product->name }}">
                    @if($product->is_local_product)
                        <span class="local-badge">Tani Desa</span>
                    @endif
                    @if($product->current_stock > 0 && $product->current_stock <= 5)
                        <span class="guest-favorite-badge" style="background-color: #ffebeb; color: #c13515;">Stok Menipis</span>
                    @elseif($product->current_stock === 0)
                        <span class="guest-favorite-badge" style="background-color: #f2f2f2; color: var(--colors-muted);">Habis</span>
                    @endif
                </div>
                
                <!-- Product Meta Details -->
                <div class="property-card-meta">
                    <div class="property-card-title">
                        <span>{{ $product->name }}</span>
                        <span style="font-size: 12px; font-weight: 500; color: var(--colors-muted);">{{ $product->unit }}</span>
                    </div>
                    <p class="property-card-description">{{ $product->description ?? 'Bahan makanan berkualitas dari koperasi desa.' }}</p>
                    
                    <div class="property-card-price">
                        @auth
                            @if(auth()->user()->role === 'anggota')
                                <!-- Member Pricing -->
                                <span>Rp {{ number_format($product->price_member, 0, ',', '.') }}</span>
                                <span class="price-strike">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                                <span class="member-tag">Harga Anggota</span>
                            @else
                                <!-- Non-Member Pricing -->
                                <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            @endif
                        @else
                            <!-- Guest / Public Pricing -->
                            <span>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            <span style="font-size: 11px; display: block; color: var(--colors-primary); font-weight: 500; margin-top: 2px;">
                                Gabung Anggota untuk harga khusus Rp {{ number_format($product->price_member, 0, ',', '.') }}!
                            </span>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
