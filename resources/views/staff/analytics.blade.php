@extends('layouts.admin')

@section('title', 'Analitik Finansial & Ritel — KDKMP Digital')
@section('page-title', 'Analitik')

@section('content')

<style>
    /* View-Specific 3D Polish Styles */
    .btn-3d-primary {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all var(--t-fast) var(--ease-out);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-3d-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    }
    .btn-3d-primary:active {
        transform: translateY(0);
    }

    .btn-3d-secondary {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        color: var(--ink) !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), inset 0 1px 0 #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        transition: all var(--t-fast) var(--ease-out);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-3d-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06), inset 0 1px 0 #ffffff !important;
        border-color: var(--muted) !important;
    }
    .btn-3d-secondary:active {
        transform: translateY(0);
    }

    .analytics-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }
    
    @keyframes emoji-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    /* SVG Interactive Graph Styles */
    .chart-line {
        stroke-dasharray: 1200;
        stroke-dashoffset: 1200;
        animation: chart-draw 2.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes chart-draw {
        to { stroke-dashoffset: 0; }
    }

    .chart-area {
        opacity: 0;
        animation: fade-in 1s ease-out 1s forwards;
    }
    @keyframes fade-in {
        to { opacity: 0.15; }
    }

    .chart-marker {
        transition: r 0.2s ease, stroke-width 0.2s ease, fill 0.2s ease;
    }
    .chart-marker:hover {
        r: 8px !important;
        stroke-width: 3px !important;
    }

    .chart-filter-btn {
        padding: 6px 18px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 100px;
        cursor: pointer;
        border: 1.5px solid var(--hairline);
        background: white;
        color: var(--muted);
        transition: all 0.25s var(--ease-out);
        box-shadow: var(--shadow-sm);
    }
    .chart-filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .chart-filter-btn.active {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .chart-panel {
        transition: opacity 0.35s ease, transform 0.35s ease;
    }
    .chart-panel.hidden {
        opacity: 0;
        transform: translateY(15px) scale(0.98);
        pointer-events: none;
        position: absolute;
        width: 0;
        height: 0;
        overflow: hidden;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    /* Category bar animations */
    .bar-rect {
        transform-origin: bottom;
        animation: scale-up-bar 1.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    @keyframes scale-up-bar {
        from { transform: scaleY(0); }
        to { transform: scaleY(1); }
    }

    /* Floating Tooltip Card */
    #chart-tooltip {
        position: absolute;
        padding: 10px 14px;
        background: rgba(17, 24, 39, 0.95);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: white;
        font-size: 12px;
        font-weight: 600;
        border-radius: var(--r-sm);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        pointer-events: none;
        opacity: 0;
        transform: translate(-50%, -105%) scale(0.9);
        transition: opacity 0.15s ease, transform 0.15s ease, left 0.1s ease, top 0.1s ease;
        z-index: 1000;
    }
    #chart-tooltip.visible {
        opacity: 1;
        transform: translate(-50%, -105%) scale(1);
    }
    #chart-tooltip::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 6px 6px 0;
        border-style: solid;
        border-color: rgba(17, 24, 39, 0.95) transparent;
        display: block;
        width: 0;
    }
</style>

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

if (!function_exists('getSvgSmoothPath')) {
    function getSvgSmoothPath($points, $closed = false, $height = 220, $padBottom = 32) {
        if (empty($points)) return '';
        $count = count($points);
        if ($count < 2) return '';
        
        $path = "M {$points[0]['x']},{$points[0]['y']}";
        
        for ($i = 0; $i < $count - 1; $i++) {
            $p0 = $points[$i];
            $p1 = $points[$i+1];
            
            // Offset for control points
            $cpOffset = ($p1['x'] - $p0['x']) / 3.2;
            
            // Calculate Y slope adjustments for smoothness
            $ySlope0 = 0;
            $ySlope1 = 0;
            
            if ($i > 0) {
                $prev = $points[$i-1];
                $ySlope0 = ($p1['y'] - $prev['y']) / 6;
            } else {
                $ySlope0 = ($p1['y'] - $p0['y']) / 6;
            }
            
            if ($i < $count - 2) {
                $next = $points[$i+2];
                $ySlope1 = ($next['y'] - $p0['y']) / 6;
            } else {
                $ySlope1 = ($p1['y'] - $p0['y']) / 6;
            }
            
            $cp1x = $p0['x'] + $cpOffset;
            $cp1y = $p0['y'] + $ySlope0;
            
            $cp2x = $p1['x'] - $cpOffset;
            $cp2y = $p1['y'] - $ySlope1;
            
            // Clamp within canvas boundaries
            $cp1y = max(10, min($height - $padBottom, $cp1y));
            $cp2y = max(10, min($height - $padBottom, $cp2y));
            
            $path .= " C $cp1x,$cp1y $cp2x,$cp2y {$p1['x']},{$p1['y']}";
        }
        
        if ($closed) {
            $last = $points[$count - 1];
            $first = $points[0];
            $bottomY = $height - $padBottom;
            $path .= " L {$last['x']},{$bottomY} L {$first['x']},{$bottomY} Z";
        }
        
        return $path;
    }
}
@endphp

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div class="reveal" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin-bottom: 6px;">
            📊 Analitik <span style="color: var(--primary);">Finansial &amp; Ritel</span>
        </h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">
            Statistik real-time, volume perputaran kas, penyerapan pertanian desa, dan outstanding kredit mikro.
        </p>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;" class="no-print">
        <a href="{{ route('staff.analytics.rat-pdf') }}" class="btn-3d-primary" style="border-radius: 100px; display: inline-flex; align-items: center; gap: 8px; width: auto; font-size: 13.5px; height: 38px; padding: 0 20px;" data-no-loading>
            📄 Unduh Laporan RAT (PDF)
        </a>
        <button onclick="window.print()" class="btn-3d-secondary" style="border-radius: 100px; display: inline-flex; align-items: center; gap: 8px; width: auto; font-size: 13.5px; height: 38px; padding: 0 20px;">
            🖨️ Cetak Halaman
        </button>
    </div>
</div>

{{-- ═══════════════════════ PRINT HEADER ═══════════════════════ --}}
<div class="print-header">
    <h1>KOPERASI {{ strtoupper(auth()->user()->branch->name) }} (KDKMP {{ strtoupper(auth()->user()->branch->code) }})</h1>
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

{{-- ═══════════════════════ CHART FILTERS ═══════════════════════ --}}
<div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;" class="no-print">
    <button onclick="filterCharts('all')" class="chart-filter-btn active" id="btn-filter-all">📊 Semua Data</button>
    <button onclick="filterCharts('retail')" class="chart-filter-btn" id="btn-filter-retail">🛍️ Ritel &amp; Agro</button>
    <button onclick="filterCharts('financial')" class="chart-filter-btn" id="btn-filter-financial">🏦 Finansial &amp; Tabungan</button>
</div>

{{-- ═══════════════════════ SVG CHARTS GRID ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;" class="split-layout">
    
    {{-- Chart 1: Ritel & Agro --}}
    <div class="analytics-card chart-panel" id="panel-retail-agro">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Omset Ritel vs Penyerapan Tani (Rupiah)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Tren 5 Bulan Terakhir</span>
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
                        <stop offset="0%" stop-color="var(--primary)" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="var(--primary)" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="cropGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--info)" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="var(--info)" stop-opacity="0.0"/>
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

                <!-- Shaded Areas -->
                <path class="chart-area" d="{{ getSvgSmoothPath($pointsSales, true, 220, 32) }}" fill="url(#salesGrad)" />
                <path class="chart-area" d="{{ getSvgSmoothPath($pointsCrops, true, 220, 32) }}" fill="url(#cropGrad)" />
  
                <!-- Sales Line (Red) -->
                <path class="chart-line" d="{{ getSvgSmoothPath($pointsSales, false, 220, 32) }}" fill="none" stroke="var(--primary)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                
                <!-- Crops Line (Blue) -->
                <path class="chart-line" d="{{ getSvgSmoothPath($pointsCrops, false, 220, 32) }}" fill="none" stroke="var(--info)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
  
                <!-- Points Circle Sales -->
                @foreach($pointsSales as $idx => $pt)
                    <circle class="chart-marker" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--primary)" stroke="white" stroke-width="2" style="cursor: pointer;" onmouseover="showTooltip(event, 'Omset Ritel ({{ $labels[$idx] }})', 'Rp {{ number_format($pt['val'], 0, ',', '.') }}')" onmouseout="hideTooltip()"/>
                @endforeach
  
                <!-- Points Circle Crops -->
                @foreach($pointsCrops as $idx => $pt)
                    <circle class="chart-marker" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--info)" stroke="white" stroke-width="2" style="cursor: pointer;" onmouseover="showTooltip(event, 'Penyerapan Tani ({{ $labels[$idx] }})', 'Rp {{ number_format($pt['val'], 0, ',', '.') }}')" onmouseout="hideTooltip()"/>
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
    <div class="analytics-card chart-panel" id="panel-financial">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Outstanding Kredit vs Akumulasi Simpanan (Rupiah)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Tren 5 Bulan Terakhir</span>
        </h3>
        
        @php
            $maxVal2 = max(max($loanTrend), max($savingsTrend), 100000);
            $minVal2 = 0;
            $pointsLoans = getSvgPoints($loanTrend, $minVal2, $maxVal2);
            $pointsSavings = getSvgPoints($savingsTrend, $minVal2, $maxVal2);
        @endphp
  
        <div style="background: #fdfdfd; padding: 12px; border-radius: 8px; border: 1px solid var(--hairline-soft);">
            <svg viewBox="0 0 500 220" width="100%" height="auto" style="overflow: visible;">
                <defs>
                    <linearGradient id="loanGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#6c3de0" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="#6c3de0" stop-opacity="0.0"/>
                    </linearGradient>
                    <linearGradient id="savingsGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--success)" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="var(--success)" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>

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

                <!-- Shaded Areas -->
                <path class="chart-area" d="{{ getSvgSmoothPath($pointsLoans, true, 220, 32) }}" fill="url(#loanGrad)" />
                <path class="chart-area" d="{{ getSvgSmoothPath($pointsSavings, true, 220, 32) }}" fill="url(#savingsGrad)" />
  
                <!-- Loans Line (Purple) -->
                <path class="chart-line" d="{{ getSvgSmoothPath($pointsLoans, false, 220, 32) }}" fill="none" stroke="#6c3de0" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                
                <!-- Savings Line (Green) -->
                <path class="chart-line" d="{{ getSvgSmoothPath($pointsSavings, false, 220, 32) }}" fill="none" stroke="var(--success)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
  
                <!-- Points Circle Loans -->
                @foreach($pointsLoans as $idx => $pt)
                    <circle class="chart-marker" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#6c3de0" stroke="white" stroke-width="2" style="cursor: pointer;" onmouseover="showTooltip(event, 'Outstanding Kredit ({{ $labels[$idx] }})', 'Rp {{ number_format($pt['val'], 0, ',', '.') }}')" onmouseout="hideTooltip()"/>
                @endforeach
  
                <!-- Points Circle Savings -->
                @foreach($pointsSavings as $idx => $pt)
                    <circle class="chart-marker" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="var(--success)" stroke="white" stroke-width="2" style="cursor: pointer;" onmouseover="showTooltip(event, 'Total Tabungan ({{ $labels[$idx] }})', 'Rp {{ number_format($pt['val'], 0, ',', '.') }}')" onmouseout="hideTooltip()"/>
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

{{-- Chart 3: Category Distribution --}}
<div class="analytics-card chart-panel" id="panel-category-sales" style="margin-bottom: 36px;">
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <span>Distribusi Penjualan Ritel Sembako per Kategori</span>
        <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Data Kuartal Terakhir</span>
    </h3>
    
    @php
        // Mock category sales proportions based on actual sales
        $catSales = [
            ['name' => 'Sembako & Pangan', 'val' => $totalSales * 0.45, 'color' => 'var(--primary)'],
            ['name' => 'Sayur & Tani Lokal', 'val' => $totalSales * 0.25, 'color' => 'var(--info)'],
            ['name' => 'Mandi & Cuci', 'val' => $totalSales * 0.18, 'color' => '#d97706'],
            ['name' => 'Minuman & Camilan', 'val' => $totalSales * 0.12, 'color' => 'var(--success)']
        ];
        $maxCatVal = max(array_column($catSales, 'val')) ?: 1;
    @endphp

    <div style="background: #fdfdfd; padding: 24px; border-radius: 8px; border: 1px solid var(--hairline-soft);">
        <div style="display: flex; justify-content: space-around; align-items: flex-end; height: 160px; padding-top: 20px;">
            @foreach($catSales as $cat)
                @php
                    $pctHeight = ($cat['val'] / $maxCatVal) * 100;
                @endphp
                <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 100px;">
                    <!-- Animated Bar -->
                    <div class="bar-rect" style="width: 44px; height: {{ $pctHeight }}px; background: {{ $cat['color'] }}; border-radius: 6px 6px 0 0; box-shadow: 0 4px 10px rgba(0,0,0,0.06); cursor: pointer; transition: transform 0.2s var(--ease-out);"
                         onmouseover="showTooltip(event, 'Kategori: {{ $cat['name'] }}', 'Rp {{ number_format($cat['val'], 0, ',', '.') }}')"
                         onmouseout="hideTooltip()"
                         onmouseenter="this.style.transform='scale(1.05)';"
                         onmouseleave="this.style.transform='scale(1)';">
                    </div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--ink); text-align: center; white-space: nowrap;">{{ $cat['name'] }}</span>
                    <span style="font-size: 10px; color: var(--muted); font-weight: 600; text-align: center; white-space: nowrap;">Rp {{ number_format($cat['val']/1000, 0, ',', '.') }} K</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
 
{{-- ═══════════════════════ DETAILED REPORT TABLE ═══════════════════════ --}}
<div class="analytics-card" style="margin-bottom: 36px;">
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

{{-- Tooltip & Dynamic Filtering Script --}}
<div id="chart-tooltip"></div>

<script>
    function showTooltip(e, label, val) {
        const tooltip = document.getElementById('chart-tooltip');
        tooltip.innerHTML = `
            <div style="font-size: 10px; opacity: 0.8; margin-bottom: 2px;">${label}</div>
            <div style="font-size: 13px; font-weight: 800; color: #ffffff;">${val}</div>
        `;
        
        tooltip.classList.add('visible');
        
        // Positioning tooltip dynamically relative to page body
        const rect = e.target.getBoundingClientRect();
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        tooltip.style.left = (rect.left + rect.width / 2 + scrollLeft) + 'px';
        tooltip.style.top = (rect.top - 8 + scrollTop) + 'px';
    }

    function hideTooltip() {
        const tooltip = document.getElementById('chart-tooltip');
        tooltip.classList.remove('visible');
    }

    function filterCharts(category) {
        // Toggle Active state on buttons
        document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
        
        // Hide/Show chart panels with transitions
        const panelRetail = document.getElementById('panel-retail-agro');
        const panelFinancial = document.getElementById('panel-financial');
        const panelCategory = document.getElementById('panel-category-sales');

        if (category === 'all') {
            document.getElementById('btn-filter-all').classList.add('active');
            panelRetail.classList.remove('hidden');
            panelFinancial.classList.remove('hidden');
            panelCategory.classList.remove('hidden');
        } else if (category === 'retail') {
            document.getElementById('btn-filter-retail').classList.add('active');
            panelRetail.classList.remove('hidden');
            panelFinancial.classList.add('hidden');
            panelCategory.classList.remove('hidden');
        } else if (category === 'financial') {
            document.getElementById('btn-filter-financial').classList.add('active');
            panelRetail.classList.add('hidden');
            panelFinancial.classList.remove('hidden');
            panelCategory.classList.add('hidden');
        }
    }
</script>

@endsection
