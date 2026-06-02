<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan Koperasi</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; background: #fff; }
    .header { background: linear-gradient(135deg, #C0392B, #922B21); color: #fff; padding: 20px 24px; margin-bottom: 20px; }
    .header h1 { font-size: 16px; font-weight: 700; letter-spacing: 0.5px; }
    .header p { font-size: 9px; margin-top: 3px; opacity: 0.85; }
    .meta-grid { display: flex; gap: 24px; padding: 0 24px 16px; border-bottom: 2px solid #f0f0f0; margin-bottom: 16px; }
    .meta-item { flex: 1; }
    .meta-item .label { font-size: 8px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .meta-item .value { font-size: 11px; font-weight: 700; color: #C0392B; margin-top: 2px; }
    .section-title { background: #f8f8f8; border-left: 4px solid #C0392B; padding: 8px 12px; font-size: 11px; font-weight: 700; margin: 0 24px 10px; color: #C0392B; }
    table { width: calc(100% - 48px); margin: 0 24px 20px; border-collapse: collapse; }
    thead th { background: #C0392B; color: #fff; padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    tbody tr:nth-child(even) { background: #fdf6f6; }
    tbody tr:hover { background: #fdecea; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 9.5px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 700; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-paid { background: #cce5ff; color: #004085; }
    .badge-draft { background: #fff3cd; color: #856404; }
    .badge-rejected { background: #f8d7da; color: #721c24; }
    .summary-box { margin: 0 24px 16px; background: #fdf6f6; border: 1px solid #f5c6cb; border-radius: 6px; padding: 12px 16px; }
    .summary-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #f0c0c0; }
    .summary-row:last-child { border-bottom: none; font-weight: 700; font-size: 11px; color: #C0392B; }
    .footer { margin-top: 30px; padding: 12px 24px; border-top: 1px solid #eee; display: flex; justify-content: space-between; font-size: 8px; color: #aaa; }
    .signature-area { margin: 30px 24px 0; display: flex; justify-content: flex-end; }
    .signature-box { text-align: center; width: 180px; }
    .signature-box .line { border-bottom: 1px solid #333; margin-bottom: 4px; height: 50px; }
    .signature-box p { font-size: 9px; }
</style>
</head>
<body>

<div class="header">
    <h1>🏛️ KOPERASI DESA MERAH PUTIH</h1>
    <p>{{ $reportTitle ?? 'Laporan Keuangan' }} — Periode: {{ $period ?? date('Y') }}</p>
    <p>Dicetak: {{ now()->format('d/m/Y H:i') }} | Cabang: {{ $branchName ?? 'Pusat' }}</p>
</div>

@if(!empty($summaryItems))
<div class="section-title">📊 RINGKASAN KEUANGAN</div>
<div class="summary-box">
    @foreach($summaryItems as $item)
    <div class="summary-row">
        <span>{{ $item['label'] }}</span>
        <span>{{ $item['value'] }}</span>
    </div>
    @endforeach
</div>
@endif

@if(!empty($tableTitle) && !empty($headers) && !empty($rows))
<div class="section-title">{{ $tableTitle }}</div>
<table>
    <thead>
        <tr>
            @foreach($headers as $h)
            <th>{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        <tr>
            @foreach($row as $cell)
            <td>{{ $cell }}</td>
            @endforeach
        </tr>
        @empty
        <tr><td colspan="{{ count($headers) }}" class="text-center" style="padding:20px;color:#aaa;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
@endif

<div class="signature-area">
    <div class="signature-box">
        <div class="line"></div>
        <p><strong>Pengurus Koperasi</strong></p>
        <p style="color:#aaa;">Tanda Tangan & Cap</p>
    </div>
</div>

<div class="footer">
    <span>KDKMP Digital System — Sistem Informasi Koperasi Desa Merah Putih</span>
    <span>Halaman 1</span>
</div>

</body>
</html>
