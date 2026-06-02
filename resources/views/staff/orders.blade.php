@extends('layouts.admin')

@section('title', 'Kelola Pesanan Gerai — KDKMP')
@section('page-title', 'Pesanan Gerai')

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

    .orders-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all var(--t-base) var(--ease-bounce);
    }

    /* Floating bottom action bar */
    .bulk-action-bar-floating {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(17, 24, 39, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: var(--r-full);
        padding: 10px 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        z-index: 1000;
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.3),
                    0 1px 2px rgba(255, 255, 255, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        animation: slideUpFloating 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes slideUpFloating {
        from { transform: translateX(-50%) translateY(150%); opacity: 0; }
        to { transform: translateX(-50%) translateY(0); opacity: 1; }
    }
</style>

<div class="reveal" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Pesanan <span style="color: var(--primary);">Gerai</span></h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">Kelola riwayat belanja sembako warga dan pengiriman logistik desa.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button onclick="exportOrders('csv')" class="btn btn-sm btn-secondary btn-pill">📥 Export CSV</button>
        <button onclick="exportOrders('pdf')" class="btn btn-sm btn-secondary btn-pill">📄 Export PDF</button>
    </div>
</div>

<div class="orders-card" style="overflow: hidden;">
    @if($orders->isEmpty())
        <div style="padding: 32px; text-align: center; color: var(--muted);">
            Belum ada pesanan belanja masuk dari warga.
        </div>
    @else
        <div class="bulk-action-bar-floating" id="bulk-action-bar" style="display: none;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px; animation: emoji-bounce 2s ease-in-out infinite;">🛍️</span>
                <span style="font-size: 13px; font-weight: 700; color: white;">
                    <span id="selected-count">0</span> pesanan dipilih
                </span>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="exportOrders('csv')" class="btn-3d-secondary" style="font-size: 11px; height: 30px; padding: 0 12px; border-radius: 100px; color: white !important; background: rgba(255,255,255,0.15) !important; border-color: rgba(255,255,255,0.2) !important; box-shadow: none !important;">📥 Export CSV</button>
                <button onclick="exportOrders('pdf')" class="btn-3d-secondary" style="font-size: 11px; height: 30px; padding: 0 12px; border-radius: 100px; color: white !important; background: rgba(255,255,255,0.15) !important; border-color: rgba(255,255,255,0.2) !important; box-shadow: none !important;">📄 Export PDF</button>
            </div>
        </div>

        <div class="clean-table-container">
            <table class="clean-table" style="margin-top: 0;">
                <thead style="background: var(--surface);">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"></th>
                        <th>Nomor Pesanan</th>
                        <th>Warga / Anggota</th>
                        <th>Tanggal Pesan</th>
                        <th>Pengiriman</th>
                        <th>Total Belanja</th>
                        <th>Status Bayar</th>
                        <th style="text-align: center; width: 300px;">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="{{ $order->id }}" onchange="updateBulkActionBar()"></td>
                            <td style="font-weight: 700; color: var(--ink);">{{ $order->order_number }}</td>
                            <td>
                                <div style="font-weight: 700; color: var(--ink);">{{ $order->user->name }}</div>
                                <span style="font-size: 11px; color: var(--muted); background: var(--surface-strong); padding: 2px 8px; border-radius: var(--r-full); font-weight: 600;">{{ ucfirst($order->user->role) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge badge-neutral" style="font-weight: 600;">
                                    {{ $order->delivery_type }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success" style="font-weight: 700; border: 1px solid rgba(16,185,129,0.15); border-radius: var(--r-full);">LUNAS</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge badge-warning" style="font-weight: 700; border: 1px solid rgba(245,158,11,0.15); border-radius: var(--r-full);">PENDING</span>
                                @else
                                    <span class="badge badge-danger" style="font-weight: 700; border: 1px solid rgba(220,38,38,0.15); border-radius: var(--r-full);">DIBATALKAN</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn-3d-secondary" style="text-decoration: none; padding: 2px 10px; font-size: 11px; height: 28px; border-radius: 100px;">
                                        👁️ Detail
                                    </a>
                                    @if($order->payment_status === 'pending')
                                        <form action="{{ route('staff.orders.update', [$order->id, 'paid']) }}" method="POST" style="display: inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-3d-primary" style="background: linear-gradient(135deg, var(--success), #165c42) !important; box-shadow: 0 4px 10px rgba(16,185,129,0.15), inset 0 1px 0 rgba(255,255,255,0.2) !important; padding: 2px 10px; font-size: 11px; height: 28px; border-radius: 100px;">
                                                ✓ Lunas
                                            </button>
                                        </form>
                                        <form action="{{ route('staff.orders.update', [$order->id, 'cancelled']) }}" method="POST" style="display: inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-3d-secondary" style="color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important; padding: 2px 10px; font-size: 11px; height: 28px; border-radius: 100px;">
                                                ✕ Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    function toggleSelectAll(masterCb) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = masterCb.checked);
        updateBulkActionBar();
    }

    function updateBulkActionBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const bar = document.getElementById('bulk-action-bar');
        bar.style.display = checked.length > 0 ? 'flex' : 'none';
        document.getElementById('selected-count').textContent = checked.length;
    }

    function exportOrders(type) {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value).join(',');
        let url = `{{ route('orders.export') }}?type=${type}`;
        if (ids) url += `&ids=${ids}`;
        window.location.href = url;
    }
</script>
@endsection
