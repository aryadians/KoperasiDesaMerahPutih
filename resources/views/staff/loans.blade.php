@extends('layouts.admin')

@section('title', 'Persetujuan Kredit & Cicilan - KDKMP')

@section('content')

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Manajemen Pinjaman Kredit Mikro</h1>

<div class="split-layout">
    
    <!-- Left: Loans List -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 600; padding: 20px; border-bottom: 1px solid var(--hairline);">Daftar Pengajuan Pinjaman</h3>
            
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
                                    <div style="font-weight: 600;">{{ $loan->member->user->name }}</div>
                                    <span style="font-size: 11px; color: var(--muted);">No. Anggota: {{ $loan->member->nomor_anggota }}</span>
                                </td>
                                <td>
                                    <div>{{ $loan->loan_code }}</div>
                                    <span style="font-size: 11px; color: var(--muted);">Tenor: {{ $loan->tenor_months }} Bulan</span>
                                </td>
                                <td>Rp {{ number_format($loan->amount_requested, 0, ',', '.') }}</td>
                                <td style="font-weight: 600;">
                                    Rp {{ number_format($loan->amount_approved, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span style="font-weight: 600; text-transform: uppercase; font-size: 11px;
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
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'approved']) }}" method="POST" style="display: flex; gap: 4px; align-items: center;">
                                                @csrf
                                                <input type="number" name="amount_approved" value="{{ (int)$loan->amount_requested }}" class="text-input" style="height: 28px; width: 100px; padding: 2px 4px; font-size: 12px;" placeholder="Approve Rp">
                                                <button type="submit" class="button-primary" style="height: 28px; font-size: 11px; padding: 0 8px; width: auto; background-color: #0052cc;">Setuju</button>
                                            </form>
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'rejected']) }}" method="POST" style="margin-top: 4px;">
                                                @csrf
                                                <button type="submit" class="button-secondary" style="height: 28px; font-size: 11px; padding: 0 8px; width: 100%; border-color: var(--danger); color: var(--danger);">
                                                    Tolak
                                                </button>
                                            </form>
                                        @elseif($loan->status === 'approved')
                                            <form action="{{ route('staff.loans.update', [$loan->id, 'active']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="button-primary" style="height: 32px; font-size: 12px; padding: 0 12px; width: 100%; background-color: #1a7f5a;">
                                                    Cairkan Uang
                                                </button>
                                            </form>
                                        @elseif($loan->status === 'active')
                                            <button type="button" class="button-secondary" style="height: 32px; font-size: 12px; padding: 0 12px;" onclick="loadPaymentForm({{ $loan->id }}, '{{ $loan->loan_code }}', {{ $loan->amount_approved }}, {{ $loan->interest_rate }}, {{ $loan->payments->count() + 1 }})">
                                                Bayar Cicilan
                                            </button>
                                        @else
                                            <span style="font-size: 12px; color: var(--muted);">Selesai</span>
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
        <div class="reservation-card" id="payment-panel" style="display: none;">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">Catat Cicilan Masuk</h3>
            
            <form action="{{ route('staff.loans.payment') }}" method="POST">
                @csrf
                <input type="hidden" name="loan_id" id="form-loan-id">

                <div style="background-color: var(--surface); padding: 12px; border-radius: var(--r-sm); font-size: 13px; margin-bottom: 16px;">
                    Pinjaman: <strong id="form-loan-code">-</strong><br>
                    Tagihan Pokok: <strong id="form-loan-base">-</strong>
                </div>

                <div class="form-group">
                    <label for="installment_number">Cicilan Angsuran Ke-</label>
                    <input type="number" name="installment_number" id="form-installment-number" class="text-input" required readonly>
                </div>

                <div class="form-group">
                    <label for="amount_paid">Nominal Dibayarkan (Rupiah)</label>
                    <input type="number" name="amount_paid" id="form-amount-paid" class="text-input" placeholder="Contoh: 150000" min="1000" required>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="penalty">Denda Keterlambatan (Rupiah)</label>
                    <input type="number" name="penalty" id="form-penalty" value="0" class="text-input" placeholder="Denda jika terlambat" min="0" required>
                </div>

                <button type="submit" class="button-primary">Posting Pembayaran</button>
                <button type="button" class="button-secondary" style="margin-top: 8px; border-color: var(--hairline);" onclick="document.getElementById('payment-panel').style.display='none'">
                    Batal
                </button>
            </form>
        </div>
        
        <div class="standard-card" id="info-panel">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Pencatatan Cicilan</h3>
            <p style="font-size: 13px; color: var(--muted); line-height: 1.5;">
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
