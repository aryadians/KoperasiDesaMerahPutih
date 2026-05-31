@extends('layouts.app')
@section('title', 'Dashboard Anggota — KDKMP Digital')
@section('content')

{{-- HERO MEMBER CARD --}}
<div class="reveal" style="
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 55%, #0f3460 100%);
    border-radius: 20px; padding: 36px 40px; margin-bottom: 28px;
    position: relative; overflow: hidden; color: white;
    box-shadow: 0 16px 48px rgba(15,52,96,0.25);
">
    {{-- decorative circles --}}
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;right:80px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.03);pointer-events:none;"></div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;position:relative;z-index:1;">
        <div>
            <div class="pulse-ring" style="font-size:12px;font-weight:600;opacity:0.7;margin-bottom:10px;letter-spacing:0.3px;">Anggota Aktif</div>
            <h1 style="font-size:28px;font-weight:800;letter-spacing:-0.5px;margin-bottom:6px;">
                Halo, {{ $member->user->name }}! 👋
            </h1>
            <p style="opacity:0.7;font-size:14px;">
                No. Anggota: <strong style="opacity:1;">{{ $member->nomor_anggota }}</strong>
                &nbsp;·&nbsp; NIK: {{ $member->nik }}
            </p>
        </div>
        <div style="background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.15);border-radius:16px;padding:18px 28px;text-align:center;min-width:160px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;opacity:0.7;margin-bottom:6px;">Poin Loyalitas</div>
            <div style="font-size:28px;font-weight:800;letter-spacing:-0.5px;">⭐ {{ number_format($member->total_poin) }}</div>
            <div style="font-size:11px;opacity:0.6;margin-top:4px;">Poin Aktif</div>
        </div>
    </div>

    {{-- Quick action strip --}}
    <div style="display:flex;gap:10px;margin-top:28px;flex-wrap:wrap;position:relative;z-index:1;">
        <a href="{{ route('catalog.index') }}" style="display:inline-flex;align-items:center;gap:6px;background:white;color:#1a1a2e;font-size:13px;font-weight:700;padding:8px 18px;border-radius:100px;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            🛒 Belanja Sembako
        </a>
        <a href="{{ route('member.savings') }}" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.12);color:white;font-size:13px;font-weight:600;padding:8px 18px;border-radius:100px;border:1px solid rgba(255,255,255,0.2);transition:background 0.2s,transform 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)';this.style.transform=''">
            💰 Setor Tabungan
        </a>
        <a href="{{ route('member.loans') }}" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.12);color:white;font-size:13px;font-weight:600;padding:8px 18px;border-radius:100px;border:1px solid rgba(255,255,255,0.2);transition:background 0.2s,transform 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.12)';this.style.transform=''">
            🏦 Pinjaman Mikro
        </a>
    </div>
</div>

{{-- NAVIGATION CARDS --}}
<div class="dashboard-nav-grid reveal">
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
    <div class="main-column" style="display:flex;flex-direction:column;gap:20px;">

        {{-- Iuran Wajib Status Alert --}}
        <div class="card reveal-left" style="border-left: 5px solid {{ $iuranWajibPaidThisMonth ? 'var(--success)' : 'var(--danger)' }};">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="font-size:32px;animation: emoji-bounce 3s ease-in-out infinite;">{{ $iuranWajibPaidThisMonth ? '✅' : '📅' }}</div>
                    <div>
                        <h4 style="font-size:14px;font-weight:700;color:var(--ink);margin:0;">Status Iuran Wajib Bulanan</h4>
                        <p style="font-size:12px;color:var(--muted);margin-top:2px;">
                            Bulan {{ Carbon\Carbon::now()->translatedFormat('F Y') }} · Tagihan: <strong>Rp {{ number_format($iuranWajibNominal, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                </div>
                <div>
                    @if($iuranWajibPaidThisMonth)
                        <span class="badge badge-success" style="font-size:12px;padding:6px 14px;border-radius:100px;">LUNAS</span>
                    @else
                        <span class="badge badge-danger" style="font-size:12px;padding:6px 14px;border-radius:100px;">BELUM DIBAYAR</span>
                    @endif
                </div>
            </div>
            @if(!$iuranWajibPaidThisMonth)
                <div style="margin-top:14px;padding-top:12px;border-top:1px dashed var(--hairline-soft);font-size:12px;color:var(--muted);line-height:1.5;">
                    💡 Iuran wajib akan otomatis didebet oleh pengurus dari saldo <strong>Simpanan Sukarela</strong> Anda jika mencukupi. Pastikan saldo Anda mencukupi sebelum akhir bulan.
                </div>
            @endif
        </div>

        {{-- Savings Summary --}}
        <div class="card reveal-left">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--hairline-soft);">
                <div>
                    <h3 style="font-size:16px;font-weight:700;">Ringkasan Simpanan</h3>
                    <p style="font-size:12px;color:var(--muted);margin-top:2px;">Posisi saldo per hari ini</p>
                </div>
                <a href="{{ route('member.savings') }}" style="font-size:13px;font-weight:600;color:var(--primary);" class="animated-link">Lihat Detail →</a>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
                @php
                    $savingItems = [
                        ['label'=>'Simpanan Pokok','val'=>$savingsBalances['pokok'],'color'=>'var(--info)','icon'=>'🔒'],
                        ['label'=>'Simpanan Wajib','val'=>$savingsBalances['wajib'],'color'=>'var(--warning)','icon'=>'📅'],
                        ['label'=>'Simpanan Sukarela','val'=>$savingsBalances['sukarela'],'color'=>'var(--success)','icon'=>'💸'],
                    ];
                @endphp
                @foreach($savingItems as $item)
                    <div style="background:var(--surface);border-radius:12px;padding:16px;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                        <div style="font-size:20px;margin-bottom:8px;">{{ $item['icon'] }}</div>
                        <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:4px;">{{ $item['label'] }}</div>
                        <div style="font-size:16px;font-weight:800;color:{{ $item['color'] }};">Rp {{ number_format($item['val'], 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;background:var(--surface);border-radius:12px;padding:14px 18px;">
                <div>
                    <div style="font-size:12px;color:var(--muted);margin-bottom:2px;">Total Saldo Tabungan</div>
                    <div style="font-size:20px;font-weight:800;color:var(--ink);">Rp {{ number_format($savingsBalances['total'], 0, ',', '.') }}</div>
                </div>
                <a href="{{ route('member.savings') }}" class="btn btn-primary btn-sm btn-pill">Setor Sekarang</a>
            </div>
        </div>

        {{-- Active Loan --}}
        <div class="card reveal-left delay-2">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--hairline-soft);">
                <h3 style="font-size:16px;font-weight:700;">Status Pinjaman</h3>
                <a href="{{ route('member.loans') }}" style="font-size:13px;font-weight:600;color:var(--primary);" class="animated-link">Kelola Pinjaman →</a>
            </div>
            @if($activeLoan)
                <div style="display:flex;flex-direction:column;gap:10px;">
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
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--hairline-soft);">
                            <span style="font-size:13px;color:var(--muted);">{{ $row[0] }}</span>
                            <strong style="font-size:14px;">{{ $row[1] }}</strong>
                        </div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                        <span style="font-size:13px;color:var(--muted);">Status</span>
                        <span class="badge {{ $statusCls[0] }}">{{ $statusCls[1] }}</span>
                    </div>
                </div>
            @else
                <div style="text-align:center;padding:28px 16px;">
                    <div style="font-size:40px;margin-bottom:12px;">🏦</div>
                    <p style="font-size:14px;color:var(--muted);margin-bottom:16px;">Tidak ada pinjaman aktif saat ini.</p>
                    <a href="{{ route('member.loans') }}" class="btn btn-primary btn-sm btn-pill">Ajukan Pinjaman Mikro</a>
                </div>
            @endif
        </div>

    </div>

    {{-- RIGHT: Recent orders --}}
    <div class="sticky-rail">
        <div class="card reveal-right">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--hairline-soft);">
                <h3 style="font-size:16px;font-weight:700;">Belanjaan Terakhir</h3>
                <a href="{{ route('member.orders') }}" style="font-size:13px;font-weight:600;color:var(--primary);" class="animated-link">Lihat Semua →</a>
            </div>
            @if($recentOrders->isEmpty())
                <div style="text-align:center;padding:32px 16px;">
                    <div style="font-size:40px;margin-bottom:12px;">🛒</div>
                    <p style="font-size:14px;color:var(--muted);margin-bottom:16px;">Belum ada riwayat pemesanan.</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary btn-sm btn-pill">Mulai Belanja</a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($recentOrders as $order)
                        @php
                            $pstyle = ['paid'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger'];
                            $ptext  = ['paid'=>'Lunas','pending'=>'Belum Bayar','cancelled'=>'Dibatalkan'];
                        @endphp
                        <a href="{{ route('orders.show', $order->id) }}" style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--hairline-soft);transition:background 0.15s,padding 0.15s;border-radius:8px;" onmouseover="this.style.background='var(--surface)';this.style.padding='12px 8px'" onmouseout="this.style.background='';this.style.padding='12px 0'">
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--ink);">{{ $order->order_number }}</div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $order->created_at->format('d M Y') }} · {{ ucfirst($order->delivery_type) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:13px;font-weight:700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                <span class="badge {{ $pstyle[$order->payment_status] ?? 'badge-neutral' }}" style="margin-top:4px;">{{ $ptext[$order->payment_status] ?? $order->payment_status }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
