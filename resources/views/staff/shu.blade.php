@extends('layouts.admin')

@section('title', 'Kalkulator SHU Anggota - KDKMP')

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

    .shu-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .shu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }

    .shu-form-card {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .shu-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }

    .shu-action-card {
        background: linear-gradient(135deg, #fff1f2, #ffe4e6) !important;
        border: 1px solid rgba(225, 29, 72, 0.15) !important;
        box-shadow: 0 10px 30px -10px rgba(225, 29, 72, 0.1),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .shu-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(225, 29, 72, 0.15), inset 0 1px 0 #ffffff !important;
    }

    /* Form Input Polish */
    .form-group label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--muted);
        margin-bottom: 6px;
        display: block;
    }
    .text-input {
        border-radius: var(--r-sm);
        border: 1.5px solid var(--hairline);
        background: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        transition: all var(--t-fast) var(--ease-out);
        height: 44px;
        font-size: 13.5px;
    }
    .text-input:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }
    
    @keyframes emoji-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
</style>

{{-- Printable Header (Visible only when printing) --}}
<div class="print-header">
    <h1>KOPERASI {{ strtoupper(auth()->user()->branch->name) }} (KDKMP {{ strtoupper(auth()->user()->branch->code) }})</h1>
    <p>Laporan Estimasi Pembagian Sisa Hasil Usaha (SHU) Anggota Aktif</p>
    <p style="font-size: 11px; color: #555;">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
</div>

<div style="margin-bottom: 32px;" class="no-print">
    <h1 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Kalkulator Pembagian SHU</h1>
    <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">📊 Hitung dan bagikan dividen Sisa Hasil Usaha (SHU) tahunan secara otomatis bagi anggota koperasi</p>
</div>

<div class="split-layout">
    
    <!-- Left: Calculations Table -->
    <div class="main-column">
        <div class="shu-card card-flush" style="overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface)); border-top-left-radius: var(--r-lg); border-top-right-radius: var(--r-lg);">
                <h3 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--ink); letter-spacing: -0.3px; display: flex; align-items: center; gap: 6px;">
                    <span style="animation: emoji-bounce 2s ease-in-out infinite;">📈</span> Hasil Estimasi Pembagian SHU
                </h3>
                @if(!empty($distribution))
                    <button onclick="window.print()" class="btn-3d-secondary no-print" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; height: 32px; padding: 0 14px; border-radius: 100px;">
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
                                    <td style="font-weight: 700; color: var(--ink);">{{ $item['nomor_anggota'] }}</td>
                                    <td style="font-weight: 600;">{{ $item['name'] }}</td>
                                    <td style="text-align: center;"><span style="font-size: 11px; background: var(--surface-soft); padding: 2px 8px; border-radius: var(--r-full); border: 1px solid var(--hairline-soft); font-weight: 600; color: var(--primary);">⭐ {{ $item['points'] }} Poin</span></td>
                                    <td style="text-align: right; font-weight: 800; color: var(--success);">
                                        Rp {{ number_format($item['share'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: var(--surface-soft); font-weight: 800; font-size: 14px;">
                                <td colspan="2" style="padding: 16px; border-top: 2px solid var(--hairline-soft); color: var(--ink);">TOTAL PROYEKSI</td>
                                <td style="text-align: center; padding: 16px; border-top: 2px solid var(--hairline-soft); color: var(--primary);">⭐ {{ $totalPoints }} Poin</td>
                                <td style="text-align: right; padding: 16px; border-top: 2px solid var(--hairline-soft); color: var(--success); font-size: 15px;">
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
        <div class="shu-form-card">
            <h3 style="font-size: 16px; font-weight: 800; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin-bottom: 20px; color: var(--ink);">
                <span style="font-size: 18px;">💰</span> Input Nominal SHU
            </h3>
            
            <form action="{{ route('staff.shu') }}" method="GET" style="margin: 0;">
                
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="shu_amount">Jumlah SHU Bersih Dibagikan (Rp)</label>
                    <input type="number" name="shu_amount" id="shu_amount" class="text-input" placeholder="Masukkan total uang SHU, misal: 5000000" value="{{ $totalSHUAmount }}" min="1000" required>
                </div>

                <button type="submit" class="btn-3d-primary" style="width: 100%; height: 44px; font-size: 14px; border-radius: var(--r-sm);">Proyeksikan SHU</button>
            </form>

            <div style="font-size: 12px; color: var(--muted); line-height: 1.5; margin-top: 14px; background: var(--surface-soft); padding: 10px; border-radius: var(--r-sm); border: 1px solid var(--hairline-soft);">
                💡 <strong>Formula Koperasi:</strong><br>
                Dividen Anggota = (Poin Anggota / Total Poin Seluruh Anggota Aktif) * Dana SHU Bersih.
            </div>
        </div>

        @if(!empty($distribution))
        <div class="shu-action-card" style="margin-top: 24px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--danger); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <span style="animation: emoji-bounce 2s ease-in-out infinite;">⚡</span> Eksekusi Pembagian
            </h3>
            <p style="font-size: 13px; color: var(--body); margin-bottom: 18px; line-height: 1.5;">
                Dengan menekan tombol di bawah ini, SHU akan langsung disetorkan ke saldo <strong>Simpanan Sukarela</strong> masing-masing anggota dan <strong>Poin Loyalitas akan direset (0)</strong>.
            </p>
            
            <form action="{{ route('staff.shu') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengeksekusi pembagian SHU ini secara final? Saldo akan disetorkan dan poin anggota akan hangus / direset ke 0.')" style="margin: 0;">
                @csrf
                <input type="hidden" name="shu_amount" value="{{ $totalSHUAmount }}">
                <button type="submit" class="btn-3d-primary" style="width: 100%; height: 44px; border-radius: 100px; font-size: 13.5px; background: linear-gradient(135deg, var(--danger), #be123c) !important; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255,255,255,0.3) !important;">
                    Eksekusi Pembagian SHU
                </button>
            </form>
        </div>
        @endif
    </div>

</div>
@endsection
