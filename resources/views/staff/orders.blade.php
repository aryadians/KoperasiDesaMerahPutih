@extends('layouts.app')

@section('title', 'Kelola Pesanan Gerai - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('staff.dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard staf
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Kelola Semua Pesanan Gerai</h1>

<div class="standard-card" style="padding: 0; overflow: hidden;">
    @if($orders->isEmpty())
        <div style="padding: 32px; text-align: center; color: var(--colors-muted);">
            Belum ada pesanan belanja masuk dari warga.
        </div>
    @else
        <table class="clean-table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Nomor Pesanan</th>
                    <th>Warga / Anggota</th>
                    <th>Tanggal Pesan</th>
                    <th>Pengiriman</th>
                    <th>Total Belanja</th>
                    <th>Status Bayar</th>
                    <th style="text-align: center; width: 300px;">Aksi Kasir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td style="font-weight: 600;">{{ $order->order_number }}</td>
                        <td>
                            <div>{{ $order->user->name }}</div>
                            <span style="font-size: 11px; color: var(--colors-muted);">Role: {{ $order->user->role }}</span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td style="text-transform: uppercase; font-size: 12px; font-weight: 500;">
                            {{ $order->delivery_type }}
                        </td>
                        <td style="font-weight: 600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
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
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('orders.show', $order->id) }}" class="button-secondary" style="height: 32px; font-size: 12px; padding: 0 12px; width: auto; border-color: var(--colors-border-strong);">
                                    Lihat Detail
                                </a>
                                @if($order->payment_status === 'pending')
                                    <form action="{{ route('staff.orders.update', [$order->id, 'paid']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="button-primary" style="height: 32px; font-size: 12px; padding: 0 12px; width: auto; background-color: #1a7f5a;">
                                            Tandai Lunas
                                        </button>
                                    </form>
                                    <form action="{{ route('staff.orders.update', [$order->id, 'cancelled']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="button-primary" style="height: 32px; font-size: 12px; padding: 0 12px; width: auto; background-color: var(--colors-primary-error-text);">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
