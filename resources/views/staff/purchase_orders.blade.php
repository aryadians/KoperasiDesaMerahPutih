@extends('layouts.admin')

@section('title', 'Procurement & Purchase Orders (PO) — KDKMP Digital')

@section('content')

{{-- ═══════════════════════ HEADER ═══════════════════════ --}}
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 32px; font-weight: 800; letter-spacing: -0.5px; color: var(--ink); margin-bottom: 6px;">
            📦 Procurement &amp; Purchase Orders (PO)
        </h1>
        <p style="color: var(--muted); font-size: 15px;">
            Pemesanan inventaris sembako grosir ke supplier dan penghitungan estimasi margin laba ritel.
        </p>
    </div>
    <button onclick="document.getElementById('create-po-card').scrollIntoView({behavior: 'smooth'})" class="btn btn-md btn-primary no-print" style="border-radius: 100px; width: auto; font-size: 14px;">
        + Buat PO Baru
    </button>
</div>

<div class="split-layout">
    
    {{-- PO list table --}}
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 700; padding: 20px; border-bottom: 1px solid var(--hairline-soft); margin: 0;">
                Daftar Permintaan Pembelian Grosir (PO)
            </h3>

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
                                        <div style="font-weight: 700;">{{ $po->po_number }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">{{ $po->created_at->format('d M Y H:i') }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--ink);">{{ $po->product->name }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">Jual: Rp {{ number_format($po->selling_price_non_member, 0, ',', '.') }}</span>
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ $po->quantity }} <span style="font-size: 11px; color: var(--muted); font-weight: 400;">{{ $po->product->unit }}</span>
                                    </td>
                                    <td style="text-align: right;">Rp {{ number_format($po->cost_price, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 700; color: var(--ink);">
                                        Rp {{ number_format($po->total_cost, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 700; {{ $marginPct > 15 ? 'color: var(--success);' : 'color: var(--warning);' }}">
                                            {{ number_format($marginPct, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            {{ $po->status === 'draft' ? 'badge-neutral' : '' }}
                                            {{ $po->status === 'ordered' ? 'badge-info' : '' }}
                                            {{ $po->status === 'received' ? 'badge-success' : '' }}
                                            {{ $po->status === 'cancelled' ? 'badge-danger' : '' }}
                                        ">
                                            {{ $po->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;" class="no-print">
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            @if($po->status === 'draft')
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'ordered']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button-primary" style="height: 28px; font-size: 11px; padding: 0 10px; width: auto; background: var(--info); border-color: var(--info);">
                                                        Order
                                                    </button>
                                                </form>
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'cancelled']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button-secondary" style="height: 28px; font-size: 11px; padding: 0 10px; width: auto; border-color: var(--danger); color: var(--danger);">
                                                        ✕
                                                    </button>
                                                </form>
                                            @elseif($po->status === 'ordered')
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'received']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button-primary" style="height: 28px; font-size: 11px; padding: 0 10px; width: auto; background: var(--success); border-color: var(--success);">
                                                        Diterima ✔
                                                    </button>
                                                </form>
                                                <form action="{{ route('staff.purchase-orders.update-status', [$po->id, 'cancelled']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button-secondary" style="height: 28px; font-size: 11px; padding: 0 10px; width: auto; border-color: var(--danger); color: var(--danger);">
                                                        ✕
                                                    </button>
                                                </form>
                                            @else
                                                <span style="font-size: 12px; color: var(--muted);">-</span>
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
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 700; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin: 0;">
                Buat Purchase Order Grosir
            </h3>

            <form action="{{ route('staff.purchase-orders.store') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Menyimpan...';">
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

                <div style="background: var(--surface); padding: 14px; border-radius: var(--r-md); margin-top: 20px; font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">Harga Jual Gerai:</span>
                        <strong id="po-sel-price">Rp 0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--muted);">Proyeksi Margin Laba:</span>
                        <strong id="po-margin-pct" style="color: var(--success);">0%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--hairline); padding-top: 8px; font-size: 15px; font-weight: 700; margin-top: 4px;">
                        <span>Total Modal PO:</span>
                        <span id="po-total-cost" style="color: var(--primary);">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="button-primary" style="margin-top: 20px; height: 44px; border-radius: 100px;">
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
