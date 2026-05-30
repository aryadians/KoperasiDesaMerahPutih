@extends('layouts.app')

@section('title', 'Riwayat Belanja Sembako Saya - KDKMP')

@section('content')
<div style="margin-bottom: 24px;" class="reveal-left">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; transition: color var(--t-fast), transform var(--t-fast);" onmouseover="this.style.color='var(--ink)'; this.style.transform='translateX(-4px)';" onmouseout="this.style.color='var(--muted)'; this.style.transform='translateX(0)';">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke Dashboard Anggota
    </a>
</div>

<h1 class="reveal-left delay-1" style="font-size: 28px; font-weight: 800; margin-bottom: 24px; color: var(--ink); letter-spacing: -0.5px;">Daftar Pesanan Belanja Saya</h1>

<div class="card card-flush reveal-up delay-2" style="box-shadow: var(--shadow-sm);">
    @if($orders->isEmpty())
        <div style="padding: 64px 32px; text-align: center; color: var(--muted);">
            <div style="font-size: 64px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite;">🛍️</div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Belum Ada Riwayat Belanja</h3>
            <p style="font-size: 14px; max-width: 400px; margin: 0 auto 24px; line-height: 1.5;">Anda belum pernah melakukan pemesanan sembako di gerai digital koperasi.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-lg" style="border-radius: 100px;">🛒 Mulai Belanja Sembako</a>
        </div>
    @else
        <div class="clean-table-container">
            <table class="clean-table" style="margin-top: 0;">
                <thead style="background: var(--surface-md);">
                    <tr>
                        <th>Nomor Pesanan</th>
                        <th>Tanggal Pesan</th>
                        <th>Metode Pengantaran</th>
                        <th>Total Pembayaran</th>
                        <th>Poin Diperoleh</th>
                        <th>Status Pembayaran</th>
                        <th style="text-align: center;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr style="transition: background var(--t-fast);">
                            <td style="font-weight: 700; color: var(--ink);">{{ $order->order_number }}</td>
                            <td style="color: var(--body);">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge badge-neutral" style="font-weight: 600;">
                                    {{ $order->delivery_type === 'pickup' ? 'Ambil di Gerai' : 'Kirim Ke Rumah' }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: var(--ink);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td style="font-weight: 700; color: var(--warning);">⭐ {{ $order->points_earned }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success">LUNAS</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge badge-warning">PENDING</span>
                                @else
                                    <span class="badge badge-danger">BATAL</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm" style="border-radius: 100px;">Lihat Nota</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
