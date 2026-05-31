@extends('layouts.admin')

@section('title', 'Kalkulator SHU Anggota - KDKMP')

@section('content')
{{-- Printable Header (Visible only when printing) --}}
<div class="print-header">
    <h1>KOPERASI {{ strtoupper(auth()->user()->branch->name) }} (KDKMP {{ strtoupper(auth()->user()->branch->code) }})</h1>
    <p>Laporan Estimasi Pembagian Sisa Hasil Usaha (SHU) Anggota Aktif</p>
    <p style="font-size: 11px; color: #555;">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;" class="no-print">Kalkulator Pembagian SHU Ke Anggota</h1>

<div class="split-layout">
    
    <!-- Left: Calculations Table -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline);">
                <h3 style="font-size: 18px; font-weight: 600; margin: 0;">Hasil Estimasi Pembagian SHU</h3>
                @if(!empty($distribution))
                    <button onclick="window.print()" class="btn btn-sm btn-ghost no-print" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                        🖨️ Cetak Laporan / PDF
                    </button>
                @endif
            </div>
            
            @if(empty($distribution))
                <div style="padding: 48px; text-align: center; color: var(--muted);">
                    Input nominal SHU Bersih di sebelah kanan untuk memproyeksikan dividen bagi anggota aktif.
                </div>
            @else
                <div class="clean-table-container">
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
                            <tr style="background-color: var(--surface); font-weight: 700;">
                                <td colspan="2" style="padding: 16px; border-top: 2px solid var(--hairline);">TOTAL PROYEKSI</td>
                                <td style="text-align: center; padding: 16px; border-top: 2px solid var(--hairline);">⭐ {{ $totalPoints }} Poin</td>
                                <td style="text-align: right; padding: 16px; border-top: 2px solid var(--hairline); color: #1a7f5a;">
                                    Rp {{ number_format($totalDividends, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Calculation Input -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">Input Nominal SHU</h3>
            
            <form action="{{ route('staff.shu') }}" method="GET">
                
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="shu_amount">Jumlah SHU Bersih Dibagikan (Rp)</label>
                    <input type="number" name="shu_amount" id="shu_amount" class="text-input" placeholder="Masukkan total uang SHU, misal: 5000000" value="{{ $totalSHUAmount }}" min="1000" required>
                </div>

                <button type="submit" class="button-primary">Proyeksikan SHU</button>
            </form>

            <div style="font-size: 12px; color: var(--muted); line-height: 1.5; margin-top: 8px;">
                💡 <strong>Formula Koperasi:</strong><br>
                Dividen Anggota = (Poin Anggota / Total Poin Seluruh Anggota Aktif) * Dana SHU Bersih.
            </div>
        </div>

        @if(!empty($distribution))
        <div class="reservation-card" style="margin-top: 24px; border-color: var(--primary); background: var(--primary-light);">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--primary-dark); margin-bottom: 8px;">Eksekusi Pembagian</h3>
            <p style="font-size: 13px; color: var(--body); margin-bottom: 16px; line-height: 1.5;">
                Dengan menekan tombol di bawah ini, SHU akan langsung disetorkan ke saldo <strong>Simpanan Sukarela</strong> masing-masing anggota dan <strong>Poin Loyalitas akan direset (0)</strong>.
            </p>
            
            <form action="{{ route('staff.shu') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengeksekusi pembagian SHU ini secara final? Saldo akan disetorkan dan poin anggota akan hangus / direset ke 0.')">
                @csrf
                <input type="hidden" name="shu_amount" value="{{ $totalSHUAmount }}">
                <button type="submit" class="btn btn-primary btn-full btn-lg" style="border-radius: 100px;">
                    Eksekusi Pembagian SHU
                </button>
            </form>
        </div>
        @endif
    </div>

</div>
@endsection
