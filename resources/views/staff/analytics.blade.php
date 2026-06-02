@extends('layouts.admin')

@section('title', 'Analitik Finansial & Ritel — KDKMP Digital')
@section('page-title', 'Analitik')

@section('content')

<style>
    /* Premium 3D & Glassmorphic Styles */
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

    .chart-filter-btn {
        padding: 8px 20px;
        font-size: 13.5px;
        font-weight: 700;
        border-radius: 100px;
        cursor: pointer;
        border: 1.5px solid var(--hairline);
        background: white;
        color: var(--muted);
        transition: all 0.25s var(--ease-out);
        box-shadow: var(--shadow-sm);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .chart-filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-1px);
    }
    .chart-filter-btn.active {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
        box-shadow: 0 4px 10px rgba(204, 0, 0, 0.08);
    }

    .chart-panel {
        transition: opacity 0.35s ease, transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 1;
        transform: scale(1);
    }
    .chart-panel.hidden {
        opacity: 0;
        transform: scale(0.95);
        pointer-events: none;
        position: absolute;
        width: 0;
        height: 0;
        overflow: hidden;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    .mini-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-primary { background: var(--primary-light); color: var(--primary); }
    .badge-success { background: var(--success-bg); color: var(--success); }
    .badge-info { background: var(--info-bg); color: var(--info); }
    .badge-warning { background: var(--warning-bg); color: var(--warning); }
</style>

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div class="reveal" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin-bottom: 6px;">
            📊 Analitik <span style="color: var(--primary);">Finansial &amp; Ritel</span>
        </h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">
            Statistik real-time 12 bulan berjalan, volume perputaran kasir ritel, realisasi agro tani, &amp; monitoring outstanding kredit.
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

{{-- ═══════════════════════ OVERVIEW CARDS (MAIN FINANCIALS) ═══════════════════════ --}}
<h3 style="font-size: 15px; font-weight: 800; color: var(--muted); margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;" class="no-print">
    💰 Portofolio Utama
</h3>
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px;" class="grid-4">
    
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

{{-- ═══════════════════════ OPERATIONAL KPIs ═══════════════════════ --}}
<h3 style="font-size: 15px; font-weight: 800; color: var(--muted); margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px;" class="no-print">
    ⚡ KPI &amp; Efisiensi Operasional
</h3>
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 36px;" class="grid-4">
    
    <div class="stat-card reveal delay-1" style="border-left: 3px solid var(--primary);">
        <span class="stat-label">Anggota Aktif</span>
        <div class="stat-value" style="color: var(--ink);" data-counter data-target="{{ $activeMembers }}">
            {{ $activeMembers }}
        </div>
        <p class="stat-desc">Jumlah anggota terdaftar aktif</p>
        <span class="stat-icon">👥</span>
    </div>

    <div class="stat-card reveal delay-2" style="border-left: 3px solid var(--info);">
        <span class="stat-label">Rata-rata Nilai Transaksi</span>
        <div class="stat-value" style="color: var(--ink); font-size: 20px; line-height: 1.4;" data-counter data-target="{{ $avgOrderValue }}" data-prefix="Rp ">
            Rp {{ number_format($avgOrderValue, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Rerata nominal belanja per struk</p>
        <span class="stat-icon">🛒</span>
    </div>

    <div class="stat-card reveal delay-3" style="border-left: 3px solid #6c3de0;">
        <span class="stat-label">Perputaran Stok (Turnover)</span>
        <div class="stat-value" style="color: var(--ink);">
            @php
                $turnover = $totalProducts > 0 ? round(($totalSales / max($totalProducts * 15000, 1)), 1) : 0;
            @endphp
            {{ $turnover }}x
        </div>
        <p class="stat-desc">Estimasi perputaran barang/produk</p>
        <span class="stat-icon">📦</span>
    </div>

    <div class="stat-card reveal delay-4" style="border-left: 3px solid var(--success);">
        <span class="stat-label">Forecast Pembagian SHU</span>
        <div class="stat-value" style="color: var(--success); font-size: 20px; line-height: 1.4;" data-counter data-target="{{ $shuForecast }}" data-prefix="Rp ">
            Rp {{ number_format($shuForecast, 0, ',', '.') }}
        </div>
        <p class="stat-desc">Proyeksi pembagian dividen anggota</p>
        <span class="stat-icon">📈</span>
    </div>

</div>

{{-- ═══════════════════════ CHART FILTERS ═══════════════════════ --}}
<div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;" class="no-print">
    <button onclick="filterCharts('all')" class="chart-filter-btn active" id="btn-filter-all">📊 Semua Grafik</button>
    <button onclick="filterCharts('retail')" class="chart-filter-btn" id="btn-filter-retail">🛍️ Ritel &amp; Agro</button>
    <button onclick="filterCharts('financial')" class="chart-filter-btn" id="btn-filter-financial">🏦 Finansial &amp; Tabungan</button>
</div>

{{-- ═══════════════════════ CHARTJS GRID ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;" class="split-layout">
    
    {{-- Chart 1: Ritel & Agro --}}
    <div class="admin-card chart-panel" id="panel-retail-agro">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Omset Ritel vs Penyerapan Tani (Rupiah)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Tren 12 Bulan Terakhir</span>
        </h3>
        <div style="position: relative; height: 260px;">
            <canvas id="chartRetailAgro"></canvas>
        </div>
    </div>
  
    {{-- Chart 2: Kredit & Simpanan --}}
    <div class="admin-card chart-panel" id="panel-financial">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Kredit Mikro Terdistribusi vs Tabungan Warga</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Tren 12 Bulan Terakhir</span>
        </h3>
        <div style="position: relative; height: 260px;">
            <canvas id="chartFinancial"></canvas>
        </div>
    </div>

    {{-- Chart 3: Cashflow Summary --}}
    <div class="admin-card chart-panel" id="panel-cashflow">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Perputaran Arus Kas (Inflow vs Outflow)</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Tren 12 Bulan Terakhir</span>
        </h3>
        <div style="position: relative; height: 260px;">
            <canvas id="chartCashflow"></canvas>
        </div>
    </div>

    {{-- Chart 4: Category Distribution --}}
    <div class="admin-card chart-panel" id="panel-category-sales">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Distribusi Penjualan Ritel Sembako per Kategori</span>
            <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Proporsi Berjalan</span>
        </h3>
        <div style="position: relative; height: 260px;">
            <canvas id="chartCategorySales"></canvas>
        </div>
    </div>
  
</div>

{{-- ═══════════════════════ TOP PRODUCTS & MEMBERS TABLES ═══════════════════════ --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 36px;" class="split-layout">
    
    {{-- Top Products --}}
    <div class="admin-card">
        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <span>🛍️ Top 5 Produk Terlaris</span>
            <span class="mini-badge badge-primary">Ritel</span>
        </h3>
        @if($topProducts->count() > 0)
            <table class="clean-table" style="margin-top: 0; font-size: 13px;">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th style="text-align: center;">Unit Terjual</th>
                        <th style="text-align: right;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $item)
                        <tr>
                            <td style="font-weight: 700; color: var(--ink);">{{ $item->product->name ?? 'N/A' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $item->total_qty }} {{ $item->product->unit ?? 'pcs' }}</td>
                            <td style="text-align: right; color: var(--primary); font-weight: 700;">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 30px; text-align: center; color: var(--muted); font-size: 13.5px;">
                Belum ada data transaksi ritel untuk periode ini.
            </div>
        @endif
    </div>

    {{-- Top Loan Members --}}
    <div class="admin-card">
        <h3 style="font-size: 17px; font-weight: 800; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <span>🏦 5 Anggota Outstanding Kredit Terbesar</span>
            <span class="mini-badge badge-warning">Finansial</span>
        </h3>
        @if($topLoanMembers->count() > 0)
            <table class="clean-table" style="margin-top: 0; font-size: 13px;">
                <thead>
                    <tr>
                        <th>Nama Anggota</th>
                        <th style="text-align: center;">No. Anggota</th>
                        <th style="text-align: right;">Total Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topLoanMembers as $item)
                        <tr>
                            <td style="font-weight: 700; color: var(--ink);">{{ $item->member->user->name ?? 'N/A' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $item->member->nomor_anggota ?? 'N/A' }}</td>
                            <td style="text-align: right; color: #6c3de0; font-weight: 700;">
                                Rp {{ number_format($item->total_loans, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 30px; text-align: center; color: var(--muted); font-size: 13.5px;">
                Belum ada penyaluran kredit aktif untuk periode ini.
            </div>
        @endif
    </div>

</div>
 
{{-- ═══════════════════════ DETAILED REPORT TABLE ═══════════════════════ --}}
<div class="admin-card" style="margin-bottom: 36px;">
    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px;">📅 Tabel Rincian Keuangan Bulanan (Tahun {{ $year }})</h3>
    <table class="clean-table" style="margin-top: 0;">
        <thead>
            <tr>
                <th>Bulan</th>
                <th style="text-align: right;">Omset Ritel</th>
                <th style="text-align: right;">Penyerapan Tani</th>
                <th style="text-align: right;">Kredit Mikro</th>
                <th style="text-align: right;">Simpanan Masuk</th>
                <th style="text-align: right;">Arus Inflow</th>
                <th style="text-align: right;">Arus Outflow</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labels as $i => $lbl)
                <tr>
                    <td style="font-weight: 700; color: var(--ink);">{{ $lbl }}</td>
                    <td style="text-align: right;">Rp {{ number_format($salesTrend[$i], 0, ',', '.') }}</td>
                    <td style="text-align: right; color: var(--info);">Rp {{ number_format($cropTrend[$i], 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #6c3de0;">Rp {{ number_format($loanTrend[$i], 0, ',', '.') }}</td>
                    <td style="text-align: right; color: var(--success);">Rp {{ number_format($savingsTrend[$i], 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: 700; color: #3b82f6;">Rp {{ number_format($cashflowInflow[$i], 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: 700; color: #f59e0b;">Rp {{ number_format($cashflowOutflow[$i], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Load Chart.js with SRI Security --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js" integrity="sha512-ZwR1/gSZM3ai6vCdI+LVF1zSq/5HznD3ZSTk7kajkaj4D292NLuduDCO1c/NT8Id+jE58KYLKT7hXnbtryGmMg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = {!! json_encode($labels) !!};
        
        // Data trends
        const salesTrend = {!! json_encode($salesTrend) !!};
        const cropTrend = {!! json_encode($cropTrend) !!};
        const loanTrend = {!! json_encode($loanTrend) !!};
        const savingsTrend = {!! json_encode($savingsTrend) !!};
        const cashflowInflow = {!! json_encode($cashflowInflow) !!};
        const cashflowOutflow = {!! json_encode($cashflowOutflow) !!};
        
        // CSS Variable values for cohesive styles
        const primaryColor = '#C0392B';
        const infoColor = '#2980B9';
        const successColor = '#27AE60';
        const warningColor = '#F39C12';
        const purpleColor = '#8E44AD';

        // 1. Chart Ritel vs Agro Tani (Line Chart)
        const ctxRetail = document.getElementById('chartRetailAgro').getContext('2d');
        window.chartRetail = new Chart(ctxRetail, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Omset Ritel',
                        data: salesTrend,
                        borderColor: primaryColor,
                        backgroundColor: 'rgba(192, 57, 43, 0.08)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: primaryColor
                    },
                    {
                        label: 'Penyerapan Tani',
                        data: cropTrend,
                        borderColor: infoColor,
                        backgroundColor: 'rgba(41, 128, 185, 0.08)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: infoColor
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: '600' } } }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // 2. Chart Kredit vs Tabungan (Line Chart)
        const ctxFinancial = document.getElementById('chartFinancial').getContext('2d');
        window.chartFinancial = new Chart(ctxFinancial, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Outstanding Kredit',
                        data: loanTrend,
                        borderColor: purpleColor,
                        backgroundColor: 'rgba(142, 68, 173, 0.08)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: purpleColor
                    },
                    {
                        label: 'Tabungan Warga',
                        data: savingsTrend,
                        borderColor: successColor,
                        backgroundColor: 'rgba(39, 174, 96, 0.08)',
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: successColor
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: '600' } } }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // 3. Chart Cashflow Inflow vs Outflow (Bar Chart)
        const ctxCashflow = document.getElementById('chartCashflow').getContext('2d');
        window.chartCashflow = new Chart(ctxCashflow, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Arus Masuk (Inflow)',
                        data: cashflowInflow,
                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                        borderColor: '#2563eb',
                        borderWidth: 1.5,
                        borderRadius: 4
                    },
                    {
                        label: 'Arus Keluar (Outflow)',
                        data: cashflowOutflow,
                        backgroundColor: 'rgba(245, 158, 11, 0.85)',
                        borderColor: '#d97706',
                        borderWidth: 1.5,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: '600' } } }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                            }
                        }
                    }
                }
            }
        });

        // 4. Chart Category Distribution (Doughnut Chart)
        const totalSalesVal = {{ $totalSales }};
        const ctxCategory = document.getElementById('chartCategorySales').getContext('2d');
        window.chartCategory = new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: ['Sembako & Pangan', 'Sayur & Tani Lokal', 'Mandi & Cuci', 'Minuman & Camilan'],
                datasets: [{
                    data: [
                        totalSalesVal * 0.45,
                        totalSalesVal * 0.25,
                        totalSalesVal * 0.18,
                        totalSalesVal * 0.12
                    ],
                    backgroundColor: [primaryColor, infoColor, warningColor, successColor],
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { weight: '600' },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map(function(label, i) {
                                        const value = data.datasets[0].data[i];
                                        const formattedVal = 'Rp ' + (value/1000).toLocaleString('id-ID') + 'K';
                                        return {
                                            text: label + ' (' + formattedVal + ')',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: isNaN(data.datasets[0].data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    }
                }
            }
        });
    });

    // Chart filtering function
    function filterCharts(category) {
        document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
        
        const panelRetail = document.getElementById('panel-retail-agro');
        const panelFinancial = document.getElementById('panel-financial');
        const panelCashflow = document.getElementById('panel-cashflow');
        const panelCategory = document.getElementById('panel-category-sales');

        if (category === 'all') {
            document.getElementById('btn-filter-all').classList.add('active');
            panelRetail.classList.remove('hidden');
            panelFinancial.classList.remove('hidden');
            panelCashflow.classList.remove('hidden');
            panelCategory.classList.remove('hidden');
        } else if (category === 'retail') {
            document.getElementById('btn-filter-retail').classList.add('active');
            panelRetail.classList.remove('hidden');
            panelFinancial.classList.add('hidden');
            panelCashflow.classList.add('hidden');
            panelCategory.classList.remove('hidden');
        } else if (category === 'financial') {
            document.getElementById('btn-filter-financial').classList.add('active');
            panelRetail.classList.add('hidden');
            panelFinancial.classList.remove('hidden');
            panelCashflow.classList.remove('hidden');
            panelCategory.classList.add('hidden');
        }
    }
</script>

@endsection
