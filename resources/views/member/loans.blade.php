@extends('layouts.app')

@section('title', 'Kredit Usaha Mikro Koperasi - KDKMP')

@section('content')
<div style="margin-bottom: 24px;" class="reveal-left">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform='translateX(0)'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<h1 class="reveal-left delay-1" style="font-size: 28px; font-weight: 800; margin-bottom: 24px; color: var(--ink); letter-spacing: -0.5px;">Pinjaman Kredit Mikro Desa</h1>

<div class="split-layout">
    
    <!-- Left: Loan Status and Payment Schedule -->
    <div class="main-column reveal-scale delay-2">
        @if($activeLoan)
            <div class="card card-flush" style="box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 18px; font-weight: 700; margin: 0; border-bottom: 1px solid var(--hairline-soft); padding: 20px; background: var(--surface-md); color: var(--ink); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <span>Pinjaman Aktif Anda</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if($activeLoan->status === 'active' || $activeLoan->status === 'approved')
                            <a href="{{ route('member.loans.pdf', $activeLoan->id) }}" class="btn btn-secondary btn-sm" style="font-size: 12px; font-weight: 600; padding: 2px 12px; height: 26px; border-radius: 4px; border: 1.5px solid var(--hairline); display: inline-flex; align-items: center; gap: 4px;" data-no-loading>
                                📥 Unduh Slip PDF
                            </a>
                        @endif
                        <span class="badge {{ $activeLoan->status === 'active' ? 'badge-success' : ($activeLoan->status === 'draft' ? 'badge-warning' : 'badge-info') }}" style="margin: 0;">
                            {{ $activeLoan->status }}
                        </span>
                    </div>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 24px; border-bottom: 1px solid var(--hairline-soft);">
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Kode Kontrak</span>
                        <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: var(--ink);">{{ $activeLoan->loan_code }}</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Tenor Pinjaman</span>
                        <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: var(--ink);">{{ $activeLoan->tenor_months }} Bulan</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Nominal Diajukan</span>
                        <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: var(--ink);">Rp {{ number_format($activeLoan->amount_requested, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Nominal Disetujui</span>
                        <div style="font-size: 16px; font-weight: 800; margin-top: 4px; color: var(--primary);">
                            Rp {{ number_format($activeLoan->amount_approved, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Suku Bunga Koperasi</span>
                        <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: var(--ink);">{{ $activeLoan->interest_rate }}% Flat</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--muted); font-weight: 500;">Total Pengembalian</span>
                        @php 
                            $interestMultiplier = 1 + ($activeLoan->interest_rate / 100);
                            $totalExpected = $activeLoan->amount_approved * $interestMultiplier;
                        @endphp
                        <div style="font-size: 16px; font-weight: 700; margin-top: 4px; color: var(--ink);">
                            Rp {{ number_format($totalExpected, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($activeLoan->status === 'active')
                    <div style="padding: 20px;">
                        <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: var(--ink);">Riwayat Pembayaran Angsuran</h4>
                        @if($activeLoan->payments->isEmpty())
                            <div style="padding: 24px; text-align: center; border: 1.5px dashed var(--hairline); border-radius: var(--r-sm); color: var(--muted); font-size: 14px; font-weight: 500;">
                                Belum ada catatan angsuran dibayarkan.
                            </div>
                        @else
                            <div class="clean-table-container">
                                <table class="clean-table" style="margin-top: 0;">
                                    <thead style="background: var(--surface);">
                                        <tr>
                                            <th>Angsuran Ke-</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Denda Terlambat</th>
                                            <th style="text-align: right;">Jumlah Terbayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeLoan->payments as $payment)
                                            <tr style="transition: background var(--t-fast);">
                                                <td style="font-weight: 600; color: var(--ink);">{{ $payment->installment_number }} / {{ $activeLoan->tenor_months }}</td>
                                                <td style="color: var(--body);">{{ $payment->payment_date->format('d M Y H:i') }}</td>
                                                <td style="color: var(--danger);">Rp {{ number_format($payment->penalty, 0, ',', '.') }}</td>
                                                <td style="text-align: right; font-weight: 700; color: var(--success);">
                                                    Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @else
                    <div style="background-color: var(--warning-bg); padding: 16px; border-radius: var(--r-sm); font-size: 13px; color: var(--warning); text-align: center; font-weight: 500; margin: 20px; border: 1px solid var(--warning-border);">
                        ⏳ Menunggu verifikasi administrasi oleh pengurus koperasi untuk pencairan dana pinjaman.
                    </div>
                @endif
            </div>
        @else
            <div class="card" style="text-align: center; padding: 64px 32px; border: 2px dashed var(--hairline); box-shadow: none; color: var(--muted);">
                <div style="font-size: 48px; margin-bottom: 16px; animation: float-emoji 3s ease-in-out infinite;">🏦</div>
                <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin-bottom: 8px;">Tidak Ada Pinjaman Aktif</h3>
                <p style="font-size: 14px; max-width: 300px; margin: 0 auto;">Isi formulir di sebelah kanan untuk mengajukan pinjaman modal usaha kecil.</p>
            </div>
        @endif

        <!-- Loan History -->
        <div class="card card-flush reveal-scale delay-3" style="margin-top: 32px; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 18px; font-weight: 700; padding: 20px; border-bottom: 1px solid var(--hairline-soft); margin: 0; background: var(--surface-md); color: var(--ink);">Riwayat Seluruh Pinjaman</h3>
            @if($loans->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--muted); font-size: 14px;">
                    Belum ada riwayat pengajuan pinjaman sebelumnya.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead style="background: var(--surface);">
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal Diajukan</th>
                                <th>Tenor</th>
                                <th>Diajukan</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center; width: 80px;">Slip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr style="transition: background var(--t-fast);">
                                    <td style="font-weight: 700; color: var(--ink);">{{ $loan->loan_code }}</td>
                                    <td style="color: var(--body);">{{ $loan->created_at->format('d M Y') }}</td>
                                    <td style="font-weight: 600; color: var(--ink);">{{ $loan->tenor_months }} Bln</td>
                                    <td>Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @if($loan->status === 'paid_off')
                                            <span class="badge badge-success">LUNAS</span>
                                        @elseif($loan->status === 'rejected')
                                            <span class="badge badge-danger">DITOLAK</span>
                                        @elseif($loan->status === 'active')
                                            <span class="badge badge-info">AKTIF</span>
                                        @else
                                            <span class="badge badge-warning">DRAFT</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if(in_array($loan->status, ['active', 'approved', 'paid_off']))
                                            <a href="{{ route('member.loans.pdf', $loan->id) }}" class="btn btn-secondary btn-sm" style="padding: 2px 8px; height: auto; font-size: 11px; border-radius: 4px;" data-no-loading>
                                                📥 PDF
                                            </a>
                                        @else
                                            <span style="color: var(--muted); font-size: 11px; font-style: italic;">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Application Form -->
    <div class="sticky-rail reveal-right delay-4">
        <div class="card" style="box-shadow: var(--shadow-lg); border: 1.5px solid var(--hairline);">
            <h3 style="font-size: 18px; font-weight: 700; border-bottom: 1px dashed var(--hairline); padding-bottom: 16px; margin-bottom: 20px; color: var(--ink); display: flex; align-items: center; justify-content: space-between;">
                Ajukan Pinjaman
                <span style="font-size: 24px;">💸</span>
            </h3>
            
            @if($activeLoan)
                <div style="padding: 16px; background-color: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); border-radius: var(--r-sm); font-size: 13px; font-weight: 600; line-height: 1.5;">
                    ⚠️ Anda memiliki pengajuan pinjaman aktif ({{ strtoupper($activeLoan->status) }}). Lunasi atau selesaikan pinjaman saat ini sebelum mengajukan pinjaman baru.
                </div>
            @else
                <form action="{{ route('member.loans.apply') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="amount_requested">Nominal Pinjaman Modal</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-weight: 600;">Rp</span>
                            <input type="number" name="amount_requested" id="amount_requested" class="text-input" style="padding-left: 40px;" placeholder="Minimal 100.000" min="100000" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tenor_months">Tenor Pengembalian</label>
                        <div style="position: relative;">
                            <select name="tenor_months" id="tenor_months" class="text-input" style="height: 48px; padding-left: 14px; padding-right: 36px; appearance: none; cursor: pointer; background: var(--canvas);" required>
                                <option value="3">3 Bulan (Jangka Pendek)</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan (1 Tahun)</option>
                                <option value="24">24 Bulan</option>
                            </select>
                            <svg style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>

                    <div style="background-color: var(--surface); padding: 16px; border-radius: var(--r-md); font-size: 13px; color: var(--body); line-height: 1.5; margin-bottom: 24px; border: 1px solid var(--hairline-soft);">
                        📌 <strong style="color: var(--ink);">Catatan:</strong> Pengajuan pinjaman tunduk pada persetujuan Pengurus Koperasi. Bunga flat <strong style="color: var(--primary);">5.00%</strong> akan diterapkan secara otomatis per tahun.
                    </div>

                    <button type="submit" class="btn btn-primary btn-xl btn-full" style="font-weight: 700;">
                        Kirim Pengajuan Modal
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection