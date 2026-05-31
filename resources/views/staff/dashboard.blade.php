@extends('layouts.admin')

@section('title', 'Dashboard Administrasi — KDKMP Digital')

@section('content')

{{-- ═══════════════════════ HERO HEADER ═══════════════════════ --}}
<div class="reveal" style="margin-bottom: 36px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <div class="pulse-ring" style="font-size: 12px; font-weight: 600; color: var(--success); margin-bottom: 10px; letter-spacing: 0.3px;">
                Sistem Aktif
            </div>
            <h1 style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin-bottom: 6px;">
                Dashboard Administrasi
            </h1>
            <p style="color: var(--muted); font-size: 15px;">
                Selamat bekerja, <strong style="color: var(--ink);">{{ ucfirst(auth()->user()->name) }}</strong>
                &nbsp;·&nbsp; <span style="background: var(--surface-strong); padding: 2px 10px; border-radius: 100px; font-size: 12px; font-weight: 600;">{{ ucfirst(auth()->user()->role) }}</span>
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
    <div style="height: 1px; background: var(--hairline-soft); margin-top: 24px;"></div>
</div>

{{-- ═══════════════════════ STAT CARDS ═══════════════════════ --}}
<div class="grid-3" style="margin-bottom: 36px;">

    <div class="stat-card reveal delay-1">
        <span class="stat-label">Total Omset Penjualan</span>
        <div class="stat-value" style="color: var(--success);"
             data-counter data-target="{{ $totalSales }}" data-prefix="Rp " data-suffix="">
            Rp {{ number_format($totalSales, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Dari pesanan gerai sembako lunas</p>
        <span class="stat-icon">💰</span>
    </div>

    <div class="stat-card reveal delay-2">
        <span class="stat-label">Penyaluran Hasil Tani</span>
        <div class="stat-value" style="color: var(--info);"
             data-counter data-target="{{ $totalCropPayout }}" data-prefix="Rp ">
            Rp {{ number_format($totalCropPayout, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Total modal penyerapan lokal lunas</p>
        <span class="stat-icon">🌾</span>
    </div>

    <div class="stat-card reveal delay-3">
        <span class="stat-label">Outstanding Kredit Mikro</span>
        <div class="stat-value" style="color: var(--primary);"
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
        <div class="standard-card" style="padding: 0; overflow: hidden; border-color: #fde2e2; box-shadow: 0 4px 16px rgba(220, 38, 38, 0.06);">
            <div style="padding: 18px 24px; background: linear-gradient(135deg, #fdf2f2, #fef6f6); border-bottom: 1px solid #fde8e8; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px; animation: badge-float 2s ease-in-out infinite;">⚠️</span>
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--danger);">Peringatan Stok Menipis</h3>
                    <p style="font-size: 12px; color: var(--danger); opacity: 0.8; margin-top: 2px;">Produk dengan stok &lt; 5 unit</p>
                </div>
            </div>

            @if($lowStockProducts->isEmpty())
                <div style="padding: 40px; text-align: center; color: var(--muted);">
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
                                    <span style="background: var(--surface-strong); padding: 2px 8px; border-radius: 100px; font-size: 12px;">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: var(--danger); font-weight: 700; font-size: 15px;">
                                        {{ $product->current_stock }}
                                    </span>
                                    <span style="color: var(--muted); font-size: 12px;"> {{ $product->unit }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('staff.products') }}" class="animated-link" style="color: var(--primary); font-weight: 600; font-size: 13px;">
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
                <p style="font-size: 13px; color: var(--muted); line-height: 1.6;">
                    Hitung estimasi pembagian Sisa Hasil Usaha (SHU) tahunan untuk anggota aktif secara merata berdasarkan poin loyalitas transaksi.
                </p>
            </div>
            <a href="{{ route('staff.shu') }}" class="button-primary" style="font-size: 14px; height: 44px; border-radius: 100px;">
                Buka Kalkulator SHU →
            </a>
        </div>

        {{-- Autodebet Setoran Wajib --}}
        <div class="reservation-card" style="margin-top: 20px; border-color: var(--success-border); background: var(--success-bg); padding: 24px;">
            <div>
                <div style="font-size: 32px; margin-bottom: 12px; animation: emoji-bounce 3s ease-in-out infinite;">🔄</div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px; color: var(--success);">Autodebet Setoran Wajib</h3>
                <p style="font-size: 13px; color: #35624f; line-height: 1.6; margin-bottom: 12px;">
                    Besaran Bulanan: <strong style="color: #165c42;">Rp {{ number_format($iuranWajibNominal, 0, ',', '.') }}</strong>
                </p>
                
                {{-- Payment stats --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px;">
                    <div style="background: rgba(255,255,255,0.5); padding: 8px; border-radius: 8px; text-align: center;">
                        <span style="font-size: 10px; color: var(--muted); font-weight: 600; text-transform: uppercase;">Lunas</span>
                        <div style="font-size: 16px; font-weight: 800; color: var(--success); margin-top: 2px;">{{ $paidCount }}</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.5); padding: 8px; border-radius: 8px; text-align: center;">
                        <span style="font-size: 10px; color: var(--muted); font-weight: 600; text-transform: uppercase;">Belum</span>
                        <div style="font-size: 16px; font-weight: 800; color: var(--danger); margin-top: 2px;">{{ $unpaidCount }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('staff.autodebet') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Memproses...';" style="margin: 0;">
                @csrf
                <button type="submit" class="button-primary" style="background: linear-gradient(135deg, var(--success), #165c42); font-size: 13px; height: 40px; border-radius: 100px; width: 100%; border: none; cursor: pointer; color: white; font-weight: 700;">
                    Jalankan Autodebet ➔
                </button>
            </form>

            {{-- Autodebet Logs --}}
            <div style="margin-top: 18px; border-top: 1px dashed rgba(22, 92, 66, 0.15); padding-top: 14px;">
                <h4 style="font-size: 12px; font-weight: 700; color: #165c42; margin-bottom: 8px;">Log Autodebet Terakhir</h4>
                @if($autodebetLogs->isEmpty())
                    <div style="font-size: 11px; color: #628374; font-style: italic; text-align: center; padding: 10px 0;">
                        Belum ada transaksi autodebet bulan ini.
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 6px; font-size: 11px; max-height: 120px; overflow-y: auto; padding-right: 4px;">
                        @foreach($autodebetLogs as $log)
                            <div style="display: flex; justify-content: space-between; align-items: center; color: #1e3d30; padding: 6px 8px; background: rgba(255,255,255,0.4); border-radius: 6px; border: 1px solid rgba(255,255,255,0.35);">
                                <span style="font-weight: 600;">{{ $log->member->user->name }}</span>
                                <span style="font-weight: 700; color: var(--danger); font-family: monospace;">-Rp {{ number_format(abs($log->amount), 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick links --}}
        <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('staff.loans') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border: 1px solid var(--hairline); border-radius: var(--r-md); font-size: 14px; font-weight: 500; transition: all 0.2s; background: var(--canvas);" onmouseover="this.style.borderColor='var(--ink)';this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='var(--hairline)';this.style.transform=''">
                <span>🏦 Manajemen Pinjaman</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
            <a href="{{ route('staff.crops') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border: 1px solid var(--hairline); border-radius: var(--r-md); font-size: 14px; font-weight: 500; transition: all 0.2s; background: var(--canvas);" onmouseover="this.style.borderColor='var(--ink)';this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='var(--hairline)';this.style.transform=''">
                <span>🌾 Penyerapan Tani</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </div>

</div>

@endsection
