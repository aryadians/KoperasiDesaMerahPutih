@extends('layouts.app')

@section('title', 'Dashboard Administrasi — KDKMP Digital')

@section('content')

{{-- ═══════════════════════ HERO HEADER ═══════════════════════ --}}
<div class="reveal" style="margin-bottom: 36px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div class="pulse-ring" style="font-size: 12px; font-weight: 600; color: var(--colors-success); margin-bottom: 10px; letter-spacing: 0.3px;">
                Sistem Aktif
            </div>
            <h1 style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px; color: var(--colors-ink); margin-bottom: 6px;">
                Dashboard Administrasi
            </h1>
            <p style="color: var(--colors-muted); font-size: 15px;">
                Selamat bekerja, <strong style="color: var(--colors-ink);">{{ ucfirst(auth()->user()->name) }}</strong>
                &nbsp;·&nbsp; <span style="background: var(--colors-surface-strong); padding: 2px 10px; border-radius: 100px; font-size: 12px; font-weight: 600;">{{ ucfirst(auth()->user()->role) }}</span>
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('staff.products') }}" class="button-secondary" style="width: auto; height: 42px; padding: 0 20px; font-size: 14px; border-radius: 100px;">
                📦 Inventaris
            </a>
            <a href="{{ route('staff.orders') }}" class="button-primary" style="width: auto; height: 42px; padding: 0 20px; font-size: 14px; border-radius: 100px;">
                🛍 Kelola Pesanan
            </a>
        </div>
    </div>
    <div style="height: 1px; background: var(--colors-hairline-soft); margin-top: 24px;"></div>
</div>

{{-- ═══════════════════════ STAT CARDS ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 36px;">

    <div class="stat-card reveal delay-1">
        <span class="stat-label">Total Omset Penjualan</span>
        <div class="stat-value" style="color: var(--colors-success);"
             data-counter data-target="{{ $totalSales }}" data-prefix="Rp " data-suffix="">
            Rp {{ number_format($totalSales, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Dari pesanan gerai sembako lunas</p>
        <span class="stat-icon">💰</span>
    </div>

    <div class="stat-card reveal delay-2">
        <span class="stat-label">Penyaluran Hasil Tani</span>
        <div class="stat-value" style="color: var(--colors-info);"
             data-counter data-target="{{ $totalCropPayout }}" data-prefix="Rp ">
            Rp {{ number_format($totalCropPayout, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Total modal penyerapan lokal lunas</p>
        <span class="stat-icon">🌾</span>
    </div>

    <div class="stat-card reveal delay-3">
        <span class="stat-label">Outstanding Kredit Mikro</span>
        <div class="stat-value" style="color: var(--colors-primary);"
             data-counter data-target="{{ $activeLoansVolume }}" data-prefix="Rp ">
            Rp {{ number_format($activeLoansVolume, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Modal usaha bergulir yang aktif</p>
        <span class="stat-icon">🏦</span>
    </div>

</div>

{{-- ═══════════════════════ NAV CARDS ═══════════════════════ --}}
<div class="dashboard-nav-grid reveal">
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.products') }}'">
        <span class="nav-card-icon">📦</span>
        <h3>Kelola Inventaris</h3>
        <p>Atur katalog sembako, stok barang, dan harga khusus anggota.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.orders') }}'">
        <span class="nav-card-icon">🛍️</span>
        <h3>Pesanan Gerai
            @if($pendingOrdersCount > 0)
                <span class="badge-count" style="margin-left: 6px;">{{ $pendingOrdersCount }}</span>
            @endif
        </h3>
        <p>Proses pembayaran kasir dan pengantaran belanjaan warga.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.crops') }}'">
        <span class="nav-card-icon">🌾</span>
        <h3>Hasil Panen Tani
            @if($pendingCropsCount > 0)
                <span class="badge-count" style="margin-left: 6px;">{{ $pendingCropsCount }}</span>
            @endif
        </h3>
        <p>Verifikasi barang masuk dan pelunasan pembayaran petani desa.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.loans') }}'">
        <span class="nav-card-icon">🏦</span>
        <h3>Underwriting Kredit
            @if($pendingLoansCount > 0)
                <span class="badge-count" style="margin-left: 6px;">{{ $pendingLoansCount }}</span>
            @endif
        </h3>
        <p>Review pengajuan pinjaman UMKM dan catat pembayaran cicilan.</p>
    </div>
</div>

{{-- ═══════════════════════ SPLIT: Stok & SHU ═══════════════════════ --}}
<div class="split-layout">

    {{-- Low Stock Alert --}}
    <div class="main-column reveal-left">
        <div class="standard-card" style="padding: 0; overflow: hidden; border-color: #fde2e2; box-shadow: 0 4px 16px rgba(193,53,21,0.06);">
            <div style="padding: 18px 24px; background: linear-gradient(135deg, #fdf2f2, #fef6f6); border-bottom: 1px solid #fde8e8; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; animation: badge-float 2s ease-in-out infinite;">⚠️</span>
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--colors-primary-error-text);">Peringatan Stok Menipis</h3>
                    <p style="font-size: 12px; color: #c13515; opacity: 0.8; margin-top: 2px;">Produk dengan stok &lt; 5 unit</p>
                </div>
            </div>

            @if($lowStockProducts->isEmpty())
                <div style="padding: 40px; text-align: center; color: var(--colors-muted);">
                    <div style="font-size: 40px; margin-bottom: 12px;">✅</div>
                    <p style="font-weight: 600; margin-bottom: 4px;">Semua Stok Aman</p>
                    <p style="font-size: 13px;">Tidak ada produk yang menipis saat ini.</p>
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $idx => $product)
                            <tr style="animation-delay: {{ $idx * 0.05 }}s;">
                                <td style="font-weight: 600;">{{ $product->name }}</td>
                                <td>
                                    <span style="background: var(--colors-surface-strong); padding: 2px 8px; border-radius: 100px; font-size: 12px;">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: var(--colors-primary-error-text); font-weight: 700; font-size: 15px;">
                                        {{ $product->current_stock }}
                                    </span>
                                    <span style="color: var(--colors-muted); font-size: 12px;"> {{ $product->unit }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('staff.products') }}" class="animated-link" style="color: var(--colors-primary); font-weight: 600; font-size: 13px;">
                                        + Tambah Stok
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- SHU Calculator --}}
    <div class="sticky-rail reveal-right">
        <div class="reservation-card">
            <div>
                <div style="font-size: 32px; margin-bottom: 12px; animation: emoji-bounce 3s ease-in-out infinite;">📊</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Kalkulator SHU</h3>
                <p style="font-size: 13px; color: var(--colors-muted); line-height: 1.6;">
                    Hitung estimasi pembagian Sisa Hasil Usaha (SHU) tahunan untuk anggota aktif secara merata berdasarkan poin loyalitas transaksi.
                </p>
            </div>
            <a href="{{ route('staff.shu') }}" class="button-primary" style="font-size: 14px; height: 44px; border-radius: 100px;">
                Buka Kalkulator SHU →
            </a>
        </div>

        {{-- Autodebet Setoran Wajib --}}
        <div class="reservation-card" style="margin-top: 20px; border-color: var(--success-border); background: var(--success-bg);">
            <div>
                <div style="font-size: 32px; margin-bottom: 12px; animation: emoji-bounce 3s ease-in-out infinite;">🔄</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--success);">Autodebet Setoran Wajib</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.6;">
                    Jalankan pemotongan otomatis iuran wajib bulanan sebesar <strong>Rp 50.000</strong> dari saldo Simpanan Sukarela anggota yang aktif.
                </p>
            </div>
            <form action="{{ route('staff.autodebet') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Memproses...';">
                @csrf
                <button type="submit" class="button-primary" style="background: linear-gradient(135deg, var(--success), #165c42); font-size: 14px; height: 44px; border-radius: 100px; width: 100%; border: none; cursor: pointer; color: white;">
                    Jalankan Autodebet ➔
                </button>
            </form>
        </div>

        {{-- Quick links --}}
        <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('staff.loans') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); font-size: 14px; font-weight: 500; transition: all 0.2s; background: var(--colors-canvas);" onmouseover="this.style.borderColor='var(--colors-ink)';this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='var(--colors-hairline)';this.style.transform=''">
                <span>🏦 Manajemen Pinjaman</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
            <a href="{{ route('staff.crops') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); font-size: 14px; font-weight: 500; transition: all 0.2s; background: var(--colors-canvas);" onmouseover="this.style.borderColor='var(--colors-ink)';this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='var(--colors-hairline)';this.style.transform=''">
                <span>🌾 Penyerapan Tani</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </div>

</div>

@endsection
