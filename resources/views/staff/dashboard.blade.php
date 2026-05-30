@extends('layouts.app')

@section('title', 'Dashboard Staf Koperasi - KDKMP')

@section('content')
<div style="border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 24px; margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600;">Dashboard Administrasi Koperasi</h1>
    <p style="color: var(--colors-muted); font-size: 14px; margin-top: 4px;">Selamat bekerja, Anda masuk sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong>.</p>
</div>

<!-- Cooperative Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
    <div style="padding: 24px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas); box-shadow: var(--shadow-tier);">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--colors-muted);">Total Omset Penjualan</span>
        <div style="font-size: 24px; font-weight: 700; margin-top: 8px; color: #1a7f5a;">
            Rp {{ number_format($totalSales, 0, ',', '.') }}
        </div>
        <p style="font-size: 12px; color: var(--colors-muted); margin-top: 4px;">Dari pesanan gerai sembako lunas.</p>
    </div>
    
    <div style="padding: 24px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas); box-shadow: var(--shadow-tier);">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--colors-muted);">Penyaluran Hasil Tani</span>
        <div style="font-size: 24px; font-weight: 700; margin-top: 8px; color: #0052cc;">
            Rp {{ number_format($totalCropPayout, 0, ',', '.') }}
        </div>
        <p style="font-size: 12px; color: var(--colors-muted); margin-top: 4px;">Total modal penyerapan lokal lunas.</p>
    </div>

    <div style="padding: 24px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas); box-shadow: var(--shadow-tier);">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--colors-muted);">Outstanding Kredit Mikro</span>
        <div style="font-size: 24px; font-weight: 700; margin-top: 8px; color: var(--colors-primary);">
            Rp {{ number_format($activeLoansVolume, 0, ',', '.') }}
        </div>
        <p style="font-size: 12px; color: var(--colors-muted); margin-top: 4px;">Modal usaha bergulir yang aktif.</p>
    </div>
</div>

<!-- Administration Links Grid -->
<div class="dashboard-nav-grid" style="margin-bottom: 32px;">
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.products') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">📦</span>
        <h3>Kelola Inventaris</h3>
        <p>Atur katalog sembako, stok barang, dan harga khusus anggota.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.orders') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🛍️</span>
        <h3>Pesanan Gerai ({{ $pendingOrdersCount }})</h3>
        <p>Proses pembayaran kasir dan pengantaran belanjaan warga.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.crops') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🌾</span>
        <h3>Hasil Panen Tani ({{ $pendingCropsCount }})</h3>
        <p>Verifikasi barang masuk dan pelunasan pembayaran petani desa.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('staff.loans') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🏦</span>
        <h3>Underwriting Kredit ({{ $pendingLoansCount }})</h3>
        <p>Review pengajuan pinjaman UMKM dan catat pembayaran cicilan.</p>
    </div>
</div>

<div class="split-layout">
    
    <!-- Left: Low stock alerts -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden; border-color: #fde2e2;">
            <h3 style="font-size: 16px; font-weight: 600; padding: 20px; background-color: #fdf2f2; color: var(--colors-primary-error-text); border-bottom: 1px solid var(--colors-primary-disabled); display: flex; align-items: center; gap: 8px;">
                ⚠️ Peringatan Stok Menipis (< 5 Unit)
            </h3>
            
            @if($lowStockProducts->isEmpty())
                <div style="padding: 24px; text-align: center; color: var(--colors-muted); font-size: 14px;">
                    Semua stok produk gerai aman.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok Saat Ini</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr>
                                <td style="font-weight: 600;">{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td style="color: var(--colors-primary-error-text); font-weight: 700;">
                                    {{ $product->current_stock }} {{ $product->unit }}
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('staff.products', ['search' => $product->name]) }}" style="color: var(--colors-primary); font-weight: 600; font-size: 13px;">Tambah Stok</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Calculation Tools (SHU share) -->
    <div class="sticky-rail">
        <div class="standard-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px; margin-bottom: 16px;">
                Kalkulator SHU Koperasi
            </h3>
            <p style="font-size: 13px; color: var(--colors-muted); line-height: 1.5; margin-bottom: 20px;">
                Hitung estimasi pembagian Sisa Hasil Usaha (SHU) tahunan untuk anggota aktif secara merata berdasarkan poin loyalitas.
            </p>
            <a href="{{ route('staff.shu') }}" class="button-secondary" style="font-size: 14px; height: 40px;">Buka Kalkulator SHU</a>
        </div>
    </div>

</div>
@endsection
