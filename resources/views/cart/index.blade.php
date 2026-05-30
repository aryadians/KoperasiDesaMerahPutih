@extends('layouts.app')

@section('title', 'Keranjang Belanja - KDKMP')

@section('content')
<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Keranjang Belanja</h1>

@if(empty($cart))
    <div style="text-align: center; padding: 64px; border: 1px dashed var(--colors-hairline); border-radius: var(--rounded-md); color: var(--colors-muted);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 16px;">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <p style="font-size: 16px; margin-bottom: 24px;">Keranjang belanja Anda kosong.</p>
        <a href="{{ route('catalog.index') }}" class="button-primary" style="max-width: 250px; display: inline-flex;">Mulai Belanja</a>
    </div>
@else
    <!-- Cart Table and Checkout Form -->
    <div class="split-layout">
        
        <!-- Left: Items Table -->
        <div class="main-column">
            <form action="{{ route('cart.update') }}" method="POST" id="cart-update-form">
                @csrf
                <div class="standard-card" style="padding: 0; overflow: hidden;">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th style="width: 120px;">Kuantitas</th>
                                <th style="text-align: right;">Subtotal</th>
                                <th style="width: 80px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($cart as $id => $details)
                                @php
                                    $price = (auth()->check() && auth()->user()->role === 'anggota') 
                                        ? $details['price_member'] 
                                        : $details['price_non_member'];
                                    $subtotal = $price * $details['quantity'];
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">{{ $details['name'] }}</div>
                                        @if($details['is_local_product'])
                                            <span style="font-size: 11px; color: #1a7f5a; background-color: #e6f6f0; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Hasil Desa</span>
                                        @endif
                                    </td>
                                    <td>
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                        <span style="font-size: 12px; color: var(--colors-muted);">/{{ $details['unit'] }}</span>
                                    </td>
                                    <td>
                                        <input type="number" name="quantities[{{ $id }}]" value="{{ $details['quantity'] }}" min="1" class="text-input" style="height: 36px; padding: 4px 8px; text-align: center;" onchange="document.getElementById('cart-update-form').submit()">
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('cart.remove', $id) }}" style="color: var(--colors-primary); font-weight: 600; font-size: 13px;">Hapus</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-primary);">
                    ← Lanjut Belanja Sembako
                </a>
                <span style="font-size: 13px; color: var(--colors-muted);">Kuantitas otomatis diperbarui saat diganti.</span>
            </div>
        </div>

        <!-- Right: Checkout details -->
        <div class="sticky-rail">
            <div class="reservation-card">
                <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;">Ringkasan Belanja</h3>
                
                <div style="display: flex; justify-content: space-between; font-size: 15px;">
                    <span style="color: var(--colors-muted);">Total Belanja</span>
                    <strong style="font-size: 18px;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                </div>

                @if(auth()->check() && auth()->user()->role === 'anggota')
                    <div style="background-color: #e6f6f0; color: #1a7f5a; padding: 12px; border-radius: var(--rounded-sm); font-size: 13px; font-weight: 500;">
                        🎉 Anda menghemat uang dengan Harga Anggota Koperasi! Poin loyalitas diperoleh: 
                        <strong>{{ (int) floor($total / 10000) }} Poin</strong>
                    </div>
                @endif

                @if(auth()->check() && auth()->user()->role === 'anggota')
                    <form action="{{ route('cart.checkout') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px; margin-top: 8px;">
                        @csrf
                        
                        <div class="form-group">
                            <label for="delivery_type">Metode Pengiriman</label>
                            <select name="delivery_type" id="delivery_type" class="text-input" style="height: 48px; padding: 0 12px;" required>
                                <option value="pickup">Ambil di Gerai KDKMP (Gratis)</option>
                                <option value="delivery">Antar ke Rumah Warga (Kurir Desa)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="text-input" style="height: 48px; padding: 0 12px;" required>
                                <option value="cash">💵 Bayar Tunai di Gerai (COD)</option>
                                <option value="saldo_sukarela" {{ $sukarelaBalance < $total ? 'disabled' : '' }}>
                                    💳 Saldo Sukarela Koperasi (Rp {{ number_format($sukarelaBalance, 0, ',', '.') }}) 
                                    @if($sukarelaBalance < $total) — Saldo Kurang ⚠️ @endif
                                </option>
                                <option value="qris_desa">⚡ QRIS Desa (Bayar Instan)</option>
                            </select>
                        </div>

                        <button type="submit" class="button-primary">Buat Pesanan</button>
                    </form>
                @else
                    <div style="background-color: var(--colors-surface-soft); border: 1px solid var(--colors-hairline-soft); padding: 16px; border-radius: var(--rounded-md); margin-top: 12px; text-align: center; display: flex; flex-direction: column; gap: 10px;">
                        <p style="font-size: 13px; color: var(--colors-muted); line-height: 1.45;">
                            Anda belum masuk sebagai Anggota. Silakan masuk atau daftar sebagai anggota koperasi untuk melakukan pemesanan (checkout).
                        </p>
                        <a href="{{ route('login') }}" class="button-primary" style="display: flex; justify-content: center; align-items: center; height: 44px; border-radius: 100px; font-weight: 600;">
                            🔐 Masuk ke Akun
                        </a>
                        <a href="{{ route('register') }}" class="button-secondary" style="display: flex; justify-content: center; align-items: center; height: 44px; border-radius: 100px; border-color: var(--colors-primary); color: var(--colors-primary); font-weight: 600;">
                            ✨ Daftar Anggota Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endif
@endsection
