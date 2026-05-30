@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('member.orders') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke riwayat belanja
    </a>
</div>

<div class="split-layout">
    
    <!-- Left: Order Items Details -->
    <div class="main-column">
        <div class="standard-card">
            <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px;">
                Rincian Barang Belanjaan
            </h2>
            <table class="clean-table" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: center;">Kuantitas</th>
                        <th style="text-align: right;">Harga Beli</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td style="font-weight: 600;">{{ $item->product->name }}</td>
                            <td style="text-align: center;">{{ $item->quantity }} {{ $item->product->unit }}</td>
                            <td style="text-align: right;">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--colors-ink);">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Order Invoice Status / Payments -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;">
                Invoice Belanja
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--colors-muted);">No. Pesanan</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--colors-muted);">Tanggal</span>
                    <strong>{{ $order->created_at->format('d M Y H:i') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--colors-muted);">Metode</span>
                    <strong style="text-transform: uppercase;">{{ $order->delivery_type }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--colors-muted);">Poin Diperoleh</span>
                    <strong style="color: var(--colors-primary);">⭐ {{ $order->points_earned }} Poin</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--colors-hairline-soft); padding-top: 12px; font-size: 16px;">
                    <span>Total Pembayaran</span>
                    <strong style="font-size: 18px; color: var(--colors-primary);">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                    <span style="color: var(--colors-muted);">Status</span>
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 12px; padding: 4px 10px; border-radius: var(--rounded-full);
                        {{ $order->payment_status === 'paid' ? 'background-color:#e6f6f0; color:#1a7f5a;' : '' }}
                        {{ $order->payment_status === 'pending' ? 'background-color:#fff9e6; color:#b28900;' : '' }}
                        {{ $order->payment_status === 'cancelled' ? 'background-color:#ffebeb; color:#c13515;' : '' }}
                    ">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>

            @if($order->payment_status === 'pending')
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
                    <form action="{{ route('orders.pay', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="button-primary" style="width: 100%;">
                            Simulasi Pembayaran (Sukarela)
                        </button>
                    </form>
                    
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="button-secondary" style="width: 100%; border-color: var(--colors-primary-error-text); color: var(--colors-primary-error-text);">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
