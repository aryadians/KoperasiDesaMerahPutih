@extends('layouts.app')

@section('title', $product->name . ' - KDKMP Gerai')

@section('content')
<div style="margin-bottom: 24px;" class="reveal-left">
    <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 600; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; transition: color var(--t-fast), transform var(--t-fast);" onmouseover="this.style.color='var(--ink)'; this.style.transform='translateX(-4px)';" onmouseout="this.style.color='var(--muted)'; this.style.transform='translateX(0)';">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke katalog
    </a>
</div>

<!-- Split Layout: Left Info, Right Purchase Rail (DESIGN.md split-layout) -->
<div class="split-layout">
    
    <!-- Left Column: Details -->
    <div class="main-column reveal-scale">
        <div style="margin-bottom: 20px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                <h1 style="font-size: 32px; font-weight: 800; color: var(--ink); line-height: 1.2; letter-spacing: -0.5px; margin: 0;">{{ $product->name }}</h1>
                <div style="background: var(--surface-md); padding: 4px 12px; border-radius: var(--r-full); font-size: 13px; font-weight: 600; color: var(--body); display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    {{ $product->category->name }}
                </div>
            </div>
            
            <div style="margin-top: 12px; display: flex; gap: 12px;">
                @if($product->is_local_product)
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: var(--success); background: var(--success-bg); border: 1px solid var(--success-border); padding: 4px 12px; border-radius: var(--r-full);">
                        🌾 Komoditas Lokal Desa
                    </span>
                @endif
            </div>
        </div>

        <!-- Product Hero Image -->
        <div style="width: 100%; aspect-ratio: 16/10; border-radius: var(--r-xl); overflow: hidden; background-color: var(--surface-md); margin-bottom: 36px; box-shadow: var(--shadow-sm); border: 1px solid var(--hairline-soft);">
            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
        </div>

        <div class="card reveal-up delay-1" style="margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--ink); margin-bottom: 16px; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">Deskripsi Produk</h2>
            <p style="font-size: 15px; color: var(--body); line-height: 1.65;">
                {{ $product->description ?? 'Bahan makanan pokok berkualitas tinggi disuplai langsung melalui Koperasi ' . $product->branch->name . ' untuk menjamin harga terbaik bagi masyarakat desa.' }}
            </p>
        </div>

        <div class="card reveal-up delay-2">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--ink); margin-bottom: 16px; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">Spesifikasi Produk</h2>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 10px;">
                    <span style="color: var(--muted); font-size: 14px;">Satuan Ukur</span>
                    <span style="font-weight: 700; color: var(--ink);">{{ $product->unit }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 10px;">
                    <span style="color: var(--muted); font-size: 14px;">Asal Komoditas</span>
                    <span style="font-weight: 700; color: var(--ink);">{{ $product->is_local_product ? 'Petani Lokal ' . $product->branch->name : 'Distributor Nasional' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 4px;">
                    <span style="color: var(--muted); font-size: 14px;">Ketersediaan Stok</span>
                    <span style="font-weight: 800; font-size: 15px; color: {{ $product->current_stock > 0 ? 'var(--success)' : 'var(--danger)' }}">
                        {{ $product->current_stock }} <span style="font-size: 13px; font-weight: 600;">{{ $product->unit }}</span>
                        @if($product->current_stock > 0 && $product->current_stock <= 5)
                            <span style="color: var(--danger); font-size: 12px; margin-left: 6px;">(Sisa sedikit!)</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sticky Cart Drawer -->
    <div class="sticky-rail reveal-right">
        <div class="card" style="box-shadow: var(--shadow-lg); border: 1.5px solid var(--hairline); padding: 32px 28px;">
            <div style="margin-bottom: 28px;">
                <!-- Primary price display -->
                @auth
                    @if(auth()->user()->role === 'anggota')
                        <div style="font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">🎉 Harga Spesial Anggota</div>
                        <div style="font-size: 32px; font-weight: 800; color: var(--ink); line-height: 1;">
                            Rp {{ number_format($product->price_member, 0, ',', '.') }}
                            <span style="font-size: 16px; font-weight: 600; color: var(--muted);">/ {{ $product->unit }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                            <span style="font-size: 14px; color: var(--muted); text-decoration: line-through;">Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</span>
                            <span class="badge badge-success">Hemat Rp {{ number_format($product->price_non_member - $product->price_member, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div style="font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Harga Normal</div>
                        <div style="font-size: 32px; font-weight: 800; color: var(--ink); line-height: 1;">
                            Rp {{ number_format($product->price_non_member, 0, ',', '.') }}
                            <span style="font-size: 16px; font-weight: 600; color: var(--muted);">/ {{ $product->unit }}</span>
                        </div>
                    @endif
                @else
                    <div style="font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Harga Normal</div>
                    <div style="font-size: 32px; font-weight: 800; color: var(--ink); line-height: 1;">
                        Rp {{ number_format($product->price_non_member, 0, ',', '.') }}
                        <span style="font-size: 16px; font-weight: 600; color: var(--muted);">/ {{ $product->unit }}</span>
                    </div>
                    
                    <div style="margin-top: 16px; padding: 12px; border-radius: var(--r-md); background: var(--primary-light); border: 1px dashed var(--primary-muted);">
                        <div style="font-size: 13px; font-weight: 700; color: var(--primary-dark);">Khusus Anggota: Rp {{ number_format($product->price_member, 0, ',', '.') }}</div>
                        <div style="font-size: 12px; color: var(--primary-dark); opacity: 0.8; margin-top: 4px;">
                            <a href="{{ route('register') }}" style="text-decoration: underline; font-weight: 600;">Daftar akun anggota</a> sekarang untuk nikmati harga grosir desa!
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Form: Add to Cart -->
            @if($product->current_stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="quantity" style="margin-bottom: 8px;">Tentukan Jumlah</label>
                        <div style="position: relative;">
                            <select name="quantity" id="quantity" class="text-input" style="height: 52px; padding: 0 16px; font-size: 15px; font-weight: 600; cursor: pointer; appearance: none; background-color: var(--surface);">
                                @for($i = 1; $i <= min($product->current_stock, 10); $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $product->unit }}</option>
                                @endfor
                            </select>
                            <svg style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    @auth
                        <button type="submit" class="btn btn-primary btn-xl btn-full" style="font-weight: 700; font-size: 16px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            Masukkan Keranjang
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-xl btn-full" style="font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                            🔐 Masuk untuk Membeli
                        </a>
                    @endauth
                </form>
            @else
                <button class="btn btn-xl btn-full" style="background-color: var(--surface-md); color: var(--muted); border: 1.5px solid var(--hairline); cursor: not-allowed; font-weight: 700;" disabled>
                    ❌ Stok Habis
                </button>
            @endif

            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--hairline-soft); color: var(--muted); font-size: 12px; text-align: center;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Transparansi SHU Belanja Warga
            </div>
        </div>
    </div>
</div>
@endsection
