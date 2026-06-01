@extends('layouts.app')

@section('title', 'Riwayat Belanja Sembako Saya - KDKMP')

@section('content')
<style>
    .btn-3d-primary {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        height: 38px;
        padding: 0 20px;
        border-radius: 100px;
        font-size: 13px;
    }
    .btn-3d-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    }

    .btn-3d-secondary {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        color: var(--ink) !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), inset 0 1px 0 #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        padding: 0 16px;
        height: 32px;
        border-radius: 100px;
        font-size: 12px;
    }
    .btn-3d-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06), inset 0 1px 0 #ffffff !important;
        border-color: var(--muted) !important;
    }

    .card-3d {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04),
                    0 1px 2px rgba(0, 0, 0, 0.01),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .card-3d:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }
</style>

<div style="margin-bottom: 24px;" class="reveal-left">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 700; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)';" onmouseout="this.style.transform='translateX(0)';">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke Dashboard Anggota
    </a>
</div>

<div style="margin-bottom: 32px;" class="reveal-left delay-1">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--ink); letter-spacing: -0.8px; margin: 0;">🛍️ Daftar Pesanan Belanja Saya</h1>
    <p style="color: var(--muted); font-size: 14.5px; margin-top: 4px; margin-bottom: 0;">Pantau status pemesanan sembako dan riwayat transaksi belanja Anda.</p>
</div>

<div class="card-3d reveal-up delay-2" style="overflow: hidden;">
    @if($orders->isEmpty())
        <div style="padding: 64px 32px; text-align: center; color: var(--muted);">
            <div style="font-size: 64px; margin-bottom: 16px; animation: emoji-bounce 3s ease-in-out infinite;">🛍️</div>
            <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Belum Ada Riwayat Belanja</h3>
            <p style="font-size: 14.5px; max-width: 400px; margin: 0 auto 24px; line-height: 1.55;">Anda belum pernah melakukan pemesanan sembako di gerai digital koperasi.</p>
            <a href="{{ route('catalog.index') }}" class="btn-3d-primary">🛒 Mulai Belanja Sembako</a>
        </div>
    @else
        <div class="clean-table-container">
            <table class="clean-table" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th>Nomor Pesanan</th>
                        <th>Tanggal Pesan</th>
                        <th>Metode Pengantaran</th>
                        <th>Total Pembayaran</th>
                        <th>Poin Diperoleh</th>
                        <th>Status Pembayaran</th>
                        <th style="text-align: center; width: 120px;">Detail Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td style="font-weight: 800; color: var(--ink);">{{ $order->order_number }}</td>
                            <td style="color: var(--body); font-weight: 500;">{{ $order->created_at->format('d M Y H:i') }} WIB</td>
                            <td>
                                @if($order->delivery_type === 'pickup')
                                    <span class="badge badge-neutral" style="font-weight: 700; background: #f1f5f9; color: #475569; border: 1px solid rgba(71,85,105,0.12);">AMBIL DI GERAI</span>
                                @else
                                    <span class="badge badge-neutral" style="font-weight: 700; background: #e0f2fe; color: #0369a1; border: 1px solid rgba(3,105,161,0.12);">KIRIM KE RUMAH</span>
                                @endif
                            </td>
                            <td style="font-weight: 800; color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td style="font-weight: 800; color: #b45309;">⭐ {{ $order->points_earned }} Pts</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success" style="font-weight: 700;">LUNAS</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge badge-warning" style="font-weight: 700;">PENDING</span>
                                @else
                                    <span class="badge badge-danger" style="font-weight: 700;">BATAL</span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn-3d-secondary">Lihat Nota</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
