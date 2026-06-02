<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota Penerimaan Hasil Tani</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; padding: 30px; }
    .receipt { max-width: 560px; margin: 0 auto; border: 2px solid #27AE60; border-radius: 8px; overflow: hidden; }
    .receipt-header { background: linear-gradient(135deg, #27AE60, #1a7a44); color: #fff; padding: 18px 24px; text-align: center; }
    .receipt-header h1 { font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .receipt-header p { font-size: 9px; opacity: 0.85; margin-top: 4px; }
    .receipt-body { padding: 20px 24px; }
    .receipt-number { background: #eafaf1; border: 1px dashed #27AE60; padding: 8px 12px; text-align: center; margin-bottom: 16px; border-radius: 4px; }
    .receipt-number span { font-size: 13px; font-weight: 700; color: #27AE60; letter-spacing: 2px; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #f0f0f0; }
    .info-row .label { color: #888; font-size: 10px; }
    .info-row .value { font-weight: 600; font-size: 10.5px; text-align: right; }
    .commodity-box { background: #eafaf1; border: 1px solid #27AE60; padding: 12px 16px; border-radius: 6px; margin: 12px 0; }
    .commodity-box .commodity-name { font-size: 14px; font-weight: 700; color: #27AE60; }
    .commodity-grid { display: flex; gap: 16px; margin-top: 8px; }
    .commodity-cell { flex: 1; text-align: center; background: #fff; border-radius: 4px; padding: 8px; border: 1px solid #d5f5e3; }
    .commodity-cell .c-label { font-size: 8px; color: #aaa; text-transform: uppercase; }
    .commodity-cell .c-value { font-size: 12px; font-weight: 700; color: #1a1a1a; margin-top: 2px; }
    .amount-box { background: #27AE60; color: #fff; padding: 14px 20px; text-align: center; margin: 16px 0; border-radius: 6px; }
    .amount-box .label { font-size: 9px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; }
    .amount-box .amount { font-size: 20px; font-weight: 700; margin-top: 4px; }
    .status-badge { display: inline-block; padding: 3px 12px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #cce5ff; color: #004085; }
    .status-paid { background: #d4edda; color: #155724; }
    .footer { border-top: 1px dashed #ddd; padding-top: 12px; margin-top: 12px; display: flex; justify-content: space-between; align-items: flex-end; }
    .signature { text-align: center; width: 140px; }
    .signature .line { border-bottom: 1px solid #333; height: 40px; margin-bottom: 4px; }
    .note { font-size: 8.5px; color: #aaa; margin-top: 8px; text-align: center; }
</style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <h1>🌾 Koperasi Desa Merah Putih</h1>
        <p>NOTA PENERIMAAN HASIL TANI / AGRO</p>
    </div>
    <div class="receipt-body">
        <div class="receipt-number">
            <div style="font-size:8px;color:#aaa;margin-bottom:2px;">NOMOR REFERENSI</div>
            <span>{{ $referenceNumber }}</span>
        </div>

        <div class="info-row"><span class="label">Tanggal</span><span class="value">{{ $date }}</span></div>
        <div class="info-row"><span class="label">Nama Petani / Anggota</span><span class="value">{{ $memberName }}</span></div>
        <div class="info-row"><span class="label">NIK</span><span class="value">{{ $memberNik }}</span></div>
        <div class="info-row">
            <span class="label">Status</span>
            <span class="value">
                <span class="status-badge status-{{ $status }}">{{ strtoupper($status) }}</span>
            </span>
        </div>

        <div class="commodity-box">
            <div class="commodity-name">🌾 {{ $commodityName }}</div>
            <div class="commodity-grid">
                <div class="commodity-cell">
                    <div class="c-label">Berat Diterima</div>
                    <div class="c-value">{{ $weightKg }} Kg</div>
                </div>
                <div class="commodity-cell">
                    <div class="c-label">Harga / Kg</div>
                    <div class="c-value">Rp {{ $pricePerKg }}</div>
                </div>
                <div class="commodity-cell">
                    <div class="c-label">Kualitas</div>
                    <div class="c-value">{{ $quality ?? 'Standar' }}</div>
                </div>
            </div>
        </div>

        <div class="amount-box">
            <div class="label">TOTAL PEMBAYARAN</div>
            <div class="amount">Rp {{ $totalPayout }}</div>
        </div>

        @if($notes)
        <div class="info-row"><span class="label">Catatan Pengurus</span><span class="value">{{ $notes }}</span></div>
        @endif

        <div class="footer">
            <div>
                <div style="font-size:8px;color:#aaa;">Cabang</div>
                <div style="font-weight:600;">{{ $branchName }}</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:8px;color:#aaa;">Tanda Terima Petani</div>
                <div class="signature" style="width:120px;display:inline-block;">
                    <div class="line"></div>
                    <div style="font-size:9px;font-weight:600;">{{ $memberName }}</div>
                </div>
            </div>
            <div class="signature">
                <div class="line"></div>
                <div style="font-size:9px;font-weight:600;">Pengurus Agro</div>
                <div style="color:#27AE60;font-size:8px;opacity:0.5;">KDKMP Digital</div>
            </div>
        </div>
        <div class="note">⚠️ Nota ini adalah bukti penerimaan hasil tani yang sah. Hanya berlaku dengan cap koperasi dan tanda tangan kedua pihak.</div>
    </div>
</div>
</body>
</html>
