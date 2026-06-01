@extends('layouts.app')

@section('title', 'Mutasi Tabungan Koperasi - KDKMP')

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

    .balance-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .balance-card {
        padding: 24px 20px;
        border-radius: var(--r-lg);
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.01), inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .balance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
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
        height: 46px;
        font-size: 13.5px;
    }
    .text-input:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }
</style>

<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; text-decoration: none; width: fit-content; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform=''">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; color: var(--ink); letter-spacing: -0.8px; margin: 0;">💰 Tabungan Saku Anggota</h1>
        <p style="color: var(--muted); font-size: 14.5px; margin-top: 4px; margin-bottom: 0;">Mutasi lengkap simpanan pokok, wajib, dan sukarela KDKMP.</p>
    </div>
    <a href="{{ route('member.savings.pdf') }}" class="btn-3d-secondary" style="font-size: 13px; height: 38px; padding: 0 20px; border-radius: 100px; display: inline-flex; align-items: center; gap: 6px;" data-no-loading>
        📥 Unduh Laporan PDF
    </a>
</div>

<!-- Balance Cards -->
<div class="balance-card-grid">
    <div class="balance-card" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: rgba(59, 130, 246, 0.15);">
        <span style="font-size: 11px; color: #1e3a8a; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">🔒 Simpanan Pokok</span>
        <div style="font-size: 22px; font-weight: 800; margin-top: 10px; color: #1e40af;">
            Rp {{ number_format($balances['pokok'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11.5px; color: #3b82f6; margin-top: 6px; margin-bottom: 0; font-weight: 500;">Uang pangkal awal keanggotaan.</p>
    </div>
    <div class="balance-card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: rgba(245, 158, 11, 0.15);">
        <span style="font-size: 11px; color: #78350f; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">📅 Simpanan Wajib</span>
        <div style="font-size: 22px; font-weight: 800; margin-top: 10px; color: #b45309;">
            Rp {{ number_format($balances['wajib'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11.5px; color: #d97706; margin-top: 6px; margin-bottom: 0; font-weight: 500;">Iuran bulanan anggota koperasi.</p>
    </div>
    <div class="balance-card" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-color: rgba(16, 185, 129, 0.15);">
        <span style="font-size: 11px; color: #064e3b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">💸 Simpanan Sukarela</span>
        <div style="font-size: 22px; font-weight: 800; margin-top: 10px; color: #047857;">
            Rp {{ number_format($balances['sukarela'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11.5px; color: #10b981; margin-top: 6px; margin-bottom: 0; font-weight: 500;">Uang tabungan bebas ditarik.</p>
    </div>
    <div class="balance-card" style="background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); border-color: rgba(225, 29, 72, 0.15);">
        <span style="font-size: 11px; color: #9f1239; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">⭐ Total Akumulasi</span>
        <div style="font-size: 22px; font-weight: 800; margin-top: 10px; color: #be123c;">
            Rp {{ number_format($balances['total'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11.5px; color: #f43f5e; margin-top: 6px; margin-bottom: 0; font-weight: 500;">Total modal Anda di Koperasi.</p>
    </div>
</div>

<div class="split-layout">
    
    <!-- Left: Transaction History List -->
    <div class="main-column">
        <div class="card-3d" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface));">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--ink); margin: 0;">📜 Riwayat Mutasi Simpanan</h3>
            </div>
            
            @if($savings->isEmpty())
                <div style="padding: 48px; text-align: center; color: var(--muted);">
                    Belum ada riwayat setoran tabungan.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>Tanggal &amp; Waktu</th>
                                <th>Jenis Simpanan</th>
                                <th>Keterangan Catatan</th>
                                <th style="text-align: right;">Jumlah Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($savings as $saving)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ink);">{{ $saving->transaction_date->format('d M Y') }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">{{ $saving->transaction_date->format('H:i') }} WIB</span>
                                    </td>
                                    <td>
                                        @if($saving->type === 'pokok')
                                            <span style="background: #eff6ff; color: #1e40af; border: 1px solid rgba(59,130,246,0.15); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px; text-transform: uppercase;">POKOK</span>
                                        @elseif($saving->type === 'wajib')
                                            <span style="background: #fffbeb; color: #b45309; border: 1px solid rgba(245,158,11,0.15); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px; text-transform: uppercase;">WAJIB</span>
                                        @else
                                            <span style="background: #ecfdf5; color: #047857; border: 1px solid rgba(16,185,129,0.15); font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 100px; text-transform: uppercase;">SUKARELA</span>
                                        @endif
                                    </td>
                                    <td style="color: var(--body); font-weight: 500;">{{ $saving->notes ?? '-' }}</td>
                                    <td style="text-align: right; font-weight: 800; color: var(--success); font-size: 14.5px;">
                                        + Rp {{ number_format($saving->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Deposit Form -->
    <div class="sticky-rail">
        <div class="card-3d" style="background: linear-gradient(135deg, #ffffff, #f8fafc) !important;">
            <h3 style="font-size: 17px; font-weight: 800; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin-bottom: 20px; color: var(--ink);">
                💰 Tambah Setoran
            </h3>
            
            <form action="{{ route('member.savings.deposit') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Memproses...';" style="margin: 0;">
                @csrf
                
                <div class="form-group">
                    <label for="type">Jenis Simpanan</label>
                    <select name="type" id="type" class="text-input" style="height: 44px; padding: 0 12px; font-weight: 600; width: 100%;" required>
                        <option value="pokok">Simpanan Pokok (Pendaftaran)</option>
                        <option value="wajib">Simpanan Wajib (Bulanan)</option>
                        <option value="sukarela" selected>Simpanan Sukarela (Bebas Tabung)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label for="amount">Nominal Setoran (Rupiah)</label>
                    <input type="number" name="amount" id="amount" class="text-input" placeholder="Contoh: 100000" min="1000" required style="width: 100%;">
                </div>

                <div class="form-group" style="margin-top: 14px; margin-bottom: 24px;">
                    <label for="notes">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" id="notes" class="text-input" placeholder="Misal: Setoran wajib bulan ini" style="width: 100%;">
                </div>

                <button type="submit" class="btn-3d-primary">Setor Saldo Sekarang ➔</button>
            </form>
        </div>
    </div>

</div>
@endsection
