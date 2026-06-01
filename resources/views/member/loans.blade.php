@extends('layouts.app')

@section('title', 'Kredit Usaha Mikro Koperasi - KDKMP')

@section('content')
<style>
    .btn-3d-primary {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        width: 100%;
        height: 44px;
        border-radius: var(--r-sm);
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
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
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

    .card-3d {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04),
                    0 1px 2px rgba(0, 0, 0, 0.01),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .card-3d:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px -15px rgba(225, 29, 72, 0.08), 
                    0 1px 2px rgba(0, 0, 0, 0.01), 
                    inset 0 1px 0 #ffffff !important;
        border-color: rgba(225, 29, 72, 0.2) !important;
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
    .text-input, .form-select {
        border-radius: var(--r-sm);
        border: 1.5px solid var(--hairline);
        background: rgba(255, 255, 255, 0.8);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        transition: all var(--t-fast) var(--ease-out);
        height: 44px;
        font-size: 13.5px;
    }
    .text-input:focus, .form-select:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }
</style>

<div style="margin-bottom: 24px;" class="reveal-left">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 700; color: var(--muted); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform=''">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<div style="margin-bottom: 32px;" class="reveal-left delay-1">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--ink); letter-spacing: -0.8px; margin: 0;">🏦 Pinjaman Kredit Mikro Anggota</h1>
    <p style="color: var(--muted); font-size: 14.5px; margin-top: 4px; margin-bottom: 0;">Ajukan pembiayaan modal usaha mikro/tani atau catat riwayat angsuran.</p>
</div>

<div class="split-layout">
    
    <!-- Left: Loan Status and Payment Schedule -->
    <div class="main-column reveal-scale delay-2">
        @if($activeLoan)
            <div class="card-3d" style="overflow: hidden;">
                <h3 style="font-size: 17px; font-weight: 800; margin: 0; border-bottom: 1px solid var(--hairline-soft); padding: 20px; background: linear-gradient(to bottom, var(--surface-soft), var(--surface)); color: var(--ink); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <span>Pinjaman Aktif Anda</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        @if($activeLoan->status === 'active' || $activeLoan->status === 'approved')
                            <a href="{{ route('member.loans.pdf', $activeLoan->id) }}" class="btn-3d-secondary" style="font-size: 11.5px; font-weight: 700; padding: 0 12px; height: 28px; border-radius: 100px; display: inline-flex; align-items: center; gap: 4px;" data-no-loading>
                                📥 Unduh Slip PDF
                            </a>
                        @endif
                        @if($activeLoan->status === 'active')
                            <span class="badge badge-success" style="font-weight: 700;">ACTIVE</span>
                        @elseif($activeLoan->status === 'approved')
                            <span class="badge badge-info" style="font-weight: 700;">APPROVED</span>
                        @elseif($activeLoan->status === 'draft')
                            <span class="badge badge-warning" style="font-weight: 700;">PENDING</span>
                        @else
                            <span class="badge badge-neutral" style="font-weight: 700;">{{ strtoupper($activeLoan->status) }}</span>
                        @endif
                    </div>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 24px; border-bottom: 1px solid var(--hairline-soft);">
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Kode Kontrak</span>
                        <div style="font-size: 15px; font-weight: 800; margin-top: 4px; color: var(--ink);">{{ $activeLoan->loan_code }}</div>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Tenor Pinjaman</span>
                        <div style="font-size: 15px; font-weight: 800; margin-top: 4px; color: var(--ink);">{{ $activeLoan->tenor_months }} Bulan</div>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Nominal Diajukan</span>
                        <div style="font-size: 15px; font-weight: 800; margin-top: 4px; color: var(--ink);">Rp {{ number_format($activeLoan->amount_requested, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Nominal Disetujui</span>
                        <div style="font-size: 16px; font-weight: 800; margin-top: 4px; color: var(--primary);">
                            Rp {{ number_format($activeLoan->amount_approved, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Suku Bunga Koperasi</span>
                        <div style="font-size: 15px; font-weight: 800; margin-top: 4px; color: var(--ink);">{{ $activeLoan->interest_rate }}% Flat / Thn</div>
                    </div>
                    <div>
                        <span style="font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase;">Total Pengembalian</span>
                        @php 
                            $interestMultiplier = 1 + ($activeLoan->interest_rate / 100);
                            $totalExpected = $activeLoan->amount_approved * $interestMultiplier;
                        @endphp
                        <div style="font-size: 16px; font-weight: 800; margin-top: 4px; color: var(--ink);">
                            Rp {{ number_format($totalExpected, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($activeLoan->status === 'active')
                    <div style="padding: 24px;">
                        <h4 style="font-size: 15px; font-weight: 800; margin-bottom: 16px; color: var(--ink); display: flex; align-items: center; gap: 6px;">📋 Riwayat Pembayaran Angsuran</h4>
                        @if($activeLoan->payments->isEmpty())
                            <div style="padding: 32px; text-align: center; border: 1.5px dashed var(--hairline-soft); border-radius: var(--r-md); color: var(--muted); font-size: 13.5px; font-weight: 500;">
                                Belum ada catatan angsuran dibayarkan.
                            </div>
                        @else
                            <div class="clean-table-container">
                                <table class="clean-table" style="margin-top: 0;">
                                    <thead>
                                        <tr>
                                            <th>Angsuran Ke</th>
                                            <th>Tanggal &amp; Waktu Bayar</th>
                                            <th>Denda Keterlambatan</th>
                                            <th style="text-align: right;">Jumlah Terbayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeLoan->payments as $payment)
                                            <tr>
                                                <td style="font-weight: 800; color: var(--ink);">{{ $payment->installment_number }} / {{ $activeLoan->tenor_months }}</td>
                                                <td style="color: var(--body); font-weight: 500;">{{ $payment->payment_date->format('d M Y H:i') }} WIB</td>
                                                <td style="color: var(--danger); font-weight: 700;">Rp {{ number_format($payment->penalty, 0, ',', '.') }}</td>
                                                <td style="text-align: right; font-weight: 800; color: var(--success); font-size: 14.5px;">
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
                    <div style="background-color: #fffbeb; padding: 18px; border-radius: var(--r-md); font-size: 13px; color: #b45309; text-align: center; font-weight: 600; margin: 24px; border: 1px solid rgba(245, 158, 11, 0.15);">
                        ⏳ Menunggu verifikasi administrasi oleh pengurus koperasi untuk pencairan dana pinjaman.
                    </div>
                @endif
            </div>
        @else
            <div class="card-3d" style="text-align: center; padding: 64px 32px; color: var(--muted);">
                <div style="font-size: 56px; margin-bottom: 16px; animation: emoji-bounce 3s ease-in-out infinite;">🏦</div>
                <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-bottom: 8px;">Tidak Ada Pinjaman Aktif</h3>
                <p style="font-size: 14px; max-width: 320px; margin: 0 auto; line-height: 1.5;">Isi formulir di sebelah kanan untuk mengajukan pinjaman modal usaha kecil keanggotaan.</p>
            </div>
        @endif

        <!-- Loan History -->
        <div class="card-3d reveal-scale delay-3" style="margin-top: 32px; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface));">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--ink); margin: 0;">📜 Riwayat Seluruh Pinjaman Anda</h3>
            </div>
            @if($loans->isEmpty())
                <div style="padding: 48px; text-align: center; color: var(--muted); font-size: 14px;">
                    Belum ada riwayat pengajuan pinjaman sebelumnya.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tenor</th>
                                <th>Jumlah Pengajuan</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center; width: 80px;">Slip PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr>
                                    <td style="font-weight: 800; color: var(--ink);">{{ $loan->loan_code }}</td>
                                    <td style="color: var(--body); font-weight: 500;">{{ $loan->created_at->format('d M Y') }}</td>
                                    <td style="font-weight: 700; color: var(--ink);">{{ $loan->tenor_months }} Bln</td>
                                    <td style="font-weight: 700; color: var(--body);">Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @if($loan->status === 'paid_off')
                                            <span class="badge badge-success" style="font-weight: 700;">LUNAS</span>
                                        @elseif($loan->status === 'rejected')
                                            <span class="badge badge-danger" style="font-weight: 700;">DITOLAK</span>
                                        @elseif($loan->status === 'active')
                                            <span class="badge badge-info" style="font-weight: 700;">AKTIF</span>
                                        @else
                                            <span class="badge badge-warning" style="font-weight: 700;">PENDING</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if(in_array($loan->status, ['active', 'approved', 'paid_off']))
                                            <a href="{{ route('member.loans.pdf', $loan->id) }}" class="btn-3d-secondary" style="padding: 0 10px; height: 26px; font-size: 11px; border-radius: 100px;" data-no-loading>
                                                PDF
                                            </a>
                                        @else
                                            <span style="color: var(--muted); font-size: 11.5px; font-weight: 600; font-style: italic;">Draft</span>
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
        <div class="card-3d" style="background: linear-gradient(135deg, #ffffff, #f8fafc) !important;">
            <h3 style="font-size: 17px; font-weight: 800; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin-bottom: 20px; color: var(--ink); display: flex; align-items: center; justify-content: space-between;">
                <span>🏦 Ajukan Pinjaman</span>
                <span style="font-size: 22px; animation: emoji-bounce 2s infinite;">💸</span>
            </h3>
            
            @if($activeLoan)
                <div style="padding: 16px; background-color: #fef2f2; color: var(--danger); border: 1px solid rgba(220,38,38,0.15); border-radius: var(--r-sm); font-size: 13px; font-weight: 600; line-height: 1.5;">
                    ⚠️ Anda memiliki pengajuan pinjaman aktif ({{ strtoupper($activeLoan->status) }}). Lunasi atau selesaikan pinjaman saat ini sebelum mengajukan pinjaman baru.
                </div>
            @else
                <form action="{{ route('member.loans.apply') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Mengirim...';" style="margin: 0;">
                    @csrf
                    
                    <div class="form-group">
                        <label for="amount_requested">Nominal Pinjaman Modal</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-weight: 700; font-size: 13.5px;">Rp</span>
                            <input type="number" name="amount_requested" id="amount_requested" class="text-input" style="padding-left: 40px; width: 100%;" placeholder="Minimal 100.000" min="100000" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 14px;">
                        <label for="tenor_months">Tenor Pengembalian</label>
                        <div style="position: relative;">
                            <select name="tenor_months" id="tenor_months" class="form-select" style="width: 100%; font-weight: 600; padding-left: 14px;" required>
                                <option value="3">3 Bulan (Jangka Pendek)</option>
                                <option value="6">6 Bulan</option>
                                <option value="12" selected>12 Bulan (1 Tahun)</option>
                                <option value="24">24 Bulan</option>
                            </select>
                        </div>
                    </div>

                    <div style="background-color: var(--surface-soft); padding: 14px; border-radius: var(--r-sm); font-size: 12.5px; color: var(--body); line-height: 1.5; margin: 20px 0; border: 1px solid var(--hairline-soft);">
                        📌 <strong style="color: var(--ink);">Info Bunga:</strong> Pengajuan pinjaman koperasi dikenakan bunga flat sebesar <strong style="color: var(--primary);">5.00%</strong> flat per tahun secara otomatis.
                    </div>

                    <!-- Real-time Installment Simulation Card -->
                    <div id="simulation-card" style="display: none; background: rgba(225, 29, 72, 0.03); border: 1px solid rgba(225, 29, 72, 0.15); border-radius: var(--r-sm); padding: 14px; margin-bottom: 20px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                        <h4 style="font-size: 13px; font-weight: 800; color: var(--ink); margin: 0 0 10px 0; display: flex; align-items: center; gap: 4px;">🧮 Simulasi Angsuran Bulanan</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12.5px;">
                            <div>
                                <span style="color: var(--muted); font-size: 10.5px; font-weight: 600;">Pokok Pinjaman:</span>
                                <div style="font-weight: 700; color: var(--ink);" id="sim-principal">Rp 0</div>
                            </div>
                            <div>
                                <span style="color: var(--muted); font-size: 10.5px; font-weight: 600;">Bunga (5% Flat/Thn):</span>
                                <div style="font-weight: 700; color: var(--primary);" id="sim-interest">Rp 0</div>
                            </div>
                            <div style="grid-column: span 2; height: 1px; background: var(--hairline-soft); margin: 4px 0;"></div>
                            <div>
                                <span style="color: var(--muted); font-size: 10.5px; font-weight: 600;">Total Bayar:</span>
                                <div style="font-weight: 700; color: var(--ink);" id="sim-total">Rp 0</div>
                            </div>
                            <div>
                                <span style="color: var(--muted); font-size: 10.5px; font-weight: 700; color: var(--success);">Angsuran / Bln:</span>
                                <div style="font-weight: 800; color: var(--success); font-size: 14px;" id="sim-monthly">Rp 0</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-3d-primary">Kirim Pengajuan Modal ➔</button>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const amountInput = document.getElementById('amount_requested');
                        const tenorSelect = document.getElementById('tenor_months');
                        const simCard = document.getElementById('simulation-card');

                        if (amountInput && tenorSelect) {
                            function updateSimulation() {
                                const amount = parseFloat(amountInput.value);
                                const tenor = parseInt(tenorSelect.value);

                                if (!isNaN(amount) && amount >= 100000) {
                                    // Bunga flat per tahun = 5%. 
                                    // Total bunga = amount * 0.05 * (tenor / 12)
                                    const interestRate = 0.05;
                                    const interest = amount * interestRate * (tenor / 12);
                                    const total = amount + interest;
                                    const monthly = total / tenor;

                                    document.getElementById('sim-principal').textContent = 'Rp ' + Math.round(amount).toLocaleString('id-ID');
                                    document.getElementById('sim-interest').textContent = 'Rp ' + Math.round(interest).toLocaleString('id-ID');
                                    document.getElementById('sim-total').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
                                    document.getElementById('sim-monthly').textContent = 'Rp ' + Math.round(monthly).toLocaleString('id-ID');

                                    simCard.style.display = 'block';
                                } else {
                                    simCard.style.display = 'none';
                                }
                            }

                            amountInput.addEventListener('input', updateSimulation);
                            amountInput.addEventListener('change', updateSimulation);
                            tenorSelect.addEventListener('change', updateSimulation);
                        }
                    });
                </script>
            @endif
        </div>
    </div>

</div>
@endsection