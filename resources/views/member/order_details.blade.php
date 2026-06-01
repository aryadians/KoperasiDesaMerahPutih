@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
{{-- THERMAL POS RECEIPT (Print Only) --}}
<div class="print-header receipt-container">
    <div class="receipt-brand">
        <h2>KDKMP {{ strtoupper($order->branch->code) }}</h2>
        <p>{{ $order->branch->address ?? $order->branch->name }}</p>
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
    <style>
        /* 3D Glassmorphism & Styling */
        .card-3d {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: var(--r-lg);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04),
                        0 1px 2px rgba(0, 0, 0, 0.01),
                        inset 0 1px 0 #ffffff !important;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 24px;
        }
        .card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
        }
        
        .btn-3d-primary, .btn-3d-secondary, .btn-3d-success, .btn-3d-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: var(--r-full);
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            outline: none;
            text-decoration: none;
            gap: 8px;
        }
        .btn-3d-primary {
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
            color: #ffffff !important;
            box-shadow: 0 4px 12px var(--primary-glow), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .btn-3d-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--primary-glow), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-3d-secondary {
            background: linear-gradient(180deg, #ffffff, var(--surface-md));
            color: var(--ink) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03), inset 0 1px 0 #ffffff;
        }
        .btn-3d-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06), inset 0 1px 0 #ffffff;
        }
        
        .btn-3d-success {
            background: linear-gradient(180deg, #10b981, #059669);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .btn-3d-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-3d-danger {
            background: linear-gradient(180deg, #ef4444, #dc2626);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .btn-3d-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .btn-full {
            width: 100%;
            display: flex;
        }

        /* Badge styles */
        .badge-3d {
            display: inline-block;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 11px;
            border-radius: var(--r-xs);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.4);
        }
        .badge-3d-success {
            background: var(--success-bg);
            color: var(--success);
            border-color: var(--success-border);
        }
        .badge-3d-warning {
            background: var(--warning-bg);
            color: var(--warning);
            border-color: var(--warning-border);
        }
        .badge-3d-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: var(--danger-border);
        }
        .badge-3d-neutral {
            background: var(--surface-md);
            color: var(--body);
            border-color: var(--hairline);
        }
        
        @keyframes float-emoji {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(4deg); }
        }

        /* QRIS Modal Overlay Styles */
        .qris-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .qris-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .qris-modal-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            width: 90%;
            max-width: 420px;
            border-radius: var(--r-xl);
            padding: 24px;
            transform: scale(0.9) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.25),
                        0 0 0 1px rgba(0, 0, 0, 0.05),
                        inset 0 1px 0 #ffffff !important;
        }
        .qris-modal-overlay.active .qris-modal-card {
            transform: scale(1) translateY(0);
        }
        .qris-modal-header {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1.5px dashed var(--hairline);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .qris-modal-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--ink);
            margin: 0;
        }
        .qris-modal-header p {
            font-size: 12px;
            color: var(--muted);
            margin: 2px 0 0 0;
        }
        .qris-amount-display {
            text-align: center;
            background: var(--surface);
            padding: 12px;
            border-radius: var(--r-md);
            border: 1px solid var(--hairline-soft);
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .amount-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.5px;
        }
        .amount-val {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
        }
        .qris-qr-wrapper {
            position: relative;
            background: #ffffff;
            border: 1.5px solid var(--hairline);
            border-radius: var(--r-lg);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            width: 240px;
            height: 240px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .qris-qr-svg {
            display: block;
            transition: opacity 0.3s ease;
        }
        /* Scanning Laser Line */
        .qris-scanner-line {
            position: absolute;
            left: 20px;
            right: 20px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            box-shadow: 0 0 8px #22c55e;
            animation: scan-line 3s linear infinite;
        }
        @keyframes scan-line {
            0% { top: 20px; }
            50% { top: 220px; }
            100% { top: 20px; }
        }

        /* Success Overlay */
        .qris-success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
            animation: fade-in 0.3s ease forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .success-checkmark-wrapper {
            width: 80px;
            height: 80px;
            margin-bottom: 12px;
        }
        .checkmark-svg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 3;
            stroke: #22c55e;
            stroke-miterlimit: 10;
            box-shadow: inset 0 0 0 #22c55e;
            animation: check-fill .4s ease-in-out .4s forwards, check-scale .3s ease-in-out .9s both;
        }
        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 3;
            stroke-miterlimit: 10;
            stroke: #22c55e;
            fill: none;
            animation: stroke .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke .3s cubic-bezier(0.65, 0, 0.45, 1) .8s forwards;
        }
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        @keyframes check-fill {
            100% { box-shadow: inset 0 0 0 40px rgba(34, 197, 94, 0.1); }
        }
        @keyframes check-scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        .success-title {
            font-size: 18px;
            font-weight: 800;
            color: #15803d;
            animation: text-pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) 0.6s both;
        }
        @keyframes text-pop {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .qris-timer-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: var(--body);
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 8px 16px;
            border-radius: var(--r-sm);
            margin-bottom: 16px;
        }
        .qris-hint {
            font-size: 11.5px;
            color: var(--muted);
            text-align: center;
            line-height: 1.5;
            margin: 0;
        }
        .qris-modal-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
        .confetti-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--color);
            border-radius: 50%;
            opacity: 0;
            animation: confetti-fall 1.5s ease-out forwards;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-50px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(150px) rotate(360deg); opacity: 0; }
        }
    </style>

    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <a href="{{ route('member.orders') }}" class="btn-3d-secondary" style="padding: 8px 16px; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform='translateX(0)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Riwayat
        </a>
        <button onclick="window.print()" class="btn-3d-secondary" style="padding: 8px 16px; font-size: 13.5px; border-radius: 100px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Struk Kasir
        </button>
    </div>

    <div class="split-layout">
        
        <!-- Left: Order Items Details -->
        <div class="main-column reveal-scale">
            <div class="card-3d" style="padding: 0; overflow: hidden;">
                <h2 style="font-size: 18px; font-weight: 800; color: var(--ink); padding: 20px 24px; border-bottom: 1px solid var(--hairline-soft); margin: 0; background: var(--surface); display: flex; align-items: center; gap: 8px;">
                    <span>🛒</span> Rincian Barang Belanjaan
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
                                    <td style="text-align: center; color: var(--body); font-weight: 500;">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                    <td style="text-align: right; color: var(--body);">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
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
            <div class="card-3d">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); border-bottom: 1.5px dashed var(--hairline); padding-bottom: 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                    <span>Invoice Digital</span>
                    <span style="font-size: 24px;">🧾</span>
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: var(--body);">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">No. Pesanan</span>
                        <strong style="color: var(--ink); font-family: monospace;">{{ $order->order_number }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">Waktu</span>
                        <strong style="color: var(--ink); font-weight: 600;">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">Metode</span>
                        @if($order->delivery_type === 'pickup')
                            <span class="badge-3d badge-3d-neutral" style="font-size: 10px;">AMBIL DI GERAI</span>
                        @else
                            <span class="badge-3d badge-3d-neutral" style="font-weight: 700; background: #e0f2fe; color: #0369a1; border: 1px solid rgba(3,105,161,0.12); font-size: 10px;">KIRIM KE RUMAH</span>
                        @endif
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">Pembayaran</span>
                        <strong style="color: var(--ink); font-size: 13px; text-transform: uppercase;">
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
                        <div style="display: flex; justify-content: space-between; background: var(--warning-bg); padding: 10px 14px; border-radius: var(--r-sm); border: 1.5px solid var(--warning-border); margin: 4px 0;">
                            <span style="color: var(--warning); font-weight: 700; font-size: 12.5px;">Poin SHU Diperoleh</span>
                            <strong style="color: var(--warning);">⭐ {{ $order->points_earned }} Poin</strong>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1.5px dashed var(--hairline); padding-top: 16px; font-size: 16px; margin-top: 4px; align-items: baseline;">
                        <span style="font-weight: 700;">Total Belanja</span>
                        <strong style="font-size: 26px; font-weight: 800; color: var(--primary); line-height: 1; letter-spacing: -0.5px;">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; background: var(--surface); padding: 12px 16px; border-radius: var(--r-md); border: 1px solid var(--hairline-soft);">
                        <span style="color: var(--muted); font-size: 12px; font-weight: 700; text-transform: uppercase;">Status Invoice</span>
                        @if($order->payment_status === 'paid')
                            <span class="badge-3d badge-3d-success" style="font-size: 12px;">LUNAS</span>
                        @elseif($order->payment_status === 'pending')
                            <span class="badge-3d badge-3d-warning" style="font-size: 12px;">PENDING</span>
                        @else
                            <span class="badge-3d badge-3d-danger" style="font-size: 12px;">BATAL</span>
                        @endif
                    </div>
                </div>

                @if($order->payment_status === 'pending')
                    <div style="margin-top: 24px; border-top: 1px solid var(--hairline-soft); padding-top: 20px; display: flex; flex-direction: column; gap: 12px;">
                        @if($order->payment_method === 'qris_desa')
                            {{-- QRIS Simulation Section --}}
                            <div style="text-align: center; background: var(--surface); border: 1px solid var(--hairline-soft); border-radius: var(--r-md); padding: 18px; margin-bottom: 4px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="font-size: 32px; display: block; margin-bottom: 8px; animation: float-emoji 3s ease-in-out infinite;">📱</span>
                                <h4 style="font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px;">Pembayaran QRIS Desa</h4>
                                <p style="font-size: 11.5px; color: var(--muted); margin: 0 0 12px; line-height: 1.4;">Bayar instan dan aman langsung dari e-wallet Anda.</p>
                                <button type="button" id="btn-trigger-qris" class="btn-3d-success btn-full btn-md" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                    ⚡ Buka QRIS & Scan
                                </button>
                            </div>

                            <form id="qris-pay-form" action="{{ route('orders.pay', $order->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @elseif($order->payment_method === 'cash')
                            <div class="alert alert-info" style="margin: 0 0 4px 0; display: flex; gap: 12px; align-items: flex-start; text-align: left; background: var(--info-bg); color: var(--info); border: 1px solid var(--info-border); padding: 16px; border-radius: var(--r-md);">
                                <div style="font-size: 24px; line-height: 1;">ℹ️</div>
                                <div>
                                    <strong style="display: block; margin-bottom: 4px; font-weight: 700;">Tunjukkan ke Kasir</strong>
                                    <span style="font-size: 12.5px; line-height: 1.5; color: var(--body);">Simpan nomor pesanan ini dan lakukan pembayaran tunai di meja kasir KDKMP {{ $order->branch->name }}.</span>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-3d-danger btn-full btn-md" style="background: transparent; color: var(--danger) !important; border: 1.5px solid var(--danger-border); box-shadow: none;" onmouseover="this.style.background='var(--danger-bg)'" onmouseout="this.style.background='transparent'">
                                ✕ Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@if($order->payment_status === 'pending' && $order->payment_method === 'qris_desa')
<!-- QRIS Payment Modal Overlay -->
<div id="qris-modal" class="qris-modal-overlay">
    <div class="qris-modal-card">
        <div class="qris-modal-header">
            <span style="font-size: 28px; line-height: 1;">⚡</span>
            <div>
                <h3>QRIS Desa Merah Putih</h3>
                <p>KDKMP {{ strtoupper($order->branch->name) }}</p>
            </div>
        </div>
        
        <div class="qris-modal-body">
            <div class="qris-amount-display">
                <span class="amount-label">TOTAL TAGIHAN</span>
                <span class="amount-val">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            
            <div class="qris-qr-wrapper" id="confetti-container">
                {{-- Mock QR Code in SVG --}}
                <svg width="200" height="200" viewBox="0 0 100 100" class="qris-qr-svg" id="qris-qr-svg">
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
                <div class="qris-scanner-line" id="qris-laser"></div>
                
                <!-- Success Overlay inside the QR wrapper (hidden by default) -->
                <div class="qris-success-overlay" id="qris-success-screen" style="display: none;">
                    <div class="success-checkmark-wrapper">
                        <svg class="checkmark-svg" viewBox="0 0 52 52">
                            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <span class="success-title">Pembayaran Berhasil!</span>
                </div>
            </div>
            
            <div class="qris-timer-wrapper">
                <span class="timer-icon">⏳</span>
                <span>Sisa Waktu Pembayaran: <strong id="qris-countdown">05:00</strong></span>
            </div>
            
            <p class="qris-hint">Scan QR di atas menggunakan aplikasi M-Banking atau E-Wallet Anda (GPN, GoPay, OVO, Dana, ShopeePay).</p>
        </div>
        
        <div class="qris-modal-actions">
            <!-- Simulated Payment trigger -->
            <button id="btn-simulate-pay" type="button" class="btn-3d-success btn-full">
                ⚡ Simulasikan Pembayaran Sukses
            </button>
            <button id="btn-close-qris" type="button" class="btn-3d-secondary btn-full">
                ✕ Tutup QRIS
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('qris-modal');
    const btnTrigger = document.getElementById('btn-trigger-qris');
    const btnClose = document.getElementById('btn-close-qris');
    const btnSimulate = document.getElementById('btn-simulate-pay');
    const qrSvg = document.getElementById('qris-qr-svg');
    const laser = document.getElementById('qris-laser');
    const successScreen = document.getElementById('qris-success-screen');
    const payForm = document.getElementById('qris-pay-form');
    const countdownEl = document.getElementById('qris-countdown');
    const confettiContainer = document.getElementById('confetti-container');
    
    let timerInterval = null;
    let secondsLeft = 300; // 5 minutes

    function startTimer() {
        clearInterval(timerInterval);
        secondsLeft = 300;
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                countdownEl.textContent = "EXPIRED";
                modal.classList.remove('active');
                setTimeout(() => { modal.style.display = 'none'; }, 300);
            } else {
                updateTimerDisplay();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const mins = Math.floor(secondsLeft / 60);
        const secs = secondsLeft % 60;
        countdownEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Play POS Chime via Web Audio API
    function playSuccessChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            
            // Primary high ping tone
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'triangle';
            osc1.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
            osc1.frequency.exponentialRampToValueAtTime(783.99, ctx.currentTime + 0.12); // G5
            osc1.frequency.exponentialRampToValueAtTime(1046.50, ctx.currentTime + 0.24); // C6
            
            gain1.gain.setValueAtTime(0.25, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start();
            osc1.stop(ctx.currentTime + 0.55);

            // High chime accent
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1318.51, ctx.currentTime + 0.12); // E6
            
            gain2.gain.setValueAtTime(0.12, ctx.currentTime + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);
            
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime + 0.12);
            osc2.stop(ctx.currentTime + 0.5);
        } catch (e) {
            console.error("Web Audio API not supported or blocked by user gesture:", e);
        }
    }

    function triggerConfetti() {
        for (let i = 0; i < 45; i++) {
            const p = document.createElement('div');
            p.classList.add('confetti-particle');
            p.style.setProperty('--color', ['#22c55e', '#3b82f6', '#ef4444', '#eab308', '#a855f7'][Math.floor(Math.random() * 5)]);
            p.style.left = Math.random() * 100 + '%';
            p.style.top = Math.random() * 100 + '%';
            p.style.transform = `rotate(${Math.random() * 360}deg)`;
            p.style.animationDelay = Math.random() * 0.2 + 's';
            confettiContainer.appendChild(p);
        }
    }

    btnTrigger.addEventListener('click', function() {
        modal.style.display = 'flex';
        // Force reflow
        modal.offsetHeight;
        modal.classList.add('active');
        startTimer();
    });

    btnClose.addEventListener('click', function() {
        modal.classList.remove('active');
        clearInterval(timerInterval);
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    });

    btnSimulate.addEventListener('click', function() {
        btnSimulate.disabled = true;
        btnClose.disabled = true;
        
        // Hide QR Code and laser line
        qrSvg.style.opacity = '0';
        laser.style.display = 'none';
        
        // Show success screen inside the wrapper
        successScreen.style.display = 'flex';
        
        // Play POS check chime
        playSuccessChime();
        
        // Run confetti effect
        triggerConfetti();
        
        // Delayed form submission to persist paid state in Laravel DB
        setTimeout(() => {
            payForm.submit();
        }, 1600);
    });
});
</script>
@endif

@endsection