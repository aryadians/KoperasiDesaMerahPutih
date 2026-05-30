@extends('layouts.app')

@section('title', 'Verifikasi Penyerapan Tani - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('staff.dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard staf
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Kelola Penyerapan Hasil Panen Warga</h1>

<div class="standard-card" style="padding: 0; overflow: hidden;">
    @if($crops->isEmpty())
        <div style="padding: 32px; text-align: center; color: var(--colors-muted);">
            Belum ada hasil panen yang didaftarkan petani untuk diserap.
        </div>
    @else
        <table class="clean-table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Petani (Anggota)</th>
                    <th>Hasil Tani</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Total Payout</th>
                    <th>Status Penyerapan</th>
                    <th style="text-align: center; width: 250px;">Aksi Gudang / Kasir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($crops as $crop)
                    <tr>
                        <td style="font-weight: 600;">
                            <div>{{ $crop->member->user->name }}</div>
                            <span style="font-size: 11px; color: var(--colors-muted);">No. Anggota: {{ $crop->member->nomor_anggota }}</span>
                        </td>
                        <td>{{ $crop->product_name }}</td>
                        <td>{{ number_format($crop->quantity, 2) }} kg/unit</td>
                        <td>Rp {{ number_format($crop->price_per_unit, 0, ',', '.') }}</td>
                        <td style="font-weight: 600; color: #1a7f5a;">
                            Rp {{ number_format($crop->total_payout, 0, ',', '.') }}
                        </td>
                        <td>
                            <span style="font-weight: 600; text-transform: uppercase; font-size: 11px;
                                {{ $crop->status === 'paid' ? 'color:#1a7f5a;' : '' }}
                                {{ $crop->status === 'received' ? 'color:#0052cc;' : '' }}
                                {{ $crop->status === 'pending' ? 'color:#b28900;' : '' }}
                            ">
                                {{ $crop->status }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                @if($crop->status === 'pending')
                                    <form action="{{ route('staff.crops.update', [$crop->id, 'received']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="button-primary" style="height: 32px; font-size: 12px; padding: 0 12px; width: auto; background-color: #0052cc;">
                                            Sudah Diterima Gudang
                                        </button>
                                    </form>
                                @endif
                                
                                @if($crop->status === 'received')
                                    <form action="{{ route('staff.crops.update', [$crop->id, 'paid']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="button-primary" style="height: 32px; font-size: 12px; padding: 0 12px; width: auto; background-color: #1a7f5a;">
                                            Bayar Tunai / Transfer
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
