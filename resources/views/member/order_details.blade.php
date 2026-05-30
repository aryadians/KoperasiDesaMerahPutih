@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
{{-- Printable Header (Visible only when printing) --}}
<div class="print-header">
    <h1>KOPERASI DESA MERAH PUTIH (KDKMP)</h1>
    <p>Struk Belanja & Invoice Pembayaran Gerai Retail</p>
    <p style="font-size: 11px; color: #555;">No. Pesanan: {{ $order->order_number }} &nbsp;·&nbsp; Tanggal: {{ $order->created_at->format('d M Y H:i') }}</p>
</div>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;" class="no-print">
    <a href="{{ route('member.orders') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke riwayat belanja
    </a>
    <button onclick="window.print()" class="btn btn-sm btn-ghost" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
        🖨️ Cetak Struk / PDF
    </button>
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
                    <span style="color: var(--colors-muted);">Pengiriman</span>
                    <strong style="text-transform: uppercase;">{{ $order->delivery_type }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--colors-muted);">Metode Bayar</span>
                    <strong style="color: var(--colors-ink);">
                        @if($order->payment_method === 'cash')
                            💵 TUNAI (BAYAR DI GERAI)
                        @elseif($order->payment_method === 'saldo_sukarela')
                            💳 SALDO SUKARELA (E-WALLET)
                        @elseif($order->payment_method === 'qris_desa')
                            ⚡ QRIS DESA (INSTAN)
                        @else
                            {{ strtoupper($order->payment_method) }}
                        @endif
                    </strong>
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
                <div style="margin-top: 24px; border-top: 1px solid var(--colors-hairline-soft); padding-top: 16px;">
                    @if($order->payment_method === 'qris_desa')
                        {{-- QRIS Simulation Section --}}
                        <div style="text-align: center; background: #fafafa; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); padding: 20px; margin-bottom: 16px;">
                            <div style="font-weight: 700; font-size: 13px; color: #c13515; letter-spacing: 1px; margin-bottom: 12px;">QRIS KDKMP MERAH PUTIH</div>
                            
                            {{-- Mock QR Code in SVG --}}
                            <svg width="150" height="150" viewBox="0 0 100 100" style="background: white; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin: 0 auto 12px; display: block;">
                                <!-- Corners -->
                                <rect x="5" y="5" width="25" height="25" fill="#1a1a1a"/>
                                <rect x="9" y="9" width="17" height="17" fill="white"/>
                                <rect x="13" y="13" width="9" height="9" fill="#1a1a1a"/>

                                <rect x="70" y="5" width="25" height="25" fill="#1a1a1a"/>
                                <rect x="74" y="9" width="17" height="17" fill="white"/>
                                <rect x="78" y="13" width="9" height="9" fill="#1a1a1a"/>

                                <rect x="5" y="70" width="25" height="25" fill="#1a1a1a"/>
                                <rect x="9" y="74" width="17" height="17" fill="white"/>
                                <rect x="13" y="78" width="9" height="9" fill="#1a1a1a"/>

                                <!-- Random QR Blocks -->
                                <rect x="35" y="10" width="10" height="5" fill="#1a1a1a"/>
                                <rect x="50" y="5" width="5" height="15" fill="#1a1a1a"/>
                                <rect x="40" y="25" width="15" height="10" fill="#1a1a1a"/>
                                <rect x="10" y="40" width="20" height="5" fill="#1a1a1a"/>
                                <rect x="5" y="55" width="10" height="10" fill="#1a1a1a"/>
                                <rect x="25" y="50" width="5" height="15" fill="#1a1a1a"/>
                                
                                <rect x="70" y="40" width="10" height="10" fill="#1a1a1a"/>
                                <rect x="85" y="50" width="10" height="5" fill="#1a1a1a"/>
                                <rect x="60" y="70" width="5" height="20" fill="#1a1a1a"/>
                                <rect x="75" y="80" width="15" height="5" fill="#1a1a1a"/>
                                <rect x="35" y="75" width="15" height="15" fill="#1a1a1a"/>
                                <rect x="55" y="50" width="10" height="10" fill="#1a1a1a"/>
                            </svg>
                            <p style="font-size: 11px; color: var(--colors-muted);">Pindai QRIS di atas untuk melakukan simulasi pembayaran instan.</p>
                        </div>

                        <form action="{{ route('orders.pay', $order->id) }}" method="POST" style="margin-bottom: 10px;">
                            @csrf
                            <button type="submit" class="button-primary" style="width: 100%; background: #1a7f5a;">
                                📱 Konfirmasi Bayar QRIS (Sukses)
                            </button>
                        </form>
                    @elseif($order->payment_method === 'cash')
                        <div style="background-color: var(--colors-warning-bg); border: 1px solid var(--colors-warning-border); color: var(--colors-warning); padding: 12px; border-radius: var(--rounded-sm); font-size: 12px; line-height: 1.5; margin-bottom: 16px;">
                            📌 <strong>Pembayaran Tunai:</strong> Silakan datang ke kasir gerai Koperasi Desa Merah Putih untuk membayar tunai dan mengambil barang belanjaan Anda.
                        </div>
                    @endif

                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="button-secondary" style="width: 100%; border-color: var(--colors-primary-error-text); color: var(--colors-primary-error-text); font-weight: 600;">
                            ✕ Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
