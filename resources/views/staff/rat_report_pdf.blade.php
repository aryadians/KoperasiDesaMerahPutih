<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan RAT Pertanggungjawaban Cabang KDKMP</title>
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
            border-bottom: 3px double #e4002b;
            padding-bottom: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        .logo-text {
            font-size: 20pt;
            font-weight: bold;
            color: #e4002b;
            margin: 0;
            letter-spacing: 1px;
        }
        .subtitle {
            font-size: 10pt;
            color: #4b5563;
            margin: 4px 0 0 0;
            font-weight: bold;
        }
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0 10px 0;
            letter-spacing: 0.5px;
            color: #111827;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 30px;
            color: #4b5563;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 5px 0;
            font-size: 10pt;
        }
        .meta-label {
            font-weight: bold;
            width: 20%;
            color: #4b5563;
        }
        .meta-val {
            width: 30%;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #e4002b;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .metrics-table th, .metrics-table td {
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            font-size: 9.5pt;
        }
        .metrics-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        .metrics-table td.amount {
            text-align: right;
            font-weight: bold;
            font-family: monospace;
            font-size: 10pt;
        }
        .sig-container {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        .sig-box {
            width: 50%;
            text-align: center;
            font-size: 10pt;
        }
        .sig-space {
            height: 70px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
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
        <span class="logo-text">KOPERASI DESA MERAH PUTIH (KDKMP)</span>
        <p class="subtitle">KDKMP Unit {{ $branch->name }} (Cabang Kode: {{ strtoupper($branch->code) }})</p>
        <p style="font-size: 8pt; color: #6b7280; margin: 4px 0 0 0;">{{ $branch->address ?? 'Kecamatan Karangpawitan, Kabupaten Garut, Jawa Barat' }}</p>
    </div>

    <div class="doc-title">Laporan Kinerja Pertanggungjawaban Cabang</div>
    <div class="doc-subtitle">Bahan Sidang Rapat Anggota Tahunan (RAT) Buku Tahun 2026</div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Desa Cabang</td>
            <td class="meta-val">: {{ $branch->name }}</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-val">: {{ date('d F Y H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kepala Cabang</td>
            <td class="meta-val">: {{ $staffName }}</td>
            <td class="meta-label">Status Unit</td>
            <td class="meta-val">: Aktif &amp; Terverifikasi 📍</td>
        </tr>
    </table>

    <div class="section-title">I. Ringkasan Kinerja Finansial &amp; Ritel</div>
    
    <table class="metrics-table">
        <thead>
            <tr>
                <th style="width: 55%; text-align: left;">Indikator Kinerja Utama (IKU) Koperasi</th>
                <th style="width: 45%; text-align: right;">Total Nilai Buku Volume (Rupiah)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Omset Penjualan Gerai Ritel Sembako</strong><br><span style="font-size: 8pt; color: #6b7280;">Total transaksi penjualan sembako lunas di kasir POS offline dan e-Commerce warga.</span></td>
                <td class="amount">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>2. Dana Penyerapan Komoditas Pertanian Lokal</strong><br><span style="font-size: 8pt; color: #6b7280;">Dana tunai yang disalurkan langsung untuk menyerap hasil panen tani warga desa.</span></td>
                <td class="amount" style="color: #005baa;">Rp {{ number_format($totalCrops, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>3. Outstanding Penyaluran Kredit Pinjaman Mikro</strong><br><span style="font-size: 8pt; color: #6b7280;">Modal bergulir produktif yang dipinjam oleh pelaku UMKM dan warga aktif.</span></td>
                <td class="amount">Rp {{ number_format($totalLoans, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>4. Total Akumulasi Dana Simpanan Anggota</strong><br><span style="font-size: 8pt; color: #6b7280;">Total simpanan warga (pokok, wajib, sukarela) yang terhimpun di kas cabang.</span></td>
                <td class="amount" style="color: #059669;">Rp {{ number_format($totalSavings, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">II. Statistik Keanggotaan &amp; Layanan Desa</div>
    
    <table class="metrics-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 60%;">Metrik Layanan Sosial Koperasi</th>
                <th style="text-align: center; width: 40%;">Jumlah Kapasitas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Warga Terdaftar Menjadi Anggota Aktif</td>
                <td style="text-align: center; font-weight: bold;">{{ $activeMembersCount }} Anggota</td>
            </tr>
            <tr>
                <td>Jumlah Produk &amp; Komoditas Tersedia di Gerai</td>
                <td style="text-align: center; font-weight: bold;">{{ $activeProductsCount }} Item Produk</td>
            </tr>
            <tr>
                <td>Estimasi Omset Penjualan Hari Ini</td>
                <td style="text-align: center; font-weight: bold; color: #059669;">Rp {{ number_format($salesToday, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">III. Pengesahan Laporan Kinerja RAT</div>
    <p style="font-size: 9pt; line-height: 1.5; color: #4b5563;">
        Demikian laporan pertanggungjawaban data keuangan dan operasional KDKMP Unit {{ $branch->name }} ini dibuat dengan sebenar-benarnya untuk digunakan sebagai bahan pertimbangan pertanggungjawaban dalam Rapat Anggota Tahunan (RAT) tahun buku berjalan.
    </p>

    <table class="sig-container">
        <tr>
            <td class="sig-box">
                <p>Dilaporkan Oleh,</p>
                <p style="font-weight: bold;">Kepala KDKMP Unit {{ $branch->name }}</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $staffName }}</p>
                <p style="font-size: 8pt; color: #6b7280;">NIP: KDKMP-{{ strtoupper($branch->code) }}-{{ date('Ymd') }}</p>
            </td>
            <td class="sig-box">
                <p>Mengetahui,</p>
                <p style="font-weight: bold;">Ketua Umum KDKMP Pusat</p>
                <div class="sig-space"></div>
                <p class="sig-name">Ir. H. Haryadi Aryadiansyah</p>
                <p style="font-size: 8pt; color: #6b7280;">NIP: KDKMP-HQ-001</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Laporan Pertanggungjawaban RAT KDKMP Digital Platform · Halaman 1 dari 1</p>
        <p>Dicetak pada {{ date('d/m/Y H:i') }} · Platform Digital Koperasi Desa Merah Putih</p>
    </div>

</body>
</html>
