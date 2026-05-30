@extends('layouts.app')

@section('title', 'Dashboard Anggota - KDKMP')

@section('content')
<div style="border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 24px; margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 600;">Halo, {{ $member->user->name }}!</h1>
        <p style="color: var(--colors-muted); font-size: 14px; margin-top: 4px;">
            Nomor Anggota: <strong style="color: var(--colors-ink);">{{ $member->nomor_anggota }}</strong> | NIK: {{ $member->nik }}
        </p>
    </div>
    <div style="background-color: var(--colors-surface-soft); border: 1px solid var(--colors-hairline); border-radius: var(--rounded-md); padding: 12px 24px; text-align: center;">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--colors-muted);">Total Poin Loyalitas</span>
        <div style="font-size: 24px; font-weight: 700; color: var(--colors-primary);">⭐ {{ $member->total_poin }} Poin</div>
    </div>
</div>

<!-- Dashboard Grid Links (Airbnb cards) -->
<div class="dashboard-nav-grid">
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.savings') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">💰</span>
        <h3>Tabungan Saku</h3>
        <p>Lihat mutasi simpanan pokok, wajib, dan sukarela Anda.</p>
    </div>
    
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.loans') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🏦</span>
        <h3>Pinjaman Mikro</h3>
        <p>Ajukan modal usaha UMKM desa atau bayar cicilan pinjaman.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.crops') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🌾</span>
        <h3>Penyerapan Tani</h3>
        <p>Jual hasil panen cabai, bawang, padi langsung ke koperasi.</p>
    </div>

    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.orders') }}'">
        <span style="font-size: 24px; margin-bottom: 8px;">🛍️</span>
        <h3>Belanja Saya</h3>
        <p>Pantau status pesanan sembako dan riwayat transaksi belanja.</p>
    </div>
</div>

<!-- Core Info Rows -->
<div class="split-layout">
    <!-- Left: Savings & Loans Summary -->
    <div class="main-column" style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Savings Balances -->
        <div class="standard-card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px;">
                Ringkasan Simpanan Koperasi
            </h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                <div style="padding: 12px; background-color: var(--colors-surface-soft); border-radius: var(--rounded-sm);">
                    <span style="font-size: 12px; color: var(--colors-muted);">Simpanan Pokok</span>
                    <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">Rp {{ number_format($savingsBalances['pokok'], 0, ',', '.') }}</div>
                </div>
                <div style="padding: 12px; background-color: var(--colors-surface-soft); border-radius: var(--rounded-sm);">
                    <span style="font-size: 12px; color: var(--colors-muted);">Simpanan Wajib</span>
                    <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">Rp {{ number_format($savingsBalances['wajib'], 0, ',', '.') }}</div>
                </div>
                <div style="padding: 12px; background-color: var(--colors-surface-soft); border-radius: var(--rounded-sm);">
                    <span style="font-size: 12px; color: var(--colors-muted);">Simpanan Sukarela</span>
                    <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">Rp {{ number_format($savingsBalances['sukarela'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--colors-hairline-soft); padding-top: 16px;">
                <span>Total Saldo Tabungan: <strong>Rp {{ number_format($savingsBalances['total'], 0, ',', '.') }}</strong></span>
                <a href="{{ route('member.savings') }}" style="color: var(--colors-primary); font-weight: 600; font-size: 14px;">Setor Tabungan →</a>
            </div>
        </div>

        <!-- Loans Summary -->
        <div class="standard-card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px;">
                Status Pinjaman Aktif
            </h3>
            @if($activeLoan)
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--colors-muted);">Kode Pinjaman</span>
                        <strong style="color: var(--colors-ink);">{{ $activeLoan->loan_code }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--colors-muted);">Nominal Diajukan</span>
                        <strong>Rp {{ number_format($activeLoan->amount_requested, 0, ',', '.') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--colors-muted);">Bunga Flat</span>
                        <strong>{{ $activeLoan->interest_rate }}%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--colors-muted);">Status Persetujuan</span>
                        <span style="font-weight: 600; text-transform: uppercase; font-size: 12px; padding: 2px 8px; border-radius: 4px; 
                            {{ $activeLoan->status === 'active' ? 'background-color:#e6f6f0; color:#1a7f5a;' : '' }}
                            {{ $activeLoan->status === 'draft' ? 'background-color:#fff9e6; color:#b28900;' : '' }}
                            {{ $activeLoan->status === 'approved' ? 'background-color:#e6f2ff; color:#0052cc;' : '' }}
                            {{ $activeLoan->status === 'rejected' ? 'background-color:#ffebeb; color:#c13515;' : '' }}
                        ">
                            {{ $activeLoan->status }}
                        </span>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 16px; color: var(--colors-muted);">
                    <p style="font-size: 14px;">Anda tidak memiliki pinjaman aktif saat ini.</p>
                    <a href="{{ route('member.loans') }}" style="color: var(--colors-primary); font-weight: 600; font-size: 14px; margin-top: 8px; display: inline-block;">Ajukan Pinjaman Mikro →</a>
                </div>
            @endif
        </div>

    </div>

    <!-- Right: Recent Orders -->
    <div class="sticky-rail" style="flex: 1.2;">
        <div class="standard-card" style="box-shadow: var(--shadow-tier);">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px;">
                Belanjaan Terakhir
            </h3>
            @if($recentOrders->isEmpty())
                <p style="font-size: 14px; color: var(--colors-muted); text-align: center; padding: 16px;">Belum ada riwayat pemesanan belanja.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($recentOrders as $order)
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--colors-hairline-soft); padding-bottom: 12px; cursor: pointer;" onclick="window.location.href='{{ route('orders.show', $order->id) }}'">
                            <div>
                                <strong style="font-size: 14px; color: var(--colors-ink);">{{ $order->order_number }}</strong>
                                <div style="font-size: 12px; color: var(--colors-muted); margin-top: 2px;">
                                    {{ $order->created_at->format('d M Y') }} • {{ ucfirst($order->delivery_type) }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <strong style="font-size: 14px; color: var(--colors-ink);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; margin-top: 2px;
                                    {{ $order->payment_status === 'paid' ? 'color:#1a7f5a;' : '' }}
                                    {{ $order->payment_status === 'pending' ? 'color:#b28900;' : '' }}
                                    {{ $order->payment_status === 'cancelled' ? 'color:var(--colors-primary-error-text);' : '' }}
                                ">
                                    {{ $order->payment_status }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
