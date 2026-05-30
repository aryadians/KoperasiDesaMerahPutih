@extends('layouts.admin')

@section('title', 'Kalkulator SHU Anggota - KDKMP')

@section('content')
{{-- Printable Header (Visible only when printing) --}}
<div class="print-header">
    <h1>KOPERASI DESA MERAH PUTIH (KDKMP)</h1>
    <p>Laporan Estimasi Pembagian Sisa Hasil Usaha (SHU) Anggota Aktif</p>
    <p style="font-size: 11px; color: #555;">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;" class="no-print">Kalkulator Pembagian SHU Ke Anggota</h1>

<div class="split-layout">
    
    <!-- Left: Calculations Table -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--colors-hairline);">
                <h3 style="font-size: 18px; font-weight: 600; margin: 0;">Hasil Estimasi Pembagian SHU</h3>
                @if(!empty($distribution))
                    <button onclick="window.print()" class="btn btn-sm btn-ghost no-print" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                        🖨️ Cetak Laporan / PDF
                    </button>
                @endif
            </div>
            
            @if(empty($distribution))
                <div style="padding: 48px; text-align: center; color: var(--colors-muted);">
                    Input nominal SHU Bersih di sebelah kanan untuk memproyeksikan dividen bagi anggota aktif.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Nomor Anggota</th>
                            <th>Nama Anggota</th>
                            <th style="text-align: center;">Total Poin Loyalitas</th>
                            <th style="text-align: right;">Estimasi SHU Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalPoints = 0; 
                            $totalDividends = 0;
                        @endphp
                        @foreach($distribution as $item)
                            @php 
                                $totalPoints += $item['points'];
                                $totalDividends += $item['share'];
                            @endphp
                            <tr>
                                <td style="font-weight: 600;">{{ $item['nomor_anggota'] }}</td>
                                <td>{{ $item['name'] }}</td>
                                <td style="text-align: center;">⭐ {{ $item['points'] }} Poin</td>
                                <td style="text-align: right; font-weight: 700; color: #1a7f5a;">
                                    Rp {{ number_format($item['share'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: var(--colors-surface-soft); font-weight: 700;">
                            <td colspan="2" style="padding: 16px; border-top: 2px solid var(--colors-border-strong);">TOTAL PROYEKSI</td>
                            <td style="text-align: center; padding: 16px; border-top: 2px solid var(--colors-border-strong);">⭐ {{ $totalPoints }} Poin</td>
                            <td style="text-align: right; padding: 16px; border-top: 2px solid var(--colors-border-strong); color: #1a7f5a;">
                                Rp {{ number_format($totalDividends, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Calculation Input -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;">Input Nominal SHU</h3>
            
            <form action="{{ route('staff.shu') }}" method="GET">
                
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="shu_amount">Jumlah SHU Bersih Dibagikan (Rp)</label>
                    <input type="number" name="shu_amount" id="shu_amount" class="text-input" placeholder="Masukkan total uang SHU, misal: 5000000" value="{{ $totalSHUAmount }}" min="1000" required>
                </div>

                <button type="submit" class="button-primary">Proyeksikan SHU</button>
            </form>

            <div style="font-size: 12px; color: var(--colors-muted); line-height: 1.5; margin-top: 8px;">
                💡 <strong>Formula Koperasi:</strong><br>
                Dividen Anggota = (Poin Anggota / Total Poin Seluruh Anggota Aktif) * Dana SHU Bersih.
            </div>
        </div>
    </div>

</div>
@endsection
