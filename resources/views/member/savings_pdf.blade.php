<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mutasi Simpanan KDKMP - {{ $member->user->name }}</title>
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
        .balance-box-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .balance-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .balance-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .balance-amount {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .ledger-table th {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 8px 10px;
            text-align: left;
        }
        .ledger-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 9pt;
        }
        .amount-credit {
            color: #059669;
            font-weight: bold;
        }
        .amount-debit {
            color: #dc2626;
            font-weight: bold;
        }
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
                    <p class="subtitle">Koperasi Desa Merah Putih — Unit {{ $member->user->branch->name }}</p>
                </td>
                <td style="text-align: right; font-size: 9pt; color: #6b7280; vertical-align: bottom;">
                    {{ $member->user->branch->address ?? 'Garut, Jawa Barat' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">Laporan Mutasi Simpanan Anggota</div>

    <table class="profile-table">
        <tr>
            <td class="profile-label">Nama Anggota</td>
            <td class="profile-val">: {{ $member->user->name }}</td>
            <td class="profile-label">No. Anggota</td>
            <td class="profile-val">: {{ $member->nomor_anggota }}</td>
        </tr>
        <tr>
            <td class="profile-label">NIK KTP</td>
            <td class="profile-val">: {{ $member->nik }}</td>
            <td class="profile-label">Tgl. Bergabung</td>
            <td class="profile-val">: {{ $member->tanggal_bergabung->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="profile-label">Alamat Desa</td>
            <td class="profile-val" colspan="3">: {{ $member->alamat_desa }}</td>
        </tr>
    </table>

    <table class="balance-box-table" style="margin-left: -12px; margin-right: -12px;">
        <tr>
            <td style="width: 25%;">
                <div class="balance-box">
                    <div class="balance-label">Simpanan Pokok</div>
                    <div class="balance-amount">Rp {{ number_format($balances['pokok'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="balance-box">
                    <div class="balance-label">Simpanan Wajib</div>
                    <div class="balance-amount">Rp {{ number_format($balances['wajib'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="balance-box">
                    <div class="balance-label">Simpanan Sukarela</div>
                    <div class="balance-amount" style="color: #005baa;">Rp {{ number_format($balances['sukarela'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="balance-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="balance-label" style="color: #15803d;">Total Saldo</div>
                    <div class="balance-amount" style="color: #15803d;">Rp {{ number_format($balances['total'], 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 11pt; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; color: #374151;">Buku Rekening Mutasi (Ledger)</h3>
    
    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Jenis Simpanan</th>
                <th style="text-align: right; width: 20%;">Nominal Mutasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($savings as $saving)
                <tr>
                    <td>{{ $saving->transaction_date->format('Y-m-d') }}</td>
                    <td style="text-transform: capitalize;">{{ $saving->type }}</td>
                    <td style="text-align: right;" class="{{ $saving->amount >= 0 ? 'amount-credit' : 'amount-debit' }}">
                        {{ $saving->amount >= 0 ? '+' : '' }}Rp {{ number_format($saving->amount, 0, ',', '.') }}
                    </td>
                    <td>{{ $saving->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #9ca3af; font-style: italic; padding: 20px;">
                        Belum ada riwayat transaksi mutasi simpanan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis melalui Platform KDKMP Digital pada {{ date('d F Y H:i:s') }}.</p>
        <p>Koperasi Desa Merah Putih berizin resmi dan diawasi oleh Dinas Koperasi dan UMKM Kabupaten Garut.</p>
    </div>

</body>
</html>
