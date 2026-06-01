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
    <style>
        /* 3D Glassmorphism & Styling */
        .card-3d {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: var(--r-lg);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04),
                        0 1px 2px rgba(0, 0, 0, 0.01),
                        inset 0 1px 0 #ffffff !important;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 24px;
        }
        .card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
        }
        
        .btn-3d-primary, .btn-3d-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: var(--r-full);
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            outline: none;
            text-decoration: none;
            gap: 8px;
        }
        .btn-3d-primary {
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
            color: #ffffff !important;
            box-shadow: 0 4px 12px var(--primary-glow), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .btn-3d-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--primary-glow), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-3d-secondary {
            background: linear-gradient(180deg, #ffffff, var(--surface-md));
            color: var(--ink) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03), inset 0 1px 0 #ffffff;
        }
        .btn-3d-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06), inset 0 1px 0 #ffffff;
        }
        
        .btn-full {
            width: 100%;
            display: flex;
        }

        .text-input-3d {
            border: 1.5px solid var(--hairline);
            border-radius: var(--r-sm);
            padding: 8px 12px;
            font-weight: 600;
            color: var(--ink);
            background: #ffffff;
            box-shadow: inset 0 1.5px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }
        .text-input-3d:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: inset 0 1.5px 3px rgba(0, 0, 0, 0.03), 0 0 0 3px var(--primary-glow);
            transform: translateY(-1px);
        }
        
        .form-select-3d {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
            border: 1.5px solid var(--hairline);
            border-radius: var(--r-sm);
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .form-select-3d:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04), 0 0 0 3px var(--primary-glow);
        }

        @keyframes float-emoji {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(4deg); }
        }
    </style>

    <!-- Cart Table and Checkout Form -->
    <div class="split-layout reveal-up">
        
        <!-- Left: Items Table -->
        <div class="main-column">
            <form action="{{ route('cart.update') }}" method="POST" id="cart-update-form">
                @csrf
                <div class="card-3d" style="padding: 0; overflow: hidden;">
                    <div class="clean-table-container">
                        <table class="clean-table">
                            <thead style="background: var(--surface);">
                                <tr>
                                    <th style="padding: 16px 24px;">Produk</th>
                                    <th>Harga Satuan</th>
                                    <th style="width: 120px;">Kuantitas</th>
                                    <th style="text-align: right;">Subtotal</th>
                                    <th style="width: 80px; text-align: center; padding-right: 24px;">Aksi</th>
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
                                        <td style="padding: 16px 24px;">
                                            <div style="font-weight: 700; color: var(--ink); font-size: 14.5px;">{{ $details['name'] }}</div>
                                            @if($details['is_local_product'])
                                                <span class="badge badge-success" style="margin-top: 4px; font-weight: 700; background: var(--success-bg); color: var(--success); border: 1.5px solid var(--success-border);">🌾 Hasil Desa</span>
                                            @endif
                                        </td>
                                        <td style="color: var(--body); font-weight: 550;">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                            <span style="font-size: 11px; color: var(--muted); font-weight: 400;">/{{ $details['unit'] }}</span>
                                        </td>
                                        <td>
                                            <input type="number" name="quantities[{{ $id }}]" value="{{ $details['quantity'] }}" min="1" class="text-input-3d" style="height: 36px; padding: 4px 8px; width: 70px; text-align: center; font-weight: 700;" onchange="document.getElementById('cart-update-form').submit()">
                                        </td>
                                        <td style="text-align: right; font-weight: 800; color: var(--primary); font-size: 15px;">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: center; padding-right: 24px;">
                                            <a href="{{ route('cart.remove', $id) }}" style="color: var(--danger); font-weight: 700; font-size: 12px; padding: 6px 12px; border-radius: var(--r-full); transition: all var(--t-fast);" onmouseover="this.style.background='var(--danger-bg)'" onmouseout="this.style.background='transparent'">
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
                <a href="{{ route('catalog.index') }}" class="btn-3d-secondary" style="font-size: 13.5px; padding: 8px 16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Lanjut Belanja
                </a>
                <span style="font-size: 12px; color: var(--muted); background: var(--surface); padding: 6px 14px; border-radius: var(--r-full); border: 1.5px solid var(--hairline-soft); font-weight: 500;">
                    ℹ️ Kuantitas otomatis diperbarui saat diganti.
                </span>
            </div>
        </div>

        <!-- Right: Checkout details -->
        <div class="sticky-rail">
            <div class="card-3d">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); border-bottom: 1.5px dashed var(--hairline); padding-bottom: 16px; margin-bottom: 20px;">Ringkasan Belanja</h3>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
                    <span style="color: var(--muted); font-size: 14px; font-weight: 600;">Total Belanja</span>
                    <strong style="font-size: 28px; font-weight: 800; color: var(--ink); line-height: 1; letter-spacing: -0.5px;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                </div>

                @if(auth()->check() && auth()->user()->role === 'anggota')
                    <div style="background-color: var(--success-bg); color: var(--success); padding: 12px 16px; border-radius: var(--r-md); border: 1.5px solid var(--success-border); font-size: 13px; font-weight: 500; margin-bottom: 24px; display: flex; gap: 10px; align-items: flex-start; box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);">
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
                        
                        <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column; gap: 6px;">
                            <label for="delivery_type" style="font-weight: 700; font-size: 13px; color: var(--body);">Metode Pengiriman</label>
                            <select name="delivery_type" id="delivery_type" class="form-select-3d" required>
                                <option value="pickup">Ambil di Gerai KDKMP (Gratis)</option>
                                <option value="delivery">Antar ke Rumah Warga (Kurir Desa)</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column; gap: 6px;">
                            <label for="payment_method" style="font-weight: 700; font-size: 13px; color: var(--body);">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-select-3d" required>
                                <option value="cash">💵 Bayar Tunai di Gerai (COD)</option>
                                <option value="saldo_sukarela" {{ $sukarelaBalance < $total ? 'disabled' : '' }}>
                                    💳 Saldo Koperasi (Rp {{ number_format($sukarelaBalance, 0, ',', '.') }}) 
                                    @if($sukarelaBalance < $total) — Saldo Kurang ⚠️ @endif
                                </option>
                                <option value="qris_desa">⚡ QRIS Desa (Bayar Instan)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-3d-primary btn-full" style="margin-top: 12px; font-weight: 700; font-size: 15px; padding: 12px 20px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            Konfirmasi Pesanan
                        </button>
                    </form>
                @else
                    <div style="background-color: var(--surface); border: 1.5px solid var(--hairline-soft); padding: 20px 16px; border-radius: var(--r-md); text-align: center; display: flex; flex-direction: column; gap: 12px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                        <p style="font-size: 13px; color: var(--muted); line-height: 1.55; margin: 0; font-weight: 500;">
                            Anda belum masuk sebagai Anggota. Silakan masuk atau daftar sebagai anggota koperasi untuk melakukan <em>checkout</em> pesanan Anda.
                        </p>
                        <a href="{{ route('login') }}" class="btn-3d-primary btn-full" style="padding: 10px 20px;">
                            🔐 Masuk ke Akun
                        </a>
                        <a href="{{ route('register') }}" class="btn-3d-secondary btn-full" style="padding: 10px 20px;">
                            ✨ Daftar Anggota Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endif
@endsection
