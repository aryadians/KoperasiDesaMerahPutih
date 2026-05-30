@extends('layouts.app')

@section('title', 'Kredit Usaha Mikro Koperasi - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Pinjaman Kredit Mikro Desa</h1>

<div class="split-layout">
    
    <!-- Left: Loan Status and Payment Schedule -->
    <div class="main-column">
        @if($activeLoan)
            <div class="standard-card">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <span>Pinjaman Aktif Anda</span>
                    <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 4px 10px; border-radius: var(--rounded-full);
                        {{ $activeLoan->status === 'active' ? 'background-color:#e6f6f0; color:#1a7f5a;' : '' }}
                        {{ $activeLoan->status === 'draft' ? 'background-color:#fff9e6; color:#b28900;' : '' }}
                        {{ $activeLoan->status === 'approved' ? 'background-color:#e6f2ff; color:#0052cc;' : '' }}
                    ">
                        {{ $activeLoan->status }}
                    </span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Kode Kontrak</span>
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">{{ $activeLoan->loan_code }}</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Tenor Pinjaman</span>
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">{{ $activeLoan->tenor_months }} Bulan</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Nominal Diajukan</span>
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">Rp {{ number_format($activeLoan->amount_requested, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Nominal Disetujui</span>
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px; color: var(--colors-primary);">
                            Rp {{ number_format($activeLoan->amount_approved, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Suku Bunga Koperasi</span>
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">{{ $activeLoan->interest_rate }}% Flat</div>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--colors-muted);">Total Pengembalian</span>
                        @php 
                            $interestMultiplier = 1 + ($activeLoan->interest_rate / 100);
                            $totalExpected = $activeLoan->amount_approved * $interestMultiplier;
                        @endphp
                        <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">
                            Rp {{ number_format($totalExpected, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($activeLoan->status === 'active')
                    <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; margin-top: 24px;">Riwayat Pembayaran Angsuran</h4>
                    @if($activeLoan->payments->isEmpty())
                        <div style="padding: 16px; text-align: center; border: 1px dashed var(--colors-hairline); border-radius: var(--rounded-sm); color: var(--colors-muted); font-size: 14px;">
                            Belum ada catatan angsuran dibayarkan.
                        </div>
                    @else
                        <table class="clean-table" style="margin-top: 0;">
                            <thead>
                                <tr>
                                    <th>Angsuran Ke-</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Denda Terlambat</th>
                                    <th style="text-align: right;">Jumlah Terbayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeLoan->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->installment_number }} / {{ $activeLoan->tenor_months }}</td>
                                        <td>{{ $payment->payment_date->format('d M Y H:i') }}</td>
                                        <td>Rp {{ number_format($payment->penalty, 0, ',', '.') }}</td>
                                        <td style="text-align: right; font-weight: 600; color: #1a7f5a;">
                                            Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @else
                    <div style="background-color: var(--colors-surface-soft); padding: 16px; border-radius: var(--rounded-sm); font-size: 13px; color: var(--colors-muted); text-align: center;">
                        Menunggu verifikasi administrasi oleh pengurus koperasi untuk pencairan dana pinjaman.
                    </div>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 48px; border: 1px dashed var(--colors-hairline); border-radius: var(--rounded-md); color: var(--colors-muted);">
                <span style="font-size: 32px;">📄</span>
                <p style="font-size: 15px; margin-top: 12px;">Anda tidak memiliki pengajuan pinjaman aktif saat ini.</p>
                <p style="font-size: 13px; color: var(--colors-muted); margin-top: 4px;">Isi formulir di sebelah kanan untuk mengajukan pinjaman modal usaha kecil.</p>
            </div>
        @endif

        <!-- Loan History -->
        <div class="standard-card" style="margin-top: 24px; padding: 0; overflow: hidden;">
            <h3 style="font-size: 16px; font-weight: 600; padding: 20px; border-bottom: 1px solid var(--colors-hairline);">Riwayat Seluruh Pinjaman</h3>
            @if($loans->isEmpty())
                <div style="padding: 24px; text-align: center; color: var(--colors-muted); font-size: 14px;">
                    Belum ada riwayat pengajuan pinjaman sebelumnya.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal Diajukan</th>
                            <th>Tenor</th>
                            <th>Diajukan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr>
                                <td>{{ $loan->loan_code }}</td>
                                <td>{{ $loan->created_at->format('d M Y') }}</td>
                                <td>{{ $loan->tenor_months }} Bln</td>
                                <td>Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
                                <td>
                                    <span style="font-weight: 600; text-transform: uppercase; font-size: 11px;
                                        {{ $loan->status === 'paid_off' ? 'color:#1a7f5a;' : '' }}
                                        {{ $loan->status === 'rejected' ? 'color:#c13515;' : '' }}
                                        {{ $loan->status === 'active' ? 'color:#0052cc;' : '' }}
                                        {{ $loan->status === 'draft' ? 'color:#b28900;' : '' }}
                                    ">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Application Form -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;">Ajukan Pinjaman</h3>
            
            @if($activeLoan)
                <div style="padding: 16px; background-color: #ffebeb; color: var(--colors-primary-error-text); border-radius: var(--rounded-sm); font-size: 13px; font-weight: 500; line-height: 1.5;">
                    ⚠️ Anda memiliki pengajuan pinjaman aktif ({{ $activeLoan->status }}). Lunasi atau selesaikan pinjaman saat ini sebelum mengajukan pinjaman baru.
                </div>
            @else
                <form action="{{ route('member.loans.apply') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="amount_requested">Nominal Pinjaman Modal (Rupiah)</label>
                        <input type="number" name="amount_requested" id="amount_requested" class="text-input" placeholder="Minimal Rp 100.000" min="100000" required>
                    </div>

                    <div class="form-group">
                        <label for="tenor_months">Tenor Pengembalian (Bulan)</label>
                        <select name="tenor_months" id="tenor_months" class="text-input" style="height: 48px; padding: 0 12px;" required>
                            <option value="3">3 Bulan (Jangka Pendek)</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan (1 Tahun)</option>
                            <option value="24">24 Bulan</option>
                        </select>
                    </div>

                    <div style="background-color: var(--colors-surface-soft); padding: 12px; border-radius: var(--rounded-sm); font-size: 13px; color: var(--colors-body); line-height: 1.5; margin-bottom: 20px;">
                        📌 **Catatan:** Pengajuan pinjaman tunduk pada persetujuan Pengurus Koperasi. Bunga flat <strong>5.00%</strong> akan diterapkan secara otomatis.
                    </div>

                    <button type="submit" class="button-primary">Ajukan Modal</button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
