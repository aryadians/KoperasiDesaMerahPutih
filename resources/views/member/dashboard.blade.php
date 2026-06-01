@extends('layouts.app')
@section('title', 'Dashboard Anggota — KDKMP Digital')
@section('content')

<style>
    .member-hero-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #111827 60%, #030712 100%) !important;
        border-radius: 20px;
        padding: 36px 40px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 16px 48px rgba(15, 23, 42, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
    }
    .btn-member-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: white !important;
        color: #1e1b4b !important;
        font-size: 13.5px;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 100px;
        box-shadow: 0 4px 12px rgba(255,255,255,0.15), inset 0 1px 0 rgba(255,255,255,0.4) !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-decoration: none;
    }
    .btn-member-action:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 24px rgba(255,255,255,0.25), inset 0 1px 0 rgba(255,255,255,0.5) !important;
    }
    .btn-member-secondary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.08) !important;
        color: white !important;
        font-size: 13px;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 100px;
        border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.05) !important;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-decoration: none;
    }
    .btn-member-secondary:hover {
        background: rgba(255, 255, 255, 0.16) !important;
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.1) !important;
    }
    .card-3d {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04),
                    0 1px 2px rgba(0,0,0,0.01),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        padding: 24px;
    }
    .card-3d:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0,0,0,0.08), inset 0 1px 0 #ffffff !important;
    }
    .saving-capsule {
        background: var(--surface-soft);
        border-radius: var(--r-md);
        border: 1px solid var(--hairline-soft);
        padding: 16px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .saving-capsule:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.03), inset 0 1px 0 #ffffff !important;
        background: #ffffff;
        border-color: var(--hairline) !important;
    }
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
    }
    .btn-3d-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    }
    .btn-3d-primary:active {
        transform: translateY(0);
    }
</style>

{{-- HERO MEMBER CARD --}}
<div class="reveal member-hero-card">
    {{-- decorative circles --}}
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;right:80px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none;"></div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;position:relative;z-index:1;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.12); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; padding: 4px 12px; border-radius: 100px; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.15);">
                💚 Anggota Aktif
            </div>
            <h1 style="font-size:32px;font-weight:800;letter-spacing:-0.8px;margin-bottom:6px; text-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                Halo, {{ $member->user->name }}! 👋
            </h1>
            <p style="opacity:0.85;font-size:14.5px;">
                No. Anggota: <strong style="opacity:1; color: #ffccd5;">{{ $member->nomor_anggota }}</strong>
                &nbsp;·&nbsp; NIK: {{ $member->nik }}
            </p>
        </div>
        <div style="background:rgba(255,255,255,0.08);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.15);border-radius:18px;padding:18px 28px;text-align:center;min-width:160px;box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;opacity:0.75;margin-bottom:6px;color:#cbd5e1;">Poin Loyalitas</div>
            <div style="font-size:28px;font-weight:800;letter-spacing:-0.5px; color: #fbbf24;">⭐ {{ number_format($member->total_poin) }}</div>
            <div style="font-size:11px;opacity:0.6;margin-top:4px;">Dividen SHU Tahunan</div>
        </div>
    </div>

    {{-- Quick action strip --}}
    <div style="display:flex;gap:12px;margin-top:32px;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('catalog.index') }}" class="btn-member-action">
            🛒 Belanja Sembako
        </a>
        <a href="{{ route('member.savings') }}" class="btn-member-secondary">
            💰 Setor Tabungan
        </a>
        <a href="{{ route('member.loans') }}" class="btn-member-secondary">
            🏦 Pinjaman Mikro
        </a>
    </div>
</div>

{{-- NAVIGATION CARDS --}}
<div class="dashboard-nav-grid reveal" style="margin-bottom: 32px;">
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.savings') }}'">
        <span class="nav-card-icon">💰</span>
        <h3>Tabungan Saku</h3>
        <p>Lihat mutasi simpanan pokok, wajib, dan sukarela.</p>
    </div>
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.loans') }}'">
        <span class="nav-card-icon">🏦</span>
        <h3>Pinjaman Mikro</h3>
        <p>Ajukan modal usaha UMKM atau bayar cicilan.</p>
    </div>
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.crops') }}'">
        <span class="nav-card-icon">🌾</span>
        <h3>Penyerapan Tani</h3>
        <p>Jual hasil panen langsung ke koperasi desa.</p>
    </div>
    <div class="dashboard-nav-card" onclick="window.location.href='{{ route('member.orders') }}'">
        <span class="nav-card-icon">🛍️</span>
        <h3>Belanja Saya</h3>
        <p>Pantau status pesanan dan riwayat transaksi.</p>
    </div>
</div>

{{-- MAIN CONTENT SPLIT --}}
<div class="split-layout">
    <div class="main-column" style="display:flex;flex-direction:column;gap:24px;">

        {{-- Iuran Wajib Status Alert --}}
        <div class="card-3d reveal-left" style="border-left: 5px solid {{ $iuranWajibPaidThisMonth ? 'var(--success)' : 'var(--danger)' }} !important;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="font-size:32px;animation: emoji-bounce 3s ease-in-out infinite;">{{ $iuranWajibPaidThisMonth ? '✅' : '📅' }}</div>
                    <div>
                        <h4 style="font-size:15px;font-weight:800;color:var(--ink);margin:0;">Status Iuran Wajib Bulanan</h4>
                        <p style="font-size:12.5px;color:var(--muted);margin-top:2px;">
                            Bulan {{ Carbon\Carbon::now()->translatedFormat('F Y') }} · Tagihan: <strong>Rp {{ number_format($iuranWajibNominal, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                </div>
                <div>
                    @if($iuranWajibPaidThisMonth)
                        <span class="badge badge-success" style="font-size:12px;padding:6px 14px;border-radius:100px; font-weight: 700;">LUNAS</span>
                    @else
                        <span class="badge badge-danger" style="font-size:12px;padding:6px 14px;border-radius:100px; font-weight: 700;">BELUM DIBAYAR</span>
                    @endif
                </div>
            </div>
            @if(!$iuranWajibPaidThisMonth)
                <div style="margin-top:14px;padding-top:12px;border-top:1px dashed var(--hairline-soft);font-size:12.5px;color:var(--muted);line-height:1.55;">
                    💡 Iuran wajib akan otomatis didebet oleh pengurus dari saldo <strong>Simpanan Sukarela</strong> Anda jika mencukupi. Pastikan saldo Anda mencukupi sebelum akhir bulan.
                </div>
            @endif
        </div>

        {{-- Savings Summary --}}
        <div class="card-3d reveal-left">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--hairline-soft);">
                <div>
                    <h3 style="font-size:16px;font-weight:800; color: var(--ink);">Ringkasan Simpanan</h3>
                    <p style="font-size:12.5px;color:var(--muted);margin-top:2px;">Posisi saldo per hari ini</p>
                </div>
                <a href="{{ route('member.savings') }}" style="font-size:13.5px;font-weight:700;color:var(--primary);" class="animated-link">Lihat Detail →</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;margin-bottom:20px;">
                @php
                    $savingItems = [
                        ['label'=>'Simpanan Pokok','val'=>$savingsBalances['pokok'],'color'=>'var(--info)','icon'=>'🔒'],
                        ['label'=>'Simpanan Wajib','val'=>$savingsBalances['wajib'],'color'=>'var(--warning)','icon'=>'📅'],
                        ['label'=>'Simpanan Sukarela','val'=>$savingsBalances['sukarela'],'color'=>'var(--success)','icon'=>'💸'],
                    ];
                @endphp
                @foreach($savingItems as $item)
                    <div class="saving-capsule">
                        <div style="font-size:24px;margin-bottom:8px;">{{ $item['icon'] }}</div>
                        <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">{{ $item['label'] }}</div>
                        <div style="font-size:17px;font-weight:800;color:{{ $item['color'] }};">Rp {{ number_format($item['val'], 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;background:var(--surface-soft);border-radius:12px;padding:16px 20px; border: 1px solid var(--hairline-soft);">
                <div>
                    <div style="font-size:12.5px;color:var(--muted);margin-bottom:2px;">Total Saldo Tabungan</div>
                    <div style="font-size:22px;font-weight:800;color:var(--ink);">Rp {{ number_format($savingsBalances['total'], 0, ',', '.') }}</div>
                </div>
                <a href="{{ route('member.savings') }}" class="btn-3d-primary" style="height: 36px; padding: 0 16px; border-radius: 100px; font-size: 12.5px;">Setor Sekarang</a>
            </div>
        </div>

        {{-- Active Loan --}}
        <div class="card-3d reveal-left delay-2">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--hairline-soft);">
                <h3 style="font-size:16px;font-weight:800; color: var(--ink);">Status Pinjaman</h3>
                <a href="{{ route('member.loans') }}" style="font-size:13.5px;font-weight:700;color:var(--primary);" class="animated-link">Kelola Pinjaman →</a>
            </div>
            @if($activeLoan)
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @php
                        $loanRows = [
                            ['Kode Pinjaman', $activeLoan->loan_code],
                            ['Nominal', 'Rp ' . number_format($activeLoan->amount_requested, 0, ',', '.')],
                            ['Bunga Flat', $activeLoan->interest_rate . '% / tahun'],
                        ];
                        $statusMap = [
                            'active'   => ['badge-success', '✅ Aktif'],
                            'approved' => ['badge-info',    '✔ Disetujui'],
                            'draft'    => ['badge-warning', '⏳ Menunggu'],
                            'rejected' => ['badge-danger',  '✕ Ditolak'],
                        ];
                        $statusCls = $statusMap[$activeLoan->status] ?? ['badge-neutral', $activeLoan->status];
                    @endphp
                    @foreach($loanRows as $row)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--hairline-soft);">
                            <span style="font-size:13.5px;color:var(--muted);">{{ $row[0] }}</span>
                            <strong style="font-size:14px; color: var(--ink);">{{ $row[1] }}</strong>
                        </div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;">
                        <span style="font-size:13.5px;color:var(--muted);">Status</span>
                        <span class="badge {{ $statusCls[0] }}" style="font-weight: 700;">{{ $statusCls[1] }}</span>
                    </div>
                </div>
            @else
                <div style="text-align:center;padding:28px 16px;">
                    <div style="font-size:40px;margin-bottom:12px; animation: emoji-bounce 2.5s infinite;">🏦</div>
                    <p style="font-size:14.5px;color:var(--muted);margin-bottom:16px;">Tidak ada pinjaman aktif saat ini.</p>
                    <a href="{{ route('member.loans') }}" class="btn-3d-primary" style="height: 38px; padding: 0 20px; border-radius: 100px; font-size: 13px;">Ajukan Pinjaman Mikro</a>
                </div>
            @endif
        </div>

    </div>

    {{-- RIGHT: Recent orders --}}
    <div class="sticky-rail">
        <div class="card-3d reveal-right">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--hairline-soft);">
                <h3 style="font-size:16px;font-weight:800; color: var(--ink);">Belanjaan Terakhir</h3>
                <a href="{{ route('member.orders') }}" style="font-size:13.5px;font-weight:700;color:var(--primary);" class="animated-link">Lihat Semua →</a>
            </div>
            @if($recentOrders->isEmpty())
                <div style="text-align:center;padding:32px 16px;">
                    <div style="font-size:40px;margin-bottom:12px; animation: emoji-bounce 2s infinite;">🛒</div>
                    <p style="font-size:14.5px;color:var(--muted);margin-bottom:16px;">Belum ada riwayat pemesanan.</p>
                    <a href="{{ route('catalog.index') }}" class="btn-3d-primary" style="height: 38px; padding: 0 20px; border-radius: 100px; font-size: 13px;">Mulai Belanja</a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($recentOrders as $order)
                        @php
                            $pstyle = ['paid'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger'];
                            $ptext  = ['paid'=>'Lunas','pending'=>'Belum Bayar','cancelled'=>'Dibatalkan'];
                        @endphp
                        <a href="{{ route('orders.show', $order->id) }}" style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--hairline-soft);transition:all var(--t-fast) var(--ease-out);border-radius:8px;" onmouseover="this.style.background='var(--surface-soft)';this.style.paddingLeft='8px';this.style.paddingRight='8px'" onmouseout="this.style.background='';this.style.paddingLeft='0';this.style.paddingRight='0'">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ $order->order_number }}</div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $order->created_at->format('d M Y') }} · {{ ucfirst($order->delivery_type) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:13.5px;font-weight:800; color: var(--ink);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                <span class="badge {{ $pstyle[$order->payment_status] ?? 'badge-neutral' }}" style="margin-top:4px; font-weight: 700;">{{ $ptext[$order->payment_status] ?? $order->payment_status }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

