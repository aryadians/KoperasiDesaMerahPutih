<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk POS - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            color: #000;
            margin: 10px;
            padding: 0;
            width: 206pt; /* 226pt total width - 20pt margin */
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header h2 {
            font-size: 11px;
            margin: 0 0 2px 0;
            font-weight: 800;
        }
        .header p {
            margin: 0;
            font-size: 7px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 1px 0;
            font-size: 7.5px;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px dashed #000;
            margin-bottom: 6px;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            font-size: 7.5px;
            font-weight: 800;
        }
        .items-table td {
            padding: 3px 0;
            font-size: 7.5px;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
        }
        .totals-table td {
            padding: 1px 0;
            font-size: 7.5px;
        }
        .footer {
            margin-top: 10px;
            font-size: 7px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h2>KDKMP {{ strtoupper($order->branch->code) }}</h2>
        <p>Gerai Sembako Koperasi Desa</p>
        <p>{{ $order->branch->name }}, Indonesia</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 45%;">No Struk:</td>
            <td class="text-right"><strong>{{ $order->order_number }}</strong></td>
        </tr>
        <tr>
            <td>Tanggal:</td>
            <td class="text-right">{{ $order->created_at->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir:</td>
            <td class="text-right">{{ auth()->user()->name }}</td>
        </tr>
        <tr>
            <td>Pelanggan:</td>
            <td class="text-right">{{ $order->user->name }}{{ $member ? ' (' . $member->nomor_anggota . ')' : ' (Umum)' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th align="left" style="width: 55%;">Barang</th>
                <th align="center" style="width: 15%;">Qty</th>
                <th align="right" style="width: 30%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product->name }}<br>
                        <span style="font-size: 6.5px; color: #555;">@ Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                    </td>
                    <td align="center">{{ $item->quantity }}</td>
                    <td align="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Total Belanja:</td>
            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        @if($member)
        <tr>
            <td style="color: #1a7f5a;">Diskon Anggota Koperasi:</td>
            <td class="text-right" style="color: #1a7f5a;">Aktif</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Poin Diperoleh:</td>
            <td class="text-right" style="font-weight: bold;">⭐ +{{ $order->points_earned }} Poin</td>
        </tr>
        @endif
        <tr style="font-weight: bold;">
            <td>TOTAL NETTO:</td>
            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode Bayar:</td>
            <td class="text-right">{{ strtoupper($order->payment_method) }}</td>
        </tr>
    </table>

    <div class="footer text-center">
        <p>Terima Kasih Atas Kunjungan Anda</p>
        <p>Sisa Hasil Usaha (SHU) koperasi desa dibagikan dari warga untuk warga secara transparan.</p>
    </div>
</body>
</html>
