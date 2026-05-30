@extends('layouts.app')

@section('title', $product->name . ' - KDKMP Gerai')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke katalog
    </a>
</div>

<!-- Split Layout: Left Info, Right Purchase Rail (DESIGN.md split-layout) -->
<div class="split-layout">
    
    <!-- Left Column: Details -->
    <div class="main-column">
        <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 8px;">{{ $product->name }}</h1>
        <p style="font-size: 14px; color: var(--colors-muted); margin-bottom: 24px;">
            Kategori: <strong style="color: var(--colors-ink);">{{ $product->category->name }}</strong>
            @if($product->is_local_product)
                • <span style="color: #1a7f5a; font-weight: 600;">🌾 Komoditas Lokal Desa</span>
            @endif
        </p>

        <!-- Product Hero Image -->
        <div style="width: 100%; height: 400px; border-radius: var(--rounded-md); overflow: hidden; background-color: var(--colors-surface-strong); margin-bottom: 32px;">
            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div class="standard-card">
            <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">Deskripsi Produk</h2>
            <p style="font-size: 15px; color: var(--colors-body); line-height: 1.6;">
                {{ $product->description ?? 'Bahan makanan pokok berkualitas tinggi disuplai langsung melalui koperasi Koperasi Desa Merah Putih untuk menjamin harga terbaik bagi masyarakat desa.' }}
            </p>
        </div>

        <div class="standard-card">
            <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">Spesifikasi</h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 8px;">
                    <span style="color: var(--colors-muted);">Satuan Ukur</span>
                    <span style="font-weight: 600;">{{ $product->unit }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 8px;">
                    <span style="color: var(--colors-muted);">Asal Komoditas</span>
                    <span style="font-weight: 600;">{{ $product->is_local_product ? 'Petani Lokal Desa Merah Putih' : 'Distributor Nasional' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 8px;">
                    <span style="color: var(--colors-muted);">Ketersediaan Stok</span>
                    <span style="font-weight: 600; color: {{ $product->current_stock > 0 ? '#1a7f5a' : '#c13515' }}">
                        {{ $product->current_stock }} {{ $product->unit }} ({{ $product->current_stock > 0 ? 'Tersedia' : 'Habis' }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sticky Cart Drawer (DESIGN.md reservation-card) -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <div>
                <!-- Primary price display -->
                @auth
                    @if(auth()->user()->role === 'anggota')
                        <div style="font-size: 24px; font-weight: 700;">
                            Rp {{ number_format($product->price_member, 0, ',', '.') }}
                            <span style="font-size: 14px; font-weight: 500; color: var(--colors-muted);">/ {{ $product->unit }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--colors-muted); margin-top: 4px; text-decoration: line-through;">
                            Harga Non-Anggota: Rp {{ number_format($product->price_non_member, 0, ',', '.') }}
                        </div>
                        <span class="member-tag" style="margin-left: 0; margin-top: 8px; display: inline-block;">Diskon Anggota Koperasi Aktif</span>
                    @else
                        <div style="font-size: 24px; font-weight: 700;">
                            Rp {{ number_format($product->price_non_member, 0, ',', '.') }}
                            <span style="font-size: 14px; font-weight: 500; color: var(--colors-muted);">/ {{ $product->unit }}</span>
                        </div>
                    @endif
                @else
                    <div style="font-size: 24px; font-weight: 700;">
                        Rp {{ number_format($product->price_non_member, 0, ',', '.') }}
                        <span style="font-size: 14px; font-weight: 500; color: var(--colors-muted);">/ {{ $product->unit }}</span>
                    </div>
                    <div style="font-size: 12px; color: var(--colors-primary); font-weight: 500; margin-top: 8px;">
                        Harga Anggota: Rp {{ number_format($product->price_member, 0, ',', '.') }} (Hubungi pengurus untuk mendaftar!)
                    </div>
                @endauth
            </div>

            <!-- Form: Add to Cart -->
            @if($product->current_stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="form-group">
                        <label for="quantity">Kuantitas Belanja</label>
                        <select name="quantity" id="quantity" class="text-input" style="height: 48px; padding: 0 16px;">
                            @for($i = 1; $i <= min($product->current_stock, 10); $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $product->unit }}</option>
                            @endfor
                        </select>
                    </div>

                    @auth
                        <button type="submit" class="button-primary">Tambah ke Keranjang</button>
                    @else
                        <a href="{{ route('login') }}" class="button-primary" style="text-align: center;">Masuk untuk Membeli</a>
                    @endauth
                </form>
            @else
                <button class="button-primary" style="background-color: var(--colors-hairline); color: var(--colors-muted); cursor: not-allowed;" disabled>
                    Stok Habis
                </button>
            @endif

            <div style="font-size: 12px; color: var(--colors-muted); text-align: center;">
                @auth
                    Pembayaran disimulasikan secara cashless / potong saldo tabungan sukarela.
                @else
                    Harus memiliki akun terdaftar untuk memesan sembako.
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
