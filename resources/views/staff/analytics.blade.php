@extends('layouts.admin')

@section('title', 'Analitik Finansial & Ritel — KDKMP Digital')

@section('content')

@php
// Helper functions for dynamic SVG charts scaling
if (!function_exists('getSvgPath')) {
    function getSvgPath($data, $minVal, $maxVal, $width = 500, $height = 200, $padLeft = 50, $padRight = 50, $padTop = 20, $padBottom = 30) {
        $count = count($data);
        if ($count < 2) return '';
        
        $availWidth = $width - $padLeft - $padRight;
        $availHeight = $height - $padTop - $padBottom;
        $diff = $maxVal - $minVal ?: 1;
        
        $points = [];
        foreach ($data as $i => $val) {
            $x = $padLeft + ($i / ($count - 1)) * $availWidth;
            $y = $height - $padBottom - (($val - $minVal) / $diff) * $availHeight;
            $points[] = "$x,$y";
        }
        
        return "M " . implode(" L ", $points);
    }
}

if (!function_exists('getSvgPoints')) {
    function getSvgPoints($data, $minVal, $maxVal, $width = 500, $height = 200, $padLeft = 50, $padRight = 50, $padTop = 20, $padBottom = 30) {
        $count = count($data);
        $availWidth = $width - $padLeft - $padRight;
        $availHeight = $height - $padTop - $padBottom;
        $diff = $maxVal - $minVal ?: 1;
        
        $points = [];
        foreach ($data as $i => $val) {
            $x = $padLeft + ($i / ($count - 1)) * $availWidth;
            $y = $height - $padBottom - (($val - $minVal) / $diff) * $availHeight;
            $points[] = ['x' => $x, 'y' => $y, 'val' => $val];
        }
        return $points;
    }
}
@endphp

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px; color: var(--colors-ink); margin-bottom: 6px;">
            📊 Analitik Finansial &amp; Ritel
        </h1>
        <p style="color: var(--colors-muted); font-size: 15px;">
            Statistik real-time, volume perputaran kas, penyerapan pertanian desa, dan outstanding kredit mikro.
        </p>
    </div>
    <button onclick="window.print()" class="btn btn-md btn-secondary no-print" style="border-radius: 100px; display: inline-flex; align-items: center; gap: 8px; width: auto; font-size: 14px;">
        🖨️ Cetak Analitik / PDF
    </button>
</div>

{{-- ═══════════════════════ PRINT HEADER ═══════════════════════ --}}
<div class="print-header">
    <h1>KOPERASI DESA MERAH PUTIH (KDKMP)</h1>
    <p>Laporan Analitik Finansial, Kasir Ritel, dan Agro Tani Desa</p>
    <p style="font-size: 11px; color: #555;">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} &nbsp;·&nbsp; Oleh: {{ auth()->user()->name }}</p>
</div>

{{-- ═══════════════════════ OVERVIEW CARDS ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 36px;" class="grid-4">
    
    <div class="stat-card reveal delay-1">
        <span class="stat-label">Total Omset Ritel</span>
        <div class="stat-value" style="color: var(--primary);" data-counter data-target="{{ $totalSales }}" data-prefix="Rp ">
            Rp {{ number_format($totalSales, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Transaksi lunas gerai sembako</p>
        <span class="stat-icon">🛍️</span>
    </div>

    <div class="stat-card reveal delay-2">
        <span class="stat-label">Total Penyerapan Agro</span>
        <div class="stat-value" style="color: var(--info);" data-counter data-target="{{ $totalCrops }}" data-prefix="Rp ">
            Rp {{ number_format($totalCrops, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Dana pembelian panen lokal tani</p>
        <span class="stat-icon">🌾</span>
    </div>

    <div class="stat-card reveal delay-3">
        <span class="stat-label">Kredit Terdistribusi</span>
        <div class="stat-value" style="color: #6c3de0;" data-counter data-target="{{ $totalLoans }}" data-prefix="Rp ">
            Rp {{ number_format($totalLoans, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Outstanding modal kredit mikro warga</p>
        <span class="stat-icon">🏦</span>
    </div>

    <div class="stat-card reveal delay-4">
        <span class="stat-label">Volume Simpanan</span>
        <div class="stat-value" style="color: var(--success);" data-counter data-target="{{ $totalSavings }}" data-prefix="Rp ">
            Rp {{ number_format($totalSavings, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Total simpanan pokok, wajib, sukarela</p>
        <span class="stat-icon">🪙</span>
    </div>

</div>

{{-- ═══════════════════════ SVG CHARTS GRID ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 36px;" class="split-layout">
    
    {{-- Chart 1: Ritel & Agro --}}
    <div class="standard-card">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Omset Ritel vs Penyerapan Tani (Rupiah)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--colors-muted);">Tren 5 Bulan Terakhir</span>
        </h3>
        
        @php
            $maxVal1 = max(max($salesTrend), max($cropTrend), 100000);
            $minVal1 = 0;
            $pointsSales = getSvgPoints($salesTrend, $minVal1, $maxVal1);
            $pointsCrops = getSvgPoints($cropTrend, $minVal1, $maxVal1);
        @endphp

        <div style="background: #fdfdfd; padding: 12px; border-radius: 8px; border: 1px solid var(--hairline-soft);">
            <svg viewBox="0 0 500 220" width="100%" height="auto" style="overflow: visible;">
                <defs>
                    <linearGradient id="salesGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.12"/>
                        <stop offset="100%" stop-color="var(--primary)" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>

                <!-- Grid Lines -->
                @for($j = 0; $j <= 4; $j++)
                    @php $yLine = 20 + $j * 42; @endphp
                    <line x1="50" y1="{{ $yLine }}" x2="450" y2="{{ $yLine }}" stroke="#eee" stroke-width="1" />
                    <!-- Y Axis Labels -->
                    @php $yVal = $maxVal1 - ($j * ($maxVal1 / 4)); @endphp
                    <text x="44" y="{{ $yLine + 4 }}" font-size="8" fill="#888" text-anchor="end">Rp {{ number_format($yVal/1000000, 1) }}M</text>
                @endfor

                <!-- X Axis Labels -->
                @foreach($labels as $idx => $lbl)
                    @php $xLbl = 50 + ($idx / 4) * 400; @endphp
                    <text x="{{ $xLbl }}" y="210" font-size="9" fill="#555" font-weight="600" text-anchor="middle">{{ $lbl }}</text>
                    <line x1="{{ $xLbl }}" y1="20" x2="{{ $xLbl }}" y2="188" stroke="#f4f4f4" stroke-width="1" />
                @endforeach

                <!-- Sales Line (Red) -->
                <path d="{{ getSvgPath($salesTrend, $minVal1, $maxVal1) }}" fill="none" stroke="var(--primary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                
                <!-- Crops Line (Blue) -->
                <path d="{{ getSvgPath($cropTrend, $minVal1, $maxVal1) }}" fill="none" stroke="var(--info)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                <!-- Points Circle Sales -->
                @foreach($pointsSales as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--primary)" stroke="white" stroke-width="2" style="cursor: pointer;" title="Sales: Rp {{ number_format($pt['val']) }}"/>
                @endforeach

                <!-- Points Circle Crops -->
                @foreach($pointsCrops as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--info)" stroke="white" stroke-width="2" style="cursor: pointer;" title="Panen: Rp {{ number_format($pt['val']) }}"/>
                @endforeach
            </svg>
        </div>

        <div style="display: flex; gap: 16px; margin-top: 12px; font-size: 12px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--primary); border-radius: 2px;"></span>
                <span style="font-weight: 600;">Omset Belanja Ritel</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--info); border-radius: 2px;"></span>
                <span style="font-weight: 600;">Penyaluran Modal Hasil Tani</span>
            </div>
        </div>
    </div>

    {{-- Chart 2: Kredit & Simpanan --}}
    <div class="standard-card">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Outstanding Kredit vs Akumulasi Simpanan (Rupiah)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--colors-muted);">Tren 5 Bulan Terakhir</span>
        </h3>
        
        @php
            $maxVal2 = max(max($loanTrend), max($savingsTrend), 100000);
            $minVal2 = 0;
            $pointsLoans = getSvgPoints($loanTrend, $minVal2, $maxVal2);
            $pointsSavings = getSvgPoints($savingsTrend, $minVal2, $maxVal2);
        @endphp

        <div style="background: #fdfdfd; padding: 12px; border-radius: 8px; border: 1px solid var(--hairline-soft);">
            <svg viewBox="0 0 500 220" width="100%" height="auto" style="overflow: visible;">
                <!-- Grid Lines -->
                @for($j = 0; $j <= 4; $j++)
                    @php $yLine = 20 + $j * 42; @endphp
                    <line x1="50" y1="{{ $yLine }}" x2="450" y2="{{ $yLine }}" stroke="#eee" stroke-width="1" />
                    <!-- Y Axis Labels -->
                    @php $yVal = $maxVal2 - ($j * ($maxVal2 / 4)); @endphp
                    <text x="44" y="{{ $yLine + 4 }}" font-size="8" fill="#888" text-anchor="end">Rp {{ number_format($yVal/1000000, 1) }}M</text>
                @endfor

                <!-- X Axis Labels -->
                @foreach($labels as $idx => $lbl)
                    @php $xLbl = 50 + ($idx / 4) * 400; @endphp
                    <text x="{{ $xLbl }}" y="210" font-size="9" fill="#555" font-weight="600" text-anchor="middle">{{ $lbl }}</text>
                    <line x1="{{ $xLbl }}" y1="20" x2="{{ $xLbl }}" y2="188" stroke="#f4f4f4" stroke-width="1" />
                @endforeach

                <!-- Loans Line (Purple) -->
                <path d="{{ getSvgPath($loanTrend, $minVal2, $maxVal2) }}" fill="none" stroke="#6c3de0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                
                <!-- Savings Line (Green) -->
                <path d="{{ getSvgPath($savingsTrend, $minVal2, $maxVal2) }}" fill="none" stroke="var(--success)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                <!-- Points Circle Loans -->
                @foreach($pointsLoans as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#6c3de0" stroke="white" stroke-width="2" style="cursor: pointer;" title="Loans: Rp {{ number_format($pt['val']) }}"/>
                @endforeach

                <!-- Points Circle Savings -->
                @foreach($pointsSavings as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--success)" stroke="white" stroke-width="2" style="cursor: pointer;" title="Savings: Rp {{ number_format($pt['val']) }}"/>
                @endforeach
            </svg>
        </div>

        <div style="display: flex; gap: 16px; margin-top: 12px; font-size: 12px; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #6c3de0; border-radius: 2px;"></span>
                <span style="font-weight: 600;">Outstanding Kredit Usaha</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--success); border-radius: 2px;"></span>
                <span style="font-weight: 600;">Total Tabungan Warga</span>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════ DETAILED REPORT TABLE ═══════════════════════ --}}
<div class="standard-card">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">Tabel Rincian Saldo Bulanan</h3>
    <table class="clean-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th>Bulan (2026)</th>
                <th>Omset Ritel Gerai</th>
                <th>Penyerapan Hasil Tani</th>
                <th>Realisasi Kredit Mikro</th>
                <th>Dana Simpanan Terkumpul</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labels as $i => $lbl)
                <tr>
                    <td style="font-weight: 700;">{{ $lbl }} 2026</td>
                    <td>Rp {{ number_format($salesTrend[$i], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($cropTrend[$i], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($loanTrend[$i], 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($savingsTrend[$i], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
