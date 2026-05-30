@extends('layouts.admin')

@section('title', 'KDKMP — POS Kasir Gerai Offline')

@section('content')

<!-- POS Page Layout Stylesheet -->
<style>
    /* Full Viewport POS Layout Redesign */
    .pos-layout {
        display: flex;
        gap: 20px;
        height: calc(100vh - 190px);
        overflow: hidden;
        position: relative;
        z-index: 10;
        box-sizing: border-box;
    }

    .pos-catalog-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
        height: 100%;
    }

    .pos-cart-column {
        width: 380px;
        flex-shrink: 0;
        height: 100%;
    }

    /* Search and Filter Panel */
    .pos-search-panel {
        background: #ffffff;
        border-radius: var(--r-md);
        border: 1px solid var(--hairline-soft);
        box-shadow: var(--shadow-sm);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-shrink: 0;
        min-width: 0;
    }

    .category-strip {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    /* Lock body scroll on POS page for clean app-like experience */
    @media (min-width: 769px) {
        body {
            height: 100vh;
            overflow: hidden !important;
        }
        .admin-main {
            height: 100vh;
            overflow: hidden !important;
        }
        .admin-content {
            height: calc(100vh - 72px);
            overflow: hidden !important;
            display: flex;
            flex-direction: column;
            padding: 24px 32px 20px !important;
        }
        .pos-layout {
            flex: 1;
            min-height: 0;
            height: auto;
        }
    }

    /* Product Grid Scroll Area */
    .pos-products-scroll {
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
        padding-bottom: 20px;
    }

    .pos-products-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .pos-products-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .pos-products-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: var(--r-full);
    }
    .pos-products-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .pos-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 14px;
    }

    /* Custom POS Product Card */
    .pos-prod-card {
        background: #ffffff;
        border-radius: var(--r-md);
        border: 1px solid var(--hairline-soft);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        user-select: none;
        position: relative;
        height: 220px;
        box-shadow: var(--shadow-sm);
    }

    .pos-prod-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.04), 0 4px 6px rgba(255, 56, 92, 0.04);
    }

    .pos-prod-card:active {
        transform: scale(0.97) translateY(-2px);
    }

    .pos-prod-card.out-of-stock {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
        background: #fafafa;
    }

    .pos-prod-img-wrap {
        width: 100%;
        height: 100px;
        position: relative;
        overflow: hidden;
        background: var(--surface-strong);
    }

    .pos-prod-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .pos-prod-card:hover .pos-prod-img-wrap img {
        transform: scale(1.06);
    }

    .pos-prod-info {
        padding: 12px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pos-prod-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pos-stock-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 100px;
        width: fit-content;
        margin-top: 4px;
    }

    .pos-stock-in {
        background: #e6f7ed;
        color: var(--success);
    }

    .pos-stock-low {
        background: #fffbeb;
        color: var(--warning);
        font-weight: 700;
    }

    .pos-stock-out {
        background: #fff0f3;
        color: var(--danger);
    }

    .pos-prod-price {
        font-size: 14px;
        font-weight: 800;
        color: var(--primary);
    }

    .pos-prod-member-price {
        font-size: 10px;
        color: var(--success);
        font-weight: 600;
        margin-top: 2px;
    }

    /* Cart Panel Card */
    .pos-cart-panel {
        background: #ffffff;
        border-radius: var(--r-md);
        border: 1px solid var(--hairline-soft);
        box-shadow: var(--shadow-md);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .pos-cart-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--hairline-soft);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .pos-cart-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pos-cart-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: #fafbfd;
    }

    .pos-cart-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .pos-cart-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .pos-cart-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: var(--r-full);
    }

    /* Cart Row Item */
    .pos-cart-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        padding: 10px 12px;
        border-radius: var(--r-sm);
        border: 1px solid var(--hairline-soft);
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .pos-cart-row:hover {
        border-color: var(--hairline);
        box-shadow: var(--shadow-sm);
    }

    /* Quantity controllers */
    .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid var(--hairline);
        background: #ffffff;
        color: var(--ink);
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }

    .qty-btn:hover {
        background: var(--ink);
        color: #ffffff;
        border-color: var(--ink);
    }

    .qty-input {
        width: 44px;
        height: 24px;
        border-radius: var(--r-xs);
        border: 1px solid var(--hairline);
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        color: var(--ink);
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s;
        box-sizing: border-box;
    }
    
    .qty-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary-glow);
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
    }

    .qty-input {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    /* Checkout & Payment Area */
    .pos-checkout-panel {
        padding: 16px 20px;
        border-top: 1px solid var(--hairline-soft);
        background: #f8fafc;
        flex-shrink: 0;
    }

    .pos-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: var(--body);
        margin-bottom: 6px;
    }

    .pos-summary-total {
        font-size: 14px;
        font-weight: 700;
        color: var(--ink);
        border-top: 1px dashed var(--hairline);
        padding-top: 8px;
        margin-top: 6px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    /* Cash denominations buttons */
    .denom-btn-group {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
        margin-bottom: 10px;
    }

    .denom-btn {
        background: #ffffff;
        border: 1px solid var(--hairline);
        border-radius: var(--r-xs);
        padding: 6px 0;
        font-size: 11px;
        font-weight: 700;
        color: var(--body);
        cursor: pointer;
        text-align: center;
        transition: all 0.15s;
    }

    .denom-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary-muted);
        color: var(--primary);
    }

    /* Keyboard Shortcuts Guides */
    .kbd-badge {
        font-family: inherit;
        background: var(--surface-strong);
        border: 1px solid var(--hairline);
        border-radius: 4px;
        padding: 1px 5px;
        font-size: 9px;
        font-weight: 700;
        color: var(--muted);
        box-shadow: 0 1px 0 rgba(0,0,0,0.15);
    }

    @media (max-width: 768px) {
        .pos-layout {
            flex-direction: column;
            height: auto;
            overflow: visible;
        }
        .pos-cart-column {
            width: 100%;
            height: auto;
        }
        .pos-products-scroll {
            max-height: 400px;
        }
    }
</style>

<!-- POS Top Header Banner -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;" class="no-print">
    <div>
        <h1 style="font-size: 24px; font-weight: 800; color: var(--ink); display: flex; align-items: center; gap: 10px;">
            <span>🏪</span> POS Kasir Gerai Offline
        </h1>
        <p style="font-size: 13px; color: var(--muted); margin-top: 2px;">
            Kelola transaksi warga desa secara tatap muka dengan cepat. Gunakan pintasan keyboard <span class="kbd-badge">F1</span> s.d. <span class="kbd-badge">F4</span>.
        </p>
    </div>
    <div class="status-pill" style="background: var(--success-bg); color: var(--success); border: 1.5px solid var(--success-border); padding: 6px 14px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <span class="pulse-dot"></span>
        <span>Kasir: <strong>{{ auth()->user()->name }}</strong></span>
    </div>
</div>

<!-- Main POS Workspace Layout -->
<div class="pos-layout no-print">
    
    <!-- LEFT: Product Search and Catalog Grid -->
    <div class="pos-catalog-column">
        <!-- Search bar panel -->
        <div class="pos-search-panel">
            <div style="display: flex; gap: 12px; align-items: center; width: 100%;">
                <div style="flex: 1; position: relative; display: flex; gap: 8px;">
                    <div style="flex: 1; position: relative;">
                        <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted);" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="pos-search" class="text-input" placeholder="Scan Barcode / Cari nama barang (Tekan F1)" oninput="filterPOSProducts()" onkeydown="handleBarcodeScan(event)" style="height: 46px; padding-left: 44px; font-weight: 600; font-size: 14px;" autofocus autocomplete="off">
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="openCameraScanner()" style="height: 46px; width: 46px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: var(--r-md);" title="Scan Barcode menggunakan Kamera">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    </button>
                </div>
                <select id="pos-category" class="text-input" onchange="filterPOSProducts()" style="height: 46px; width: 200px; font-weight: 500; font-size: 14px; padding: 0 12px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="category-strip" style="margin: 0; padding-bottom: 0;">
                <button class="category-tab active" id="btn-cat-all" onclick="selectCategory('')">🏪 Semua</button>
                @foreach($categories as $cat)
                    <button class="category-tab" id="btn-cat-{{ $cat->id }}" onclick="selectCategory('{{ $cat->id }}')">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>

        <!-- Scrollable Product Catalog Grid -->
        <div class="pos-products-scroll">
            <div class="pos-products-grid" id="pos-products-grid">
                @foreach($products as $prod)
                    <div class="pos-prod-card @if($prod->current_stock <= 0) out-of-stock @endif pos-product-item" 
                         data-id="{{ $prod->id }}" 
                         data-name="{{ strtolower($prod->name) }}" 
                         data-category="{{ $prod->category_id }}"
                         data-barcode="{{ $prod->barcode }}" 
                         data-member-price="{{ $prod->price_member }}"
                         data-guest-price="{{ $prod->price_non_member }}"
                         data-stock="{{ $prod->current_stock }}"
                         data-unit="{{ $prod->unit }}"
                         onclick="addPCToCart(this)">
                        
                        <div class="pos-prod-img-wrap">
                            <img src="{{ $prod->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80' }}" alt="{{ $prod->name }}">
                            @if($prod->is_local_product)
                                <span class="local-badge" style="font-size: 8px; padding: 2px 6px; top: 8px; right: 8px; font-weight: 700;">🌾 Tani Lokal</span>
                            @endif
                        </div>
                        
                        <div class="pos-prod-info">
                            <div>
                                <div class="pos-prod-name" title="{{ $prod->name }}">{{ $prod->name }}</div>
                                @if($prod->barcode)
                                    <div style="font-size: 9px; color: var(--muted); font-family: monospace; margin: 2px 0 4px; display: flex; align-items: center; gap: 4px; user-select: text;">
                                        <span style="letter-spacing: -0.5px;">║▌║█║▌</span> <span>{{ $prod->barcode }}</span>
                                    </div>
                                @endif
                                @if($prod->current_stock <= 0)
                                    <span class="pos-stock-badge pos-stock-out">Habis</span>
                                @elseif($prod->current_stock <= 5)
                                    <span class="pos-stock-badge pos-stock-low">Kritis: {{ $prod->current_stock }} {{ $prod->unit }}</span>
                                @else
                                    <span class="pos-stock-badge pos-stock-in">Stok: {{ $prod->current_stock }} {{ $prod->unit }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 4px;">
                                <div class="pos-prod-price">Rp {{ number_format($prod->price_non_member, 0, ',', '.') }}</div>
                                <div class="pos-prod-member-price">Anggota: Rp {{ number_format($prod->price_member, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT: Cashier POS Cart Column -->
    <div class="pos-cart-column">
        <div class="pos-cart-panel">
            
            <!-- Cart Title Header -->
            <div class="pos-cart-header">
                <span class="pos-cart-title">
                    <span>🛒</span> Keranjang Kasir
                </span>
                <button class="btn btn-ghost btn-sm" onclick="clearPOSCart()" style="height: 28px; font-size: 11px; padding: 0 10px; border-color: var(--danger); color: var(--danger);">Reset <span class="kbd-badge" style="font-size: 8px;">F4</span></button>
            </div>

            <!-- Member NIK Lookup Terminal Panel -->
            <div style="padding: 16px 16px 8px; border-bottom: 1px solid var(--hairline-soft); background: #fdfdfd; flex-shrink: 0;">
                <div style="background: var(--surface-soft); padding: 12px; border-radius: var(--r-md); border: 1.5px solid var(--hairline-soft);">
                    <label class="field-label" style="font-size: 10px; font-weight: 800; margin-bottom: 6px; display: block; color: var(--muted); letter-spacing: 0.5px;">KARTU ANGGOTA (NIK)</label>
                    <div style="display: flex; gap: 6px;">
                        <input type="text" id="pos-member-nik" class="text-input" placeholder="Barcode NIK / Ketik manual" style="height: 38px; font-size: 13px; font-weight: 600; background: #ffffff;">
                        <button type="button" class="button-primary" onclick="lookupPOSMember()" style="height: 38px; width: 60px; padding: 0; font-size: 12px; border-radius: var(--r-sm);">Cek</button>
                    </div>
                    <div id="pos-member-result" style="font-size: 12px; margin-top: 8px; font-weight: 700; color: var(--success); display: none; background: var(--success-bg); padding: 6px 10px; border-radius: var(--r-xs); border: 1px solid var(--success-border);">
                        👤 <span id="pos-member-name">-</span>
                    </div>
                </div>
            </div>

            <!-- Cart Items Scrollable List -->
            <div class="pos-cart-scroll" id="pos-cart-list">
                <div style="text-align: center; color: var(--muted); font-size: 13px; padding: 32px 0;" id="pos-cart-empty">
                    <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;">🛒</div>
                    Keranjang kosong.<br>Scan barcode atau klik produk.
                </div>
            </div>

            <!-- Pricing Summary & Checkout Pay -->
            <div class="pos-checkout-panel">
                <div style="display: flex; flex-direction: column; gap: 4px; font-size: 13px; margin-bottom: 10px; color: var(--body);">
                    <div class="pos-summary-row">
                        <span>Jumlah Barang</span>
                        <strong id="pos-total-items" style="color: var(--ink);">0 Barang</strong>
                    </div>
                    <div class="pos-summary-row">
                        <span>Potongan Member</span>
                        <strong id="pos-total-discount" style="color: var(--success); font-weight: 700;">- Rp 0</strong>
                    </div>
                    <div class="pos-summary-total">
                        <span>Total Bayar</span>
                        <strong style="font-size: 20px; color: var(--primary); line-height: 1;" id="pos-total-pay">Rp 0</strong>
                    </div>
                </div>

                <!-- Tunai & Kembalian Terminal Card -->
                <div style="background: #ffffff; padding: 12px; border-radius: var(--r-md); margin-bottom: 12px; border: 1px solid var(--hairline-soft);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 10px; font-weight: 800; color: var(--ink); letter-spacing: 0.5px;">TUNAI DITERIMA</span>
                        <span style="font-size: 10px; font-weight: 600; color: var(--primary); cursor: pointer; background: var(--primary-light); padding: 2px 8px; border-radius: 100px;" onclick="posFillExactCash()">Uang Pas <span class="kbd-badge" style="font-size: 8px;">F3</span></span>
                    </div>
                    
                    <!-- Quick Cash Denominations panel -->
                    <div class="denom-btn-group">
                        <button type="button" class="denom-btn" onclick="posFillCash(10000)">10K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(20000)">20K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(50000)">50K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(100000)">100K</button>
                        <button type="button" class="denom-btn" onclick="posFillExactCash()">Exact</button>
                    </div>

                    <input type="number" id="pos-cash-received" class="text-input" placeholder="Rp 0" oninput="calculatePOSChange()" style="height: 38px; font-weight: 800; font-size: 16px; color: var(--ink); text-align: right; background: var(--surface-soft);">
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 13px; font-weight: 700; border-top: 1px dashed var(--hairline-soft); padding-top: 6px;">
                        <span style="color: var(--muted); font-size: 11px;">KEMBALIAN</span>
                        <strong id="pos-cash-change" style="color: var(--success); font-size: 16px;">Rp 0</strong>
                    </div>
                </div>

                <button class="button-primary btn-full" id="btn-pos-checkout" onclick="submitPOSCheckout()" style="height: 44px; border-radius: 100px; font-weight: 800; font-size: 14px; width: 100%; border: none;" disabled>
                    Selesaikan Bayar <span class="kbd-badge" style="font-size: 9px; color: white; background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.25);">F2</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     struk cetak receipt modal (Overlay)
     ============================================================ -->
<div class="swal-overlay" id="pos-receipt-overlay">
    <div class="swal-modal" style="max-width: 320px; text-align: left; padding: 20px; font-family: 'Courier New', Courier, monospace; color: #000; border-radius: 4px; background: white;" id="pos-receipt-box">
        <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 12px; margin-bottom: 12px;">
            <h4 style="font-weight: 800; font-size: 15px; margin-bottom: 2px;">KDKMP MERAH PUTIH</h4>
            <p style="font-size: 11px; margin: 0;">Gerai Sembako Koperasi Desa</p>
            <p style="font-size: 10px; margin: 0; color: #555;">Desa Merah Putih, Indonesia</p>
        </div>
        
        <div style="font-size: 11px; margin-bottom: 12px; line-height: 1.4;">
            <div>No Struk: <strong id="rec-number">ORD-XXXX</strong></div>
            <div>Tanggal : <span id="rec-date">{{ date('d-m-Y H:i') }}</span></div>
            <div>Kasir   : <span id="rec-cashier">{{ auth()->user()->name }}</span></div>
            <div>Warga   : <span id="rec-member">Umum / Guest</span></div>
        </div>

        <div style="border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px; font-size: 11px;" id="rec-items-list">
            <!-- items list injected here -->
        </div>

        <div style="font-size: 11px; line-height: 1.4; margin-bottom: 12px; border-bottom: 1px dashed #000; padding-bottom: 8px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Total Belanja:</span>
                <span id="rec-total-gross">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; color: #000;">
                <span>Potongan Member:</span>
                <span id="rec-discount">-Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 800;">
                <span>Total Netto:</span>
                <span id="rec-total-net">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 4px;">
                <span>Tunai / Bayar:</span>
                <span id="rec-cash">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kembalian:</span>
                <span id="rec-change">Rp 0</span>
            </div>
        </div>

        <div style="text-align: center; font-size: 10px; line-height: 1.4;">
            <p>Terima Kasih Atas Kunjungan Anda</p>
            <p>Sisa Hasil Usaha (SHU) koperasi desa dibagikan dari warga untuk warga.</p>
        </div>

        <div style="display: flex; gap: 8px; margin-top: 16px;" class="no-print">
            <button class="btn btn-primary btn-full" onclick="window.print()" style="flex: 1; height: 36px; font-size: 12px; background: #000; border-radius: 4px;">🖨️ Cetak</button>
            <button class="btn btn-secondary btn-full" onclick="closePOSReceipt()" style="flex: 1; height: 36px; font-size: 12px; border-color: #777; border-radius: 4px; color: #555;">Selesai (ESC)</button>
        </div>
    </div>
</div>

<style>
    /* Styling receipt printer printing mode */
    @media print {
        body * { visibility: hidden; }
        #pos-receipt-overlay, #pos-receipt-box, #pos-receipt-box * { visibility: visible; }
        #pos-receipt-overlay { position: absolute; inset: 0; background: white; display: block; opacity: 1; }
        #pos-receipt-box { border: none; box-shadow: none; width: 100%; max-width: 100%; padding: 0; margin: 0; }
        .no-print { display: none !important; }
    }
</style>

<!-- ============================================================
     Kamera Webcam Barcode Scanner Modal (Overlay)
     ============================================================ -->
<div class="swal-overlay" id="pos-camera-overlay">
    <div class="swal-modal" style="max-width: 450px; text-align: center; padding: 20px; border-radius: var(--r-md); background: white;" id="pos-camera-box">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--ink); margin-bottom: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <span>📷</span> Scan Barcode Kamera
        </h3>
        
        <!-- Video Stream Container -->
        <div id="camera-reader" style="width: 100%; height: auto; border-radius: var(--r-sm); overflow: hidden; background: #000; border: 1.5px solid var(--hairline); box-shadow: var(--shadow-sm);"></div>
        
        <!-- Camera selection dropdown -->
        <div style="margin-top: 14px; text-align: left;">
            <label class="field-label" style="font-size: 11px; font-weight: 700; color: var(--muted); margin-bottom: 4px; display: block;">PILIH KAMERA</label>
            <select id="camera-select" class="text-input" onchange="changeCamera(this.value)" style="height: 38px; font-size: 13px; padding: 0 10px;"></select>
        </div>
        
        <div style="margin-top: 18px; display: flex; gap: 8px;">
            <button class="btn btn-secondary btn-full" onclick="closeCameraScanner()" style="flex: 1; height: 38px; font-size: 13px; border-radius: var(--r-sm);">Tutup (ESC)</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let posCart = {}; // key: productId, value: { id, name, unit, memberPrice, guestPrice, quantity, maxStock }
    let selectedMember = null; // { name, nomor_anggota, nik }

    // --- Audio Feedback ---
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    const audioCtx = new AudioContext();
    function playBeep() {
        if(audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(800, audioCtx.currentTime); // High pitch beep
        gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.1);
    }

    // ── Keyboard Shortcuts ──
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F1') {
            e.preventDefault();
            document.getElementById('pos-search').focus();
        } else if (e.key === 'F2') {
            e.preventDefault();
            if(!document.getElementById('btn-pos-checkout').disabled) submitPOSCheckout();
        } else if (e.key === 'F3') {
            e.preventDefault();
            posFillExactCash();
        } else if (e.key === 'F4') {
            e.preventDefault();
            clearPOSCart();
        } else if (e.key === 'Escape') {
            closePOSReceipt();
        }
    });

    // ── Barcode Scanner Logic ──
    function handleBarcodeScan(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = e.target.value.trim();
            if (!val) return;
            
            // 1. Search for exact barcode match first
            const cards = document.querySelectorAll('.pos-product-item');
            let exactMatchCard = null;
            
            for (let card of cards) {
                if (card.dataset.barcode && card.dataset.barcode.trim() === val) {
                    exactMatchCard = card;
                    break;
                }
            }

            if (exactMatchCard) {
                addPCToCart(exactMatchCard);
                e.target.value = ''; // clear input
                filterPOSProducts(); // reset filters/search
                return;
            }

            // 2. Fallback: if no exact barcode match, look for exactly 1 visible item
            let matchedCard = null;
            let visibleCount = 0;

            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    visibleCount++;
                    matchedCard = card;
                }
            });

            // Auto-add if it uniquely filters down to 1 item
            if (visibleCount === 1 && matchedCard) {
                addPCToCart(matchedCard);
                e.target.value = ''; // clear for next scan
                filterPOSProducts(); // reset view
            }
        }
    }

    // ── Cart Add ──
    function addPCToCart(card) {
        playBeep(); // Audio feedback
        
        const id = parseInt(card.dataset.id);
        const name = card.dataset.name;
        const memberPrice = parseFloat(card.dataset.memberPrice);
        const guestPrice = parseFloat(card.dataset.guestPrice);
        const maxStock = parseInt(card.dataset.stock);
        const unit = card.dataset.unit;

        if (maxStock <= 0) {
            window.showSweetAlert('Stok Habis ⚠️', 'Barang ini tidak memiliki stok untuk diproses.', 'warning');
            return;
        }

        if (posCart[id]) {
            if (posCart[id].quantity >= maxStock) {
                window.showSweetAlert('Stok Tidak Cukup', 'Pembelian tidak dapat melebihi stok tersedia.', 'warning');
                return;
            }
            posCart[id].quantity++;
        } else {
            posCart[id] = { id, name, unit, memberPrice, guestPrice, quantity: 1, maxStock };
        }

        renderPOSCart();
        
        // Auto-focus search input for next barcode scan
        setTimeout(() => {
            const searchInput = document.getElementById('pos-search');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }, 50);
    }

    // ── Remove from POS Cart ──
    function removePCItem(id) {
        delete posCart[id];
        renderPOSCart();
        document.getElementById('pos-search').focus();
    }

    function adjustPCQty(id, delta) {
        if (!posCart[id]) return;
        const newQty = posCart[id].quantity + delta;
        if (newQty <= 0) {
            delete posCart[id];
        } else if (newQty > posCart[id].maxStock) {
            window.showSweetAlert('Stok Terbatas', 'Jumlah melebihi stok yang tersedia.', 'warning');
            return;
        } else {
            posCart[id].quantity = newQty;
        }
        renderPOSCart();
        document.getElementById('pos-search').focus();
    }

    // ── Direct Quantity Update ──
    function updatePCQtyDirect(id, val) {
        if (!posCart[id]) return;
        let qty = parseInt(val);
        if (isNaN(qty) || qty <= 0) {
            qty = 1;
        }
        if (qty > posCart[id].maxStock) {
            window.showSweetAlert('Stok Terbatas', 'Jumlah melebihi stok yang tersedia (' + posCart[id].maxStock + ').', 'warning');
            qty = posCart[id].maxStock;
        }
        posCart[id].quantity = qty;
        renderPOSCart();
    }

    // ── Prevent Cart Input Enter Form Submit ──
    function preventCartEnterSubmit(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.target.blur(); // Triggers updatePCQtyDirect via change event
            document.getElementById('pos-search').focus();
        }
    }

    // ── Clear POS Cart
    function clearPOSCart() {
        posCart = {};
        renderPOSCart();
        document.getElementById('pos-search').focus();
    }

    // ── POS Cart calculations & HTML rendering
    function renderPOSCart() {
        const list = document.getElementById('pos-cart-list');
        const emptyMsg = document.getElementById('pos-cart-empty');
        const countBadge = document.getElementById('pos-total-items');
        const discBadge = document.getElementById('pos-total-discount');
        const payBadge = document.getElementById('pos-total-pay');
        const checkoutBtn = document.getElementById('btn-pos-checkout');

        // Clear list
        list.innerHTML = '';

        const keys = Object.keys(posCart);
        if (keys.length === 0) {
            list.appendChild(emptyMsg);
            countBadge.textContent = '0 Barang';
            discBadge.textContent = '- Rp 0';
            payBadge.textContent = 'Rp 0';
            payBadge.dataset.value = 0;
            checkoutBtn.disabled = true;
            document.getElementById('pos-cash-received').value = '';
            calculatePOSChange();
            return;
        }

        let totalItems = 0;
        let totalGross = 0;
        let totalNet = 0;

        keys.forEach(key => {
            const item = posCart[key];
            const price = selectedMember ? item.memberPrice : item.guestPrice;
            const subtotal = price * item.quantity;

            totalItems += item.quantity;
            totalGross += item.guestPrice * item.quantity;
            totalNet += subtotal;

            const row = document.createElement('div');
            row.className = 'pos-cart-row';
            row.innerHTML = `
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--ink);">${item.name}</div>
                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                        Rp ${price.toLocaleString('id-ID')} / ${item.unit}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <button class="qty-btn" onclick="adjustPCQty(${item.id}, -1)">-</button>
                    <input type="number" class="qty-input" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="updatePCQtyDirect(${item.id}, this.value)" onkeydown="preventCartEnterSubmit(event)">
                    <button class="qty-btn" onclick="adjustPCQty(${item.id}, 1)">+</button>
                </div>
                <div style="font-weight: 800; font-size: 13px; min-width: 80px; text-align: right; color: var(--ink);">
                    Rp ${subtotal.toLocaleString('id-ID')}
                </div>
                <button onclick="removePCItem(${item.id})" style="border: none; background: none; color: var(--primary); font-weight: bold; cursor: pointer; padding: 4px; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary-dark)'" onmouseout="this.style.color='var(--primary)'" title="Hapus">✕</button>
            `;
            list.appendChild(row);
        });

        const totalDiscount = totalGross - totalNet;

        countBadge.textContent = totalItems + ' Barang';
        discBadge.textContent = '- Rp ' + totalDiscount.toLocaleString('id-ID');
        payBadge.textContent = 'Rp ' + totalNet.toLocaleString('id-ID');
        payBadge.dataset.value = totalNet; // save numeric total

        checkoutBtn.disabled = false;
        calculatePOSChange();
    }

    // ── Member NIK Lookup
    function lookupPOSMember() {
        const nik = document.getElementById('pos-member-nik').value.trim();
        const resultNode = document.getElementById('pos-member-result');
        const nameNode = document.getElementById('pos-member-name');

        if (!nik) {
            selectedMember = null;
            resultNode.style.display = 'none';
            renderPOSCart();
            return;
        }

        fetch(`/staff/pos/member/${nik}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                selectedMember = { name: data.name, nomor_anggota: data.nomor_anggota, nik: nik };
                nameNode.innerHTML = '👤 ' + data.name + ' (' + data.nomor_anggota + ') <span style="margin-left:4px;color:#10b981;">✓ Terverifikasi</span>';
                resultNode.style.display = 'block';
                window.showSweetAlert('Anggota Ditemukan! 👤', 'Warga "' + data.name + '" terverifikasi. Diskon harga anggota diterapkan.', 'success');
            } else {
                selectedMember = null;
                resultNode.style.display = 'none';
                window.showSweetAlert('Gagal', data.message || 'Anggota tidak ditemukan.', 'error');
            }
            renderPOSCart();
        })
        .catch(() => {
            selectedMember = null;
            resultNode.style.display = 'none';
            window.showSweetAlert('Error', 'Terjadi kesalahan sistem saat mencari anggota.', 'error');
            renderPOSCart();
        });
    }

    // ── Live Search filter
    function filterPOSProducts() {
        const search = document.getElementById('pos-search').value.toLowerCase();
        const category = document.getElementById('pos-category').value;
        const cards = document.querySelectorAll('#pos-products-grid .pos-product-item');

        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const catId = card.dataset.category;
            const matchesSearch = name.includes(search);
            const matchesCategory = !category || catId === category;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function selectCategory(catId) {
        document.querySelectorAll('.category-strip .category-tab').forEach(tab => tab.classList.remove('active'));
        if (catId === '') {
            document.getElementById('btn-cat-all').classList.add('active');
        } else {
            const btn = document.getElementById('btn-cat-' + catId);
            if (btn) btn.classList.add('active');
        }
        document.getElementById('pos-category').value = catId;
        filterPOSProducts();
    }

    // ── Fill exact cash amount
    function posFillExactCash() {
        const total = parseFloat(document.getElementById('pos-total-pay').dataset.value || 0);
        if (total > 0) {
            document.getElementById('pos-cash-received').value = total;
            calculatePOSChange();
        }
    }

    // ── Quick Fill custom cash amount
    function posFillCash(amount) {
        document.getElementById('pos-cash-received').value = amount;
        calculatePOSChange();
    }

    // ── Calculate Change (kembalian)
    function calculatePOSChange() {
        const total = parseFloat(document.getElementById('pos-total-pay').dataset.value || 0);
        const cash = parseFloat(document.getElementById('pos-cash-received').value || 0);
        const changeNode = document.getElementById('pos-cash-change');

        if (cash >= total) {
            changeNode.textContent = 'Rp ' + (cash - total).toLocaleString('id-ID');
            changeNode.style.color = 'var(--success)';
        } else {
            changeNode.textContent = 'Uang Kurang ⚠️';
            changeNode.style.color = 'var(--danger)';
        }
    }

    // ── Submit POS Checkout Transaction
    function submitPOSCheckout() {
        const total = parseFloat(document.getElementById('pos-total-pay').dataset.value || 0);
        const cash = parseFloat(document.getElementById('pos-cash-received').value || 0);

        if (total <= 0) return;
        if (cash < total) {
            window.showSweetAlert('Uang Tunai Kurang', 'Masukkan jumlah uang tunai yang mencukupi untuk menyelesaikan transaksi.', 'warning');
            return;
        }

        const items = [];
        Object.keys(posCart).forEach(key => {
            items.push({
                product_id: posCart[key].id,
                quantity: posCart[key].quantity
            });
        });

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const checkoutBtn = document.getElementById('btn-pos-checkout');
        checkoutBtn.disabled = true;
        checkoutBtn.textContent = '⏳ Memproses...';

        fetch("{{ route('staff.pos.checkout') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                items: items,
                member_nik: selectedMember ? selectedMember.nik : null
            })
        })
        .then(r => r.json())
        .then(data => {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = 'Selesaikan Bayar F2';

            if (data.success) {
                // Populate and Open Struk Receipt Modal
                openPOSReceipt(data.order, cash);
                
                // Clear cart state
                clearPOSCart();
                document.getElementById('pos-member-nik').value = '';
                lookupPOSMember(); // Reset member
            } else {
                window.showSweetAlert('Transaksi Gagal', data.message || 'Terjadi kesalahan saat memproses transaksi POS.', 'error');
            }
        })
        .catch(err => {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = 'Selesaikan Bayar F2';
            window.showSweetAlert('Error Sistem', 'Gagal menghubungi server.', 'error');
        });
    }

    // ── Receipt Struk Modal Handler
    function openPOSReceipt(order, cashAmount) {
        document.getElementById('rec-number').textContent = order.order_number;
        document.getElementById('rec-date').textContent = new Date(order.created_at).toLocaleString('id-ID');
        document.getElementById('rec-member').textContent = order.user.name + (selectedMember ? ' (' + order.user.role + ')' : ' (Guest/Umum)');

        const listNode = document.getElementById('rec-items-list');
        listNode.innerHTML = '';

        let totalGross = 0;
        let totalNet = 0;

        order.items.forEach(item => {
            const unitPrice = parseFloat(item.price_at_purchase);
            const subtotal = unitPrice * item.quantity;
            totalNet += subtotal;

            const div = document.createElement('div');
            div.style.cssText = 'margin-bottom: 6px;';
            div.innerHTML = `
                <div><strong>${item.product.name}</strong></div>
                <div style="display: flex; justify-content: space-between; font-size: 10px; color: #444;">
                    <span>${item.quantity} x Rp ${unitPrice.toLocaleString('id-ID')}</span>
                    <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
            `;
            listNode.appendChild(div);
        });
        
        document.getElementById('rec-total-gross').textContent = 'Rp ' + totalNet.toLocaleString('id-ID');
        document.getElementById('rec-discount').textContent = selectedMember ? 'Harga Member Koperasi' : 'Rp 0';
        document.getElementById('rec-total-net').textContent = 'Rp ' + totalNet.toLocaleString('id-ID');
        document.getElementById('rec-cash').textContent = 'Rp ' + cashAmount.toLocaleString('id-ID');
        document.getElementById('rec-change').textContent = 'Rp ' + (cashAmount - totalNet).toLocaleString('id-ID');

        // Show overlay modal
        document.getElementById('pos-receipt-overlay').classList.add('active');
    }

    // Close Modal
    function closePOSReceipt() {
        document.getElementById('pos-receipt-overlay').classList.remove('active');
    }

    // ── Real-time Stock Update (Echo) ──
    window.Echo.channel('inventory')
        .listen('ProductStockUpdated', (e) => {
            console.log('Stock updated:', e);
            const productCard = document.querySelector(`.pos-product-item[data-id="${e.id}"]`);
            if (productCard) {
                // Update stock in dataset
                productCard.dataset.stock = e.current_stock;
                
                // Update Stock Badge UI
                const infoDiv = productCard.querySelector('.pos-prod-info > div');
                if (infoDiv) {
                    const oldBadge = infoDiv.querySelector('.pos-stock-badge');
                    if (oldBadge) oldBadge.remove();
                    
                    const newBadge = document.createElement('span');
                    if (e.current_stock <= 0) {
                        productCard.classList.add('out-of-stock');
                        newBadge.className = 'pos-stock-badge pos-stock-out';
                        newBadge.textContent = 'Habis';
                    } else if (e.current_stock <= 5) {
                        productCard.classList.remove('out-of-stock');
                        newBadge.className = 'pos-stock-badge pos-stock-low';
                        newBadge.textContent = 'Kritis: ' + e.current_stock + ' ' + productCard.dataset.unit;
                    } else {
                        productCard.classList.remove('out-of-stock');
                        newBadge.className = 'pos-stock-badge pos-stock-in';
                        newBadge.textContent = 'Stok: ' + e.current_stock + ' ' + productCard.dataset.unit;
                    }
                    infoDiv.appendChild(newBadge);
                }
            }
        });

    // ============================================================
    // WEBCAM CAMERA BARCODE SCANNER ENGINE
    // ============================================================
    let html5QrCode = null;

    function openCameraScanner() {
        document.getElementById('pos-camera-overlay').classList.add('active');
        
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("camera-reader");
        }
        
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 150 }, // wide box for barcode shape
            aspectRatio: 1.777778
        };
        
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                let cameraId = devices[0].id;
                // Try to find the back camera or environment camera
                for (let device of devices) {
                    if (device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('environment') || device.label.toLowerCase().includes('belakang')) {
                        cameraId = device.id;
                        break;
                    }
                }
                
                // Populate cameras dropdown selector
                const selector = document.getElementById('camera-select');
                selector.innerHTML = '';
                devices.forEach(device => {
                    const opt = document.createElement('option');
                    opt.value = device.id;
                    opt.text = device.label || `Kamera ${selector.length + 1}`;
                    if (device.id === cameraId) opt.selected = true;
                    selector.appendChild(opt);
                });
                
                // Start scanning
                startScanningWithId(cameraId, config);
            } else {
                window.showSweetAlert('Kamera Tidak Terdeteksi', 'Tidak ada perangkat kamera yang terhubung.', 'error');
                closeCameraScanner();
            }
        }).catch(err => {
            console.error("Gagal mendeteksi kamera", err);
            window.showSweetAlert('Error Deteksi Kamera', 'Gagal mengakses izin perangkat kamera.', 'error');
            closeCameraScanner();
        });
    }

    function startScanningWithId(cameraId, config) {
        html5QrCode.start(
            cameraId, 
            config,
            (decodedText, decodedResult) => {
                playBeep(); // Audio feedback
                
                const searchInput = document.getElementById('pos-search');
                searchInput.value = decodedText;
                
                closeCameraScanner();
                
                // Trigger barcode lookup
                handleBarcodeScan({
                    key: 'Enter',
                    preventDefault: () => {},
                    target: searchInput
                });
            },
            (errorMessage) => {
                // Ignore scanner error frames to prevent console flood
            }
        ).catch(err => {
            console.error("Gagal memutar kamera", err);
            window.showSweetAlert('Gagal Mengakses Kamera', 'Izin kamera ditolak atau sedang aktif di aplikasi lain.', 'error');
            closeCameraScanner();
        });
    }

    function changeCamera(cameraId) {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                const config = { 
                    fps: 10, 
                    qrbox: { width: 250, height: 150 },
                    aspectRatio: 1.777778
                };
                startScanningWithId(cameraId, config);
            });
        }
    }

    function closeCameraScanner() {
        document.getElementById('pos-camera-overlay').classList.remove('active');
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => console.error("Gagal menghentikan kamera", err));
        }
        
        // Re-focus scanner search input
        setTimeout(() => {
            document.getElementById('pos-search').focus();
        }, 150);
    }

    // Escape key listener for closing camera modal
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeCameraScanner();
        }
    });
</script>
@endsection
