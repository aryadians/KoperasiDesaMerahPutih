@extends('layouts.app')

@section('title', 'Keranjang Belanja - KDKMP')

@section('content')
<h1 class="reveal-left" style="font-size: 28px; font-weight: 800; margin-bottom: 24px; color: var(--ink); letter-spacing: -0.5px;">Keranjang Belanja</h1>

@if(empty($cart))
    <div class="reveal-scale" style="text-align: center; padding: 64px 32px; border: 2px dashed var(--hairline); background: var(--surface); border-radius: var(--r-xl); color: var(--muted);">
        <div style="font-size: 64px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite;">🛒</div>
        <p style="font-size: 18px; font-weight: 600; color: var(--ink); margin-bottom: 8px;">Keranjang belanja Anda masih kosong</p>
        <p style="font-size: 14px; margin-bottom: 24px;">Temukan kebutuhan sehari-hari dengan harga grosir terbaik di katalog kami.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg" style="border-radius: 100px;">
            Mulai Belanja Sembako
        </a>
    </div>
@else
    <!-- Cart Table and Checkout Form -->
    <div class="split-layout reveal-up">
        
        <!-- Left: Items Table -->
        <div class="main-column">
            <form action="{{ route('cart.update') }}" method="POST" id="cart-update-form">
                @csrf
                <div class="card card-flush" style="box-shadow: var(--shadow-sm);">
                    <div class="clean-table-container">
                        <table class="clean-table">
                            <thead style="background: var(--surface-md);">
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
                                    <tr style="transition: background var(--t-fast);">
                                        <td>
                                            <div style="font-weight: 600; color: var(--ink); font-size: 14px;">{{ $details['name'] }}</div>
                                            @if($details['is_local_product'])
                                                <span class="badge badge-success" style="margin-top: 4px;">🌾 Hasil Desa</span>
                                            @endif
                                        </td>
                                        <td style="color: var(--body);">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                            <span style="font-size: 11px; color: var(--muted);">/{{ $details['unit'] }}</span>
                                        </td>
                                        <td>
                                            <input type="number" name="quantities[{{ $id }}]" value="{{ $details['quantity'] }}" min="1" class="text-input" style="height: 36px; padding: 4px 8px; text-align: center; border-radius: var(--r-sm);" onchange="document.getElementById('cart-update-form').submit()">
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: var(--ink); font-size: 15px;">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="{{ route('cart.remove', $id) }}" style="color: var(--danger); font-weight: 600; font-size: 12px; padding: 6px 10px; border-radius: var(--r-full); transition: background var(--t-fast);" onmouseover="this.style.background='var(--danger-bg)'" onmouseout="this.style.background='transparent'">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('catalog.index') }}" style="font-size: 14px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 6px; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform='translateX(0)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Lanjut Belanja
                </a>
                <span style="font-size: 12px; color: var(--muted); background: var(--surface); padding: 4px 12px; border-radius: var(--r-full); border: 1px solid var(--hairline-soft);">
                    ℹ️ Kuantitas otomatis diperbarui saat diganti.
                </span>
            </div>
        </div>

        <!-- Right: Checkout details -->
        <div class="sticky-rail">
            <div class="card" style="box-shadow: var(--shadow-lg); border: 1.5px solid var(--hairline);">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); border-bottom: 1px solid var(--hairline); padding-bottom: 16px; margin-bottom: 20px;">Ringkasan Belanja</h3>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                    <span style="color: var(--muted); font-size: 14px; font-weight: 500;">Total Belanja</span>
                    <strong style="font-size: 28px; font-weight: 800; color: var(--ink); line-height: 1;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                </div>

                @if(auth()->check() && auth()->user()->role === 'anggota')
                    <div style="background-color: var(--success-bg); color: var(--success); padding: 12px 16px; border-radius: var(--r-md); border: 1px solid var(--success-border); font-size: 13px; font-weight: 500; margin-bottom: 24px; display: flex; gap: 10px; align-items: flex-start;">
                        <span style="font-size: 18px;">🎉</span>
                        <div>
                            Harga Hemat Anggota aktif! Poin loyalitas (SHU) diperoleh: 
                            <strong style="display: block; font-size: 15px; margin-top: 2px;">⭐ {{ (int) floor($total / 10000) }} Poin</strong>
                        </div>
                    </div>
                @endif

                @if(auth()->check() && auth()->user()->role === 'anggota')
                    <form action="{{ route('cart.checkout') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="delivery_type">Metode Pengiriman</label>
                            <select name="delivery_type" id="delivery_type" class="form-select" required>
                                <option value="pickup">Ambil di Gerai KDKMP (Gratis)</option>
                                <option value="delivery">Antar ke Rumah Warga (Kurir Desa)</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="payment_method">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="cash">💵 Bayar Tunai di Gerai (COD)</option>
                                <option value="saldo_sukarela" {{ $sukarelaBalance < $total ? 'disabled' : '' }}>
                                    💳 Saldo Koperasi (Rp {{ number_format($sukarelaBalance, 0, ',', '.') }}) 
                                    @if($sukarelaBalance < $total) — Saldo Kurang ⚠️ @endif
                                </option>
                                <option value="qris_desa">⚡ QRIS Desa (Bayar Instan)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-xl btn-full" style="margin-top: 12px; font-weight: 700; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            Konfirmasi Pesanan
                        </button>
                    </form>
                @else
                    <div style="background-color: var(--surface-md); border: 1px solid var(--hairline); padding: 20px 16px; border-radius: var(--r-md); text-align: center; display: flex; flex-direction: column; gap: 12px;">
                        <p style="font-size: 13px; color: var(--muted); line-height: 1.5; margin: 0;">
                            Anda belum masuk sebagai Anggota. Silakan masuk atau daftar sebagai anggota koperasi untuk melakukan <em>checkout</em> pesanan Anda.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-md btn-full" style="border-radius: 100px;">
                            🔐 Masuk ke Akun
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-secondary btn-md btn-full" style="border-radius: 100px;">
                            ✨ Daftar Anggota Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endif
@endsection
