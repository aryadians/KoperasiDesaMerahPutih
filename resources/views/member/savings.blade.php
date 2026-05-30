@extends('layouts.app')

@section('title', 'Mutasi Tabungan Koperasi - KDKMP')

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

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Tabungan Saku Koperasi</h1>

<!-- Balance Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px;">
    <div style="padding: 20px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas);">
        <span style="font-size: 12px; color: var(--colors-muted); font-weight: 600;">Simpanan Pokok</span>
        <div style="font-size: 20px; font-weight: 700; margin-top: 8px; color: var(--colors-ink);">
            Rp {{ number_format($balances['pokok'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11px; color: var(--colors-muted); margin-top: 4px;">Uang pangkal awal keanggotaan.</p>
    </div>
    <div style="padding: 20px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas);">
        <span style="font-size: 12px; color: var(--colors-muted); font-weight: 600;">Simpanan Wajib</span>
        <div style="font-size: 20px; font-weight: 700; margin-top: 8px; color: var(--colors-ink);">
            Rp {{ number_format($balances['wajib'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11px; color: var(--colors-muted); margin-top: 4px;">Iuran wajib bulanan anggota.</p>
    </div>
    <div style="padding: 20px; border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); background-color: var(--colors-canvas);">
        <span style="font-size: 12px; color: var(--colors-muted); font-weight: 600;">Simpanan Sukarela</span>
        <div style="font-size: 20px; font-weight: 700; margin-top: 8px; color: var(--colors-ink);">
            Rp {{ number_format($balances['sukarela'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11px; color: var(--colors-muted); margin-top: 4px;">Uang tabungan bebas ditarik.</p>
    </div>
    <div style="padding: 20px; border: 1px solid var(--colors-primary-disabled); border-radius: var(--rounded-md); background-color: #fff9fa;">
        <span style="font-size: 12px; color: var(--colors-primary); font-weight: 600;">Total Akumulasi</span>
        <div style="font-size: 20px; font-weight: 700; margin-top: 8px; color: var(--colors-primary);">
            Rp {{ number_format($balances['total'], 0, ',', '.') }}
        </div>
        <p style="font-size: 11px; color: var(--colors-muted); margin-top: 4px;">Total modal Anda di koperasi.</p>
    </div>
</div>

<div class="split-layout">
    
    <!-- Left: Transaction History List -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 600; padding: 20px; border-bottom: 1px solid var(--colors-hairline);">Riwayat Transaksi Setoran</h3>
            
            @if($savings->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--colors-muted);">
                    Belum ada riwayat setoran tabungan.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Simpanan</th>
                            <th>Keterangan</th>
                            <th style="text-align: right;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($savings as $saving)
                            <tr>
                                <td>{{ $saving->transaction_date->format('d M Y H:i') }}</td>
                                <td>
                                    <span style="font-weight: 600; text-transform: uppercase; font-size: 12px;
                                        {{ $saving->type === 'pokok' ? 'color:#0052cc;' : '' }}
                                        {{ $saving->type === 'wajib' ? 'color:#b28900;' : '' }}
                                        {{ $saving->type === 'sukarela' ? 'color:#1a7f5a;' : '' }}
                                    ">
                                        {{ $saving->type }}
                                    </span>
                                </td>
                                <td>{{ $saving->notes }}</td>
                                <td style="text-align: right; font-weight: 600; color: #1a7f5a;">
                                    + Rp {{ number_format($saving->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Deposit Form -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;">Setor Tabungan</h3>
            
            <form action="{{ route('member.savings.deposit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="type">Jenis Simpanan</label>
                    <select name="type" id="type" class="text-input" style="height: 48px; padding: 0 12px;" required>
                        <option value="pokok">Simpanan Pokok (Bergabung)</option>
                        <option value="wajib">Simpanan Wajib (Bulanan)</option>
                        <option value="sukarela">Simpanan Sukarela (Bebas)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Nominal Setoran (Rupiah)</label>
                    <input type="number" name="amount" id="amount" class="text-input" placeholder="Contoh: 100000" min="1000" required>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="notes">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" id="notes" class="text-input" placeholder="Setoran bulanan Mei 2026">
                </div>

                <button type="submit" class="button-primary">Proses Setoran</button>
            </form>
        </div>
    </div>

</div>
@endsection
