@extends('layouts.app')

@section('title', 'Riwayat Belanja Sembako Saya - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Daftar Pesanan Belanja Saya</h1>

<div class="standard-card" style="padding: 0; overflow: hidden;">
    @if($orders->isEmpty())
        <div style="padding: 48px; text-align: center; color: var(--colors-muted);">
            <p style="font-size: 15px;">Anda belum pernah memesan sembako di gerai online.</p>
            <a href="{{ route('catalog.index') }}" class="button-primary" style="max-width: 200px; margin-top: 16px; display: inline-flex;">Mulai Belanja</a>
        </div>
    @else
        <table class="clean-table" style="margin-top: 0;">
            <thead>
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
                    <tr>
                        <td style="font-weight: 600;">{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <span style="text-transform: uppercase; font-size: 12px; font-weight: 500;">
                                {{ $order->delivery_type === 'pickup' ? 'Ambil di Gerai' : 'Kirim Ke Rumah' }}
                            </span>
                        </td>
                        <td style="font-weight: 600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>⭐ {{ $order->points_earned }} Poin</td>
                        <td>
                            <span style="font-weight: 600; text-transform: uppercase; font-size: 11px;
                                {{ $order->payment_status === 'paid' ? 'color:#1a7f5a;' : '' }}
                                {{ $order->payment_status === 'pending' ? 'color:#b28900;' : '' }}
                                {{ $order->payment_status === 'cancelled' ? 'color:var(--colors-primary-error-text);' : '' }}
                            ">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('orders.show', $order->id) }}" style="color: var(--colors-primary); font-weight: 600; font-size: 13px;">Lihat Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
