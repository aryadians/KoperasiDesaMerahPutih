<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kwitansi Pembayaran Cicilan Pinjaman</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; padding: 30px; }
    .receipt { max-width: 560px; margin: 0 auto; border: 2px solid #C0392B; border-radius: 8px; overflow: hidden; }
    .receipt-header { background: #C0392B; color: #fff; padding: 18px 24px; text-align: center; }
    .receipt-header h1 { font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .receipt-header p { font-size: 9px; opacity: 0.85; margin-top: 4px; }
    .receipt-body { padding: 20px 24px; }
    .receipt-number { background: #fdf6f6; border: 1px dashed #C0392B; padding: 8px 12px; text-align: center; margin-bottom: 16px; border-radius: 4px; }
    .receipt-number span { font-size: 13px; font-weight: 700; color: #C0392B; letter-spacing: 2px; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #f0f0f0; }
    .info-row .label { color: #888; font-size: 10px; }
    .info-row .value { font-weight: 600; font-size: 10.5px; text-align: right; }
    .amount-box { background: #C0392B; color: #fff; padding: 14px 20px; text-align: center; margin: 16px 0; border-radius: 6px; }
    .amount-box .label { font-size: 9px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; }
    .amount-box .amount { font-size: 20px; font-weight: 700; margin-top: 4px; }
    .remaining { background: #fff3cd; border: 1px solid #ffc107; padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
    .remaining .label { font-size: 9px; color: #856404; text-transform: uppercase; }
    .remaining .value { font-size: 13px; font-weight: 700; color: #856404; margin-top: 2px; }
    .badge-paid { background: #d4edda; color: #155724; padding: 2px 10px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .footer { border-top: 1px dashed #ddd; padding-top: 12px; margin-top: 12px; display: flex; justify-content: space-between; align-items: flex-end; }
    .signature { text-align: center; width: 140px; }
    .signature .line { border-bottom: 1px solid #333; height: 40px; margin-bottom: 4px; }
    .watermark { color: #C0392B; font-size: 8px; opacity: 0.5; }
    .note { font-size: 8.5px; color: #aaa; margin-top: 8px; text-align: center; }
</style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <h1>🏛️ Koperasi Desa Merah Putih</h1>
        <p>KWITANSI PEMBAYARAN CICILAN PINJAMAN</p>
    </div>
    <div class="receipt-body">
        <div class="receipt-number">
            <div style="font-size:8px;color:#aaa;margin-bottom:2px;">NOMOR KWITANSI</div>
            <span>{{ $receiptNumber }}</span>
        </div>

        <div class="info-row"><span class="label">Tanggal Bayar</span><span class="value">{{ $paymentDate }}</span></div>
        <div class="info-row"><span class="label">Nama Anggota</span><span class="value">{{ $memberName }}</span></div>
        <div class="info-row"><span class="label">NIK</span><span class="value">{{ $memberNik }}</span></div>
        <div class="info-row"><span class="label">No. Pinjaman</span><span class="value">{{ $loanNumber }}</span></div>
        <div class="info-row"><span class="label">Angsuran Ke-</span><span class="value">{{ $installmentNo }} dari {{ $totalInstallments }}</span></div>
        <div class="info-row"><span class="label">Jenis Pembayaran</span><span class="value"><span class="badge-paid">Cicilan Pinjaman</span></span></div>

        <div class="amount-box">
            <div class="label">JUMLAH DIBAYAR</div>
            <div class="amount">Rp {{ $amountPaid }}</div>
        </div>

        <div class="remaining">
            <div class="label">Sisa Pokok Pinjaman</div>
            <div class="value">Rp {{ $remainingBalance }}</div>
        </div>

        @if($notes)
        <div class="info-row"><span class="label">Catatan</span><span class="value">{{ $notes }}</span></div>
        @endif

        <div class="footer">
            <div>
                <div style="font-size:8px;color:#aaa;">Cabang</div>
                <div style="font-weight:600;">{{ $branchName }}</div>
            </div>
            <div class="signature">
                <div class="line"></div>
                <div style="font-size:9px;font-weight:600;">Kasir / Pengurus</div>
                <div class="watermark">KDKMP Digital</div>
            </div>
        </div>
        <div class="note">⚠️ Simpan kwitansi ini sebagai bukti pembayaran yang sah. Kwitansi hanya berlaku dengan cap koperasi.</div>
    </div>
</div>
</body>
</html>
