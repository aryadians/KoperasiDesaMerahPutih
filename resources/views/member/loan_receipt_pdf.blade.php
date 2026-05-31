<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KDKMP Slip Pinjaman - {{ $loan->loan_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 10pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #e4002b;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .logo-text {
            font-size: 18pt;
            font-weight: bold;
            color: #e4002b;
            margin: 0;
        }
        .subtitle {
            font-size: 9pt;
            color: #6b7280;
            margin: 2px 0 0 0;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            letter-spacing: 0.5px;
        }
        .profile-table {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .profile-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .profile-label {
            width: 25%;
            font-weight: bold;
            color: #4b5563;
        }
        .profile-val {
            width: 75%;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .details-table td, .details-table th {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
        }
        .details-table th {
            background-color: #f9fafb;
            text-align: left;
            font-weight: bold;
            width: 35%;
            color: #4b5563;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .payments-table th {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 8px 10px;
            text-align: left;
        }
        .payments-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 9pt;
        }
        .status-badge {
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-draft { background-color: #f3f4f6; color: #4b5563; }
        .status-approved { background-color: #eff6ff; color: #1d4ed8; }
        .status-active { background-color: #fffbeb; color: #b45309; }
        .status-paid-off { background-color: #ecfdf5; color: #047857; }
        .footer {
            margin-top: 40px;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <span class="logo-text">KDKMP DIGITAL</span>
                    <p class="subtitle">Koperasi Desa Merah Putih — Unit {{ $loan->member->user->branch->name }}</p>
                </td>
                <td style="text-align: right; font-size: 9pt; color: #6b7280; vertical-align: bottom;">
                    {{ $loan->member->user->branch->address ?? 'Garut, Jawa Barat' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">Slip &amp; Rencana Angsuran Pinjaman</div>

    <table class="profile-table">
        <tr>
            <td class="profile-label">Nama Anggota</td>
            <td class="profile-val">: {{ $loan->member->user->name }}</td>
            <td class="profile-label">Kode Pinjaman</td>
            <td class="profile-val">: <strong>{{ $loan->loan_code }}</strong></td>
        </tr>
        <tr>
            <td class="profile-label">No. Anggota</td>
            <td class="profile-val">: {{ $loan->member->nomor_anggota }}</td>
            <td class="profile-label">Status Pinjaman</td>
            <td class="profile-val">: 
                <span class="status-badge status-{{ $loan->status }}">
                    {{ $loan->status === 'paid_off' ? 'Lunas' : $loan->status }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="profile-label">NIK KTP</td>
            <td class="profile-val" colspan="3">: {{ $loan->member->nik }}</td>
        </tr>
    </table>

    @php
        $principal = (float)$loan->amount_approved ?: (float)$loan->amount_requested;
        $interestRate = (float)$loan->interest_rate;
        $tenor = $loan->tenor_months;
        
        $totalInterest = $principal * ($interestRate / 100);
        $totalExpected = $principal + $totalInterest;
        $monthlyInstallment = $totalExpected / $tenor;
        
        $totalPaid = (float)$loan->payments->sum('amount_paid');
        $totalPenalty = (float)$loan->payments->sum('penalty');
        $remaining = max(0.00, $totalExpected - $totalPaid);
    @endphp

    <table class="details-table">
        <tr>
            <th>Nominal Pengajuan</th>
            <td>Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Nominal Disetujui</th>
            <td>Rp {{ number_format($principal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Bunga Flat Koperasi</th>
            <td>{{ number_format($interestRate, 2, ',', '.') }}% Flat</td>
        </tr>
        <tr>
            <th>Tenor Pinjaman</th>
            <td>{{ $tenor }} Bulan</td>
        </tr>
        <tr>
            <th>Total Kewajiban Pelunasan</th>
            <td style="font-weight: bold;">Rp {{ number_format($totalExpected, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Besaran Angsuran Bulanan</th>
            <td style="font-weight: bold; color: #e4002b;">Rp {{ number_format($monthlyInstallment, 0, ',', '.') }} / Bulan</td>
        </tr>
        <tr>
            <th>Total Dana Sudah Dibayar</th>
            <td style="color: #059669; font-weight: bold;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Akumulasi Denda</th>
            <td style="color: #dc2626;">Rp {{ number_format($totalPenalty, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Sisa Saldo Pinjaman (Outstanding)</th>
            <td style="font-weight: bold; background-color: #fffbeb;">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3 style="font-size: 11pt; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; color: #374151;">Catatan Transaksi Cicilan Pembayaran</h3>
    
    <table class="payments-table">
        <thead>
            <tr>
                <th style="width: 15%;">Angsuran Ke-</th>
                <th style="width: 25%;">Tanggal Pembayaran</th>
                <th style="text-align: right; width: 25%;">Nominal Dibayar</th>
                <th style="text-align: right; width: 20%;">Denda Paid</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loan->payments as $payment)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $payment->installment_number }}</td>
                    <td>{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #059669;">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #dc2626;">Rp {{ number_format($payment->penalty, 0, ',', '.') }}</td>
                    <td>Angsuran Ke-{{ $payment->installment_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #9ca3af; font-style: italic; padding: 20px;">
                        Belum ada catatan pembayaran angsuran untuk pinjaman ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis melalui Platform KDKMP Digital pada {{ date('d F Y H:i:s') }}.</p>
        <p>Dana pinjaman koperasi ditujukan untuk pemberdayaan ekonomi mikro dan UMKM produktif desa.</p>
    </div>

</body>
</html>
