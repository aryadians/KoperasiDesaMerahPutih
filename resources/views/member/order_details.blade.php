@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
{{-- THERMAL POS RECEIPT (Print Only) --}}
<div class="print-header receipt-container">
    <div class="receipt-brand">
        <h2>KDKMP MERAH PUTIH</h2>
        <p>Jl. Desa Merah Putih No.1, Indonesia</p>
        <p>Telp: (021) 555-1234</p>
    </div>
    <div class="receipt-divider">================================</div>
    <div class="receipt-meta">
        <div><span>Tgl</span>: {{ $order->created_at->format('d/m/Y H:i') }}</div>
        <div><span>No</span>: {{ $order->order_number }}</div>
        <div><span>Ksr</span>: SISTEM/WEB</div>
        <div><span>Plg</span>: {{ substr($order->user->name, 0, 15) }}</div>
    </div>
    <div class="receipt-divider">================================</div>
    <table class="receipt-items">
        @foreach($order->items as $item)
            <tr>
                <td colspan="3" class="item-name">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td class="item-qty">{{ $item->quantity }}x</td>
                <td class="item-price">{{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                <td class="item-subtotal">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <div class="receipt-divider">--------------------------------</div>
    <div class="receipt-totals">
        <div class="total-row">
            <span>TOTAL</span>
            <strong>{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
        </div>
        <div class="total-row">
            <span>BAYAR ({{ strtoupper($order->payment_method) }})</span>
            <span>{{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>KEMBALI</span>
            <span>0</span>
        </div>
    </div>
    <div class="receipt-divider">================================</div>
    <div class="receipt-footer">
        @if($order->points_earned > 0)
            <p>⭐ POIN DIDAPAT: {{ $order->points_earned }}</p>
        @endif
        <p>TERIMA KASIH TELAH BERBELANJA</p>
        <p>DI KOPERASI DESA KITA</p>
        <p>Barang yang sudah dibeli tidak</p>
        <p>dapat ditukar/dikembalikan</p>
    </div>
</div>

{{-- WEB UI (Screen Only) --}}
<div class="no-print">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <a href="{{ route('member.orders') }}" style="font-size: 14px; font-weight: 600; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform='translateX(0)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke riwayat
        </a>
        <button onclick="window.print()" class="btn btn-secondary btn-sm" style="border-radius: 100px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk Kasir
        </button>
    </div>

    <div class="split-layout">
        
        <!-- Left: Order Items Details -->
        <div class="main-column reveal-scale">
            <div class="card card-flush" style="box-shadow: var(--shadow-sm);">
                <h2 style="font-size: 18px; font-weight: 700; color: var(--ink); padding: 20px; border-bottom: 1px solid var(--hairline-soft); margin: 0; background: var(--surface-md);">
                    Rincian Barang Belanjaan
                </h2>
                <div class="clean-table-container">
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
                                    <td style="font-weight: 600; color: var(--ink);">{{ $item->product->name }}</td>
                                    <td style="text-align: center;">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 700; color: var(--primary);">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Order Invoice Status / Payments -->
        <div class="sticky-rail reveal-right">
            <div class="card" style="box-shadow: var(--shadow-md); border: 1.5px solid var(--hairline);">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); border-bottom: 1px dashed var(--hairline); padding-bottom: 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                    Invoice Digital
                    <span style="font-size: 24px;">🧾</span>
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: var(--body);">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">No. Pesanan</span>
                        <strong style="color: var(--ink);">{{ $order->order_number }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">Waktu</span>
                        <strong style="color: var(--ink);">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">Metode</span>
                        <span class="badge badge-neutral" style="font-size: 10px;">{{ $order->delivery_type }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">Pembayaran</span>
                        <strong style="color: var(--ink); font-size: 12px; text-transform: uppercase;">
                            @if($order->payment_method === 'cash')
                                💵 TUNAI KASIR
                            @elseif($order->payment_method === 'saldo_sukarela')
                                💳 SALDO KOPERASI
                            @elseif($order->payment_method === 'qris_desa')
                                ⚡ QRIS DESA
                            @else
                                {{ $order->payment_method }}
                            @endif
                        </strong>
                    </div>
                    @if($order->points_earned > 0)
                        <div style="display: flex; justify-content: space-between; background: var(--warning-bg); padding: 8px 12px; border-radius: var(--r-sm); margin: 4px 0;">
                            <span style="color: var(--warning); font-weight: 600; font-size: 12px;">Poin SHU Diperoleh</span>
                            <strong style="color: var(--warning);">⭐ {{ $order->points_earned }} Poin</strong>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1.5px dashed var(--hairline); padding-top: 16px; font-size: 16px; margin-top: 4px;">
                        <span style="font-weight: 600;">Total Belanja</span>
                        <strong style="font-size: 24px; font-weight: 800; color: var(--primary); line-height: 1;">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; background: var(--surface); padding: 12px; border-radius: var(--r-sm);">
                        <span style="color: var(--muted); font-size: 12px; font-weight: 600; text-transform: uppercase;">Status Invoice</span>
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success" style="font-size: 12px;">LUNAS</span>
                        @elseif($order->payment_status === 'pending')
                            <span class="badge badge-warning" style="font-size: 12px;">MENUNGGU PEMBAYARAN</span>
                        @else
                            <span class="badge badge-danger" style="font-size: 12px;">DIBATALKAN</span>
                        @endif
                    </div>
                </div>

                @if($order->payment_status === 'pending')
                    <div style="margin-top: 24px; border-top: 1px solid var(--hairline-soft); padding-top: 20px;">
                        @if($order->payment_method === 'qris_desa')
                            {{-- QRIS Simulation Section --}}
                            <div style="text-align: center; background: white; border: 1.5px solid var(--hairline); border-radius: var(--r-lg); padding: 24px; box-shadow: var(--shadow-sm);">
                                <div style="font-weight: 800; font-size: 15px; color: var(--primary-dark); margin-bottom: 16px;">QRIS KDKMP MERAH PUTIH</div>
                                
                                {{-- Mock QR Code in SVG --}}
                                <svg width="180" height="180" viewBox="0 0 100 100" style="margin: 0 auto 16px; display: block;">
                                    <!-- Corners -->
                                    <rect x="5" y="5" width="25" height="25" fill="#111827"/>
                                    <rect x="9" y="9" width="17" height="17" fill="white"/>
                                    <rect x="13" y="13" width="9" height="9" fill="#111827"/>

                                    <rect x="70" y="5" width="25" height="25" fill="#111827"/>
                                    <rect x="74" y="9" width="17" height="17" fill="white"/>
                                    <rect x="78" y="13" width="9" height="9" fill="#111827"/>

                                    <rect x="5" y="70" width="25" height="25" fill="#111827"/>
                                    <rect x="9" y="74" width="17" height="17" fill="white"/>
                                    <rect x="13" y="78" width="9" height="9" fill="#111827"/>

                                    <!-- Random QR Blocks -->
                                    <rect x="35" y="10" width="10" height="5" fill="#111827"/>
                                    <rect x="50" y="5" width="5" height="15" fill="#111827"/>
                                    <rect x="40" y="25" width="15" height="10" fill="#111827"/>
                                    <rect x="10" y="40" width="20" height="5" fill="#111827"/>
                                    <rect x="5" y="55" width="10" height="10" fill="#111827"/>
                                    <rect x="25" y="50" width="5" height="15" fill="#111827"/>
                                    
                                    <rect x="70" y="40" width="10" height="10" fill="#111827"/>
                                    <rect x="85" y="50" width="10" height="5" fill="#111827"/>
                                    <rect x="60" y="70" width="5" height="20" fill="#111827"/>
                                    <rect x="75" y="80" width="15" height="5" fill="#111827"/>
                                    <rect x="35" y="75" width="15" height="15" fill="#111827"/>
                                    <rect x="55" y="50" width="10" height="10" fill="#111827"/>
                                </svg>
                                
                                <p style="font-size: 12px; color: var(--muted); margin: 0; line-height: 1.5;">Buka aplikasi M-Banking atau E-Wallet Anda, scan kode QR di atas untuk membayar.</p>
                            </div>

                            <form action="{{ route('orders.pay', $order->id) }}" method="POST" style="margin-bottom: 10px;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-full btn-md">
                                    📱 Konfirmasi Bayar QRIS (Sukses)
                                </button>
                            </form>
                        @elseif($order->payment_method === 'cash')
                            <div class="alert alert-info" style="margin: 0 0 16px 0;">
                                <div style="font-size: 24px;">ℹ️</div>
                                <div>
                                    <strong style="display: block; margin-bottom: 4px;">Tunjukkan ke Kasir</strong>
                                    Simpan nomor pesanan ini dan lakukan pembayaran tunai di meja kasir KDKMP Desa Merah Putih.
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-full btn-md" style="color: var(--danger); border-color: var(--danger-border);">
                                ✕ Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection