@extends('layouts.admin')

@section('title', 'Persetujuan Kredit & Cicilan - KDKMP')

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

    .loans-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all var(--t-base) var(--ease-bounce);
    }

    .loans-form-card {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .loans-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }

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
        height: 40px;
        font-size: 13.5px;
    }
    .text-input:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }
</style>

<h1 style="font-size: 28px; font-weight: 800; margin-bottom: 24px; color: var(--ink); letter-spacing: -0.5px;">Manajemen Pinjaman Kredit Mikro</h1>

<div class="split-layout">
    
    <!-- Left: Loans List -->
    <div class="main-column">
        <div class="loans-card" style="overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 700; padding: 20px; border-bottom: 1px solid var(--hairline); color: var(--ink);">Daftar Pengajuan Pinjaman</h3>
            
            @if($loans->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--muted);">
                    Belum ada pengajuan pinjaman kredit dari anggota.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Kode / Tenor</th>
                            <th>Pengajuan</th>
                            <th>Disetujui</th>
                            <th>Status</th>
                            <th style="text-align: center; width: 220px;">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--ink);">{{ $loan->member->user->name }}</div>
                                    <span style="font-size: 11px; color: var(--muted);">No. Anggota: {{ $loan->member->nomor_anggota }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">{{ $loan->loan_code }}</div>
                                    <span style="font-size: 11px; color: var(--muted);">Tenor: {{ $loan->tenor_months }} Bulan</span>
                                </td>
                                <td>Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
                                <td style="font-weight: 700; color: var(--primary);">
                                    Rp {{ number_format($loan->amount_approved, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span style="font-weight: 700; text-transform: uppercase; font-size: 11px;
                                        {{ $loan->status === 'paid_off' ? 'color:#1a7f5a;' : '' }}
                                        {{ $loan->status === 'rejected' ? 'color:#c13515;' : '' }}
                                        {{ $loan->status === 'active' ? 'color:#0052cc;' : '' }}
                                        {{ $loan->status === 'approved' ? 'color:#008080;' : '' }}
                                        {{ $loan->status === 'draft' ? 'color:#b28900;' : '' }}
                                    ">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        <!-- Actions based on status -->
                                        @if($loan->status === 'draft')
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'approved']) }}" method="POST" style="display: flex; gap: 6px; align-items: center; justify-content: center;">
                                                @csrf
                                                <input type="number" name="amount_approved" value="{{ (int)$loan->amount_requested }}" class="text-input" style="height: 32px; width: 100px; padding: 2px 8px; font-size: 12px;" placeholder="Approve Rp">
                                                <button type="submit" class="btn-3d-primary" style="height: 32px; font-size: 11px; padding: 0 10px; width: auto; background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; box-shadow: 0 4px 12px rgba(59,130,246,0.18), inset 0 1px 0 rgba(255,255,255,0.25) !important;">Setuju</button>
                                            </form>
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'rejected']) }}" method="POST" style="margin-top: 4px;">
                                                @csrf
                                                <button type="submit" class="btn-3d-secondary" style="height: 28px; font-size: 11px; padding: 0 10px; width: 100%; color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important;">
                                                    Tolak
                                                </button>
                                            </form>
                                        @elseif($loan->status === 'approved')
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'active']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-3d-primary" style="height: 32px; font-size: 11px; padding: 0 12px; width: 100%; background: linear-gradient(135deg, #059669, #047857) !important; box-shadow: 0 4px 12px rgba(5,150,105,0.18), inset 0 1px 0 rgba(255,255,255,0.25) !important;">
                                                    Cairkan Uang
                                                </button>
                                            </form>
                                        @elseif($loan->status === 'active')
                                            <button type="button" class="btn-3d-secondary" style="height: 32px; font-size: 11px; padding: 0 12px; width: 100%;" onclick="loadPaymentForm({{ $loan->id }}, '{{ $loan->loan_code }}', {{ $loan->amount_approved }}, {{ $loan->interest_rate }}, {{ $loan->payments->count() + 1 }})">
                                                Bayar Cicilan
                                            </button>
                                        @else
                                            <span style="font-size: 12px; color: var(--muted); font-style: italic;">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Payment Posting Drawer -->
    <div class="sticky-rail">
        <div class="loans-form-card" id="payment-panel" style="display: none;">
            <h3 style="font-size: 18px; font-weight: 700; border-bottom: 1px solid var(--hairline); padding-bottom: 12px; color: var(--ink); margin-top: 0;">Catat Cicilan Masuk</h3>
            
            <form action="{{ route('staff.loans.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="loan_id" id="form-loan-id">

                <div style="background-color: var(--surface); padding: 12px; border-radius: var(--r-sm); font-size: 13px; margin-bottom: 16px; border: 1px solid rgba(0,0,0,0.04);">
                    Pinjaman: <strong id="form-loan-code" style="color: var(--ink);">-</strong><br>
                    Tagihan Pokok: <strong id="form-loan-base" style="color: var(--primary);">-</strong>
                </div>

                <div class="form-group">
                    <label for="installment_number">Cicilan Angsuran Ke-</label>
                    <input type="number" name="installment_number" id="form-installment-number" class="text-input" required readonly style="width: 100%; box-sizing: border-box; background: var(--surface-strong);">
                </div>

                <div class="form-group">
                    <label for="amount_paid">Nominal Dibayarkan (Rupiah)</label>
                    <input type="number" name="amount_paid" id="form-amount-paid" class="text-input" placeholder="Contoh: 150000" min="1000" required style="width: 100%; box-sizing: border-box;">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="penalty">Denda Keterlambatan (Rupiah)</label>
                    <input type="number" name="penalty" id="form-penalty" value="0" class="text-input" placeholder="Denda jika terlambat" min="0" required style="width: 100%; box-sizing: border-box;">
                </div>

                <button type="submit" class="btn-3d-primary" style="width: 100%; height: 40px;">Posting Pembayaran</button>
                <button type="button" class="btn-3d-secondary" style="margin-top: 8px; width: 100%; height: 40px;" onclick="document.getElementById('payment-panel').style.display='none'">
                    Batal
                </button>
            </form>
        </div>
        
        <div class="loans-card" id="info-panel" style="padding: 24px;">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--ink); margin-top: 0;">Pencatatan Cicilan</h3>
            <p style="font-size: 13px; color: var(--muted); line-height: 1.5; margin: 0;">
                Klik tombol <strong>"Bayar Cicilan"</strong> pada pinjaman aktif di tabel untuk membuka form input angsuran masuk.
            </p>
        </div>
    </div>

</div>

<script>
    function loadPaymentForm(id, code, approvedAmount, interestRate, nextInstallment) {
        document.getElementById('info-panel').style.display = 'none';
        
        const panel = document.getElementById('payment-panel');
        panel.style.display = 'block';
        
        document.getElementById('form-loan-id').value = id;
        document.getElementById('form-loan-code').textContent = code;
        document.getElementById('form-installment-number').value = nextInstallment;
        
        // Calculate monthly expected
        const totalExpected = approvedAmount * (1 + (interestRate / 100));
        // A rough monthly payment suggestion
        const suggestion = Math.round(approvedAmount / 12);
        
        document.getElementById('form-loan-base').textContent = 'Rp ' + approvedAmount.toLocaleString('id-ID');
        document.getElementById('form-amount-paid').value = suggestion;
    }
</script>
@endsection
