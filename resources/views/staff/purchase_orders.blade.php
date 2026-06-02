@extends('layouts.admin')

@section('title', 'Procurement & Purchase Orders (PO) — KDKMP Digital')
@section('page-title', 'Procurement (PO)')

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

    .po-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .po-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }

    .po-form-card {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .po-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
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
        background: #ffffff;
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
    
    @keyframes emoji-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
</style>

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div class="reveal" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin: 0;">
            📦 Procurement &amp; <span style="color: var(--primary);">Purchase Orders</span>
        </h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">
            Pemesanan inventaris sembako grosir ke supplier dan estimasi margin laba ritel.
        </p>
    </div>
    <button onclick="document.getElementById('create-po-card').scrollIntoView({behavior: 'smooth'})" class="btn-3d-primary no-print" style="border-radius: 100px; width: auto; font-size: 13.5px; height: 38px; padding: 0 20px;">
        + Buat PO Baru
    </button>
</div>

<div class="split-layout">
    
    {{-- PO list table --}}
    <div class="main-column">
        <div class="po-card card-flush" style="overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface)); border-top-left-radius: var(--r-lg); border-top-right-radius: var(--r-lg);">
                <h3 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--ink); letter-spacing: -0.3px; display: flex; align-items: center; gap: 6px;">
                    <span style="animation: emoji-bounce 2s ease-in-out infinite;">📋</span> Daftar Permintaan Pembelian Grosir (PO)
                </h3>
            </div>

            @if($purchaseOrders->isEmpty())
                <div style="padding: 48px; text-align: center; color: var(--muted);">
                    Belum ada Purchase Order (PO) yang dibuat. Gunakan form di sebelah kanan untuk memulai.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>No. PO / Tanggal</th>
                                <th>Nama Produk</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: right;">Harga Beli</th>
                                <th style="text-align: right;">Total Modal</th>
                                <th style="text-align: center;">Est. Margin</th>
                                <th>Status</th>
                                <th style="text-align: center;" class="no-print">Aksi Staf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                                @php
                                    // Calculate estimated profit margin percentage based on non-member selling price
                                    $marginAmount = $po->selling_price_non_member - $po->cost_price;
                                    $marginPct = $po->selling_price_non_member > 0 ? ($marginAmount / $po->selling_price_non_member) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ink);">{{ $po->po_number }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">{{ $po->created_at->format('d M Y H:i') }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ink);">{{ $po->product->name }}</div>
                                        <span style="font-size: 11px; color: var(--muted); background: var(--surface-soft); padding: 2px 6px; border-radius: var(--r-full); border: 1px solid var(--hairline-soft);">Jual: Rp {{ number_format($po->selling_price_non_member, 0, ',', '.') }}</span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; color: var(--ink);">
                                        {{ $po->quantity }} <span style="font-size: 11px; color: var(--muted); font-weight: 500;">{{ $po->product->unit }}</span>
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: var(--body);">Rp {{ number_format($po->cost_price, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 800; color: var(--primary);">
                                        Rp {{ number_format($po->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 800; {{ $marginPct > 15 ? 'color: var(--success);' : 'color: var(--warning);' }}">
                                            {{ number_format($marginPct, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @if($po->status === 'received')
                                            <span class="badge badge-success" style="font-weight: 700;">RECEIVED</span>
                                        @elseif($po->status === 'ordered')
                                            <span class="badge badge-info" style="font-weight: 700;">ORDERED</span>
                                        @elseif($po->status === 'draft')
                                            <span class="badge badge-neutral" style="font-weight: 700;">DRAFT</span>
                                        @else
                                            <span class="badge badge-danger" style="font-weight: 700;">CANCELLED</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;" class="no-print">
                                        <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                            @if($po->status === 'draft')
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'ordered']) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn-3d-primary" style="height: 28px; font-size: 11px; padding: 0 12px; border-radius: 100px; background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; box-shadow: 0 4px 10px rgba(59,130,246,0.15), inset 0 1px 0 rgba(255,255,255,0.2) !important;">
                                                        Order
                                                    </button>
                                                </form>
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'cancelled']) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn-3d-secondary" style="height: 28px; font-size: 11px; padding: 0 10px; border-radius: 100px; color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important;">
                                                        ✕
                                                    </button>
                                                </form>
                                            @elseif($po->status === 'ordered')
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'received']) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn-3d-primary" style="height: 28px; font-size: 11px; padding: 0 12px; border-radius: 100px; background: linear-gradient(135deg, var(--success), #165c42) !important; box-shadow: 0 4px 10px rgba(16,185,129,0.15), inset 0 1px 0 rgba(255,255,255,0.2) !important;">
                                                        Diterima ✔
                                                    </button>
                                                </form>
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'cancelled']) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn-3d-secondary" style="height: 28px; font-size: 11px; padding: 0 10px; border-radius: 100px; color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important;">
                                                        ✕
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 12px; color: var(--muted); font-weight: 600;">-</span>
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
    </div>

    {{-- Create PO Form --}}
    <div class="sticky-rail" id="create-po-card">
        <div class="po-form-card">
            <h3 style="font-size: 16px; font-weight: 800; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin-bottom: 20px; color: var(--ink);">
                <span style="font-size: 18px; animation: emoji-bounce 2.5s infinite;">📦</span> Buat Purchase Order
            </h3>

            <form action="{{ route('staff.purchase-orders.store') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Menyimpan...';" style="margin: 0;">
                @csrf

                <div class="form-group">
                    <label for="product_id">Pilih Produk Gerai</label>
                    <select name="product_id" id="product_id" class="form-select" onchange="updateEstimatedPOPrices()" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" data-selling-price="{{ $prod->price_non_member }}" data-unit="{{ $prod->unit }}">
                                {{ $prod->name }} (Stok: {{ $prod->current_stock }} {{ $prod->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label for="quantity">Jumlah Unit Grosir (Quantity)</label>
                    <input type="number" name="quantity" id="quantity" class="text-input" min="1" placeholder="Misal: 50" oninput="calculateTotalPOCost()" required>
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label for="cost_price">Harga Beli Modal per Unit (Rupiah)</label>
                    <input type="number" name="cost_price" id="cost_price" class="text-input" min="1" placeholder="Misal: 10000" oninput="calculateTotalPOCost()" required>
                </div>

                <div style="background: var(--surface-soft); padding: 14px; border-radius: var(--r-sm); border: 1px solid var(--hairline-soft); margin-top: 20px; font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">Harga Jual Gerai:</span>
                        <strong id="po-sel-price" style="color: var(--ink);">Rp 0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted); font-weight: 500;">Proyeksi Margin Laba:</span>
                        <strong id="po-margin-pct" style="color: var(--success);">0%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--hairline-soft); padding-top: 8px; font-size: 15px; font-weight: 800; margin-top: 4px;">
                        <span>Total Modal PO:</span>
                        <span id="po-total-cost" style="color: var(--primary);">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn-3d-primary" style="margin-top: 20px; width: 100%; height: 44px; border-radius: var(--r-sm); font-size: 14px;">
                    Draft Purchase Order ➔
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    function updateEstimatedPOPrices() {
        const select = document.getElementById('product_id');
        const selectedOpt = select.options[select.selectedIndex];
        
        if (!selectedOpt || selectedOpt.value === "") {
            document.getElementById('po-sel-price').textContent = "Rp 0";
            return;
        }

        const sellingPrice = parseFloat(selectedOpt.dataset.sellingPrice);
        document.getElementById('po-sel-price').textContent = "Rp " + sellingPrice.toLocaleString('id-ID');

        calculateTotalPOCost();
    }

    function calculateTotalPOCost() {
        const select = document.getElementById('product_id');
        const selectedOpt = select.options[select.selectedIndex];
        const qty = parseFloat(document.getElementById('quantity').value || 0);
        const costPrice = parseFloat(document.getElementById('cost_price').value || 0);

        const totalCostNode = document.getElementById('po-total-cost');
        const marginPctNode = document.getElementById('po-margin-pct');

        const totalCost = qty * costPrice;
        totalCostNode.textContent = "Rp " + totalCost.toLocaleString('id-ID');

        if (selectedOpt && selectedOpt.value !== "" && costPrice > 0) {
            const sellingPrice = parseFloat(selectedOpt.dataset.sellingPrice);
            const margin = sellingPrice - costPrice;
            const pct = (margin / sellingPrice) * 100;

            marginPctNode.textContent = pct.toFixed(1) + "%";
            if (pct > 15) {
                marginPctNode.style.color = "var(--success)";
            } else if (pct > 0) {
                marginPctNode.style.color = "var(--warning)";
            } else {
                marginPctNode.style.color = "var(--danger)";
            }
        } else {
            marginPctNode.textContent = "0%";
            marginPctNode.style.color = "var(--success)";
        }
    }
</script>

@endsection
