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
        border-radius: var(--r-lg);
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04), 
                    0 1px 2px rgba(0, 0, 0, 0.01), 
                    inset 0 1px 0 #ffffff;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-shrink: 0;
        min-width: 0;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .pos-search-panel .text-input {
        border-radius: var(--r-sm);
        border: 1.5px solid var(--hairline);
        background: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        transition: all var(--t-fast) var(--ease-out);
    }

    .pos-search-panel .text-input:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }

    .category-strip {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 4px 0 10px;
        scrollbar-width: none;
    }
    .category-strip::-webkit-scrollbar { display: none; }

    .category-strip .category-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        padding: 8px 16px;
        border-radius: var(--r-full);
        border: 1px solid rgba(0, 0, 0, 0.06);
        white-space: nowrap;
        cursor: pointer;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02), inset 0 1px 0 #fff;
        transition: all var(--t-fast) var(--ease-out);
    }
    .category-strip .category-tab:hover {
        color: var(--ink);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.04), inset 0 1px 0 #fff;
    }
    .category-strip .category-tab.active {
        color: white !important;
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        border-color: rgba(0,0,0,0.08) !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2), inset 0 1px 0 rgba(255,255,255,0.3) !important;
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
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        cursor: pointer;
        user-select: none;
        position: relative;
        height: 190px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02), inset 0 1px 0 rgba(255,255,255,0.8);
    }

    .pos-prod-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-muted);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06), inset 0 1px 0 rgba(255,255,255,0.9);
    }

    .pos-prod-img-wrap {
        width: 100%;
        height: 75px;
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
        padding: 10px 12px;
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
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 100px;
        width: fit-content;
        margin-top: 4px;
    }

    .pos-stock-in {
        background: #e6f7ed !important;
        color: var(--success) !important;
        border: 1px solid rgba(16, 185, 129, 0.15) !important;
    }

    .pos-stock-low {
        background: #fffbeb !important;
        color: var(--warning) !important;
        border: 1px solid rgba(245, 158, 11, 0.15) !important;
    }

    .pos-stock-out {
        background: #fff0f3 !important;
        color: var(--danger) !important;
        border: 1px solid rgba(239, 68, 68, 0.15) !important;
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
        border-radius: var(--r-lg);
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 12px 36px -12px rgba(0, 0, 0, 0.08), 
                    inset 0 1px 0 #ffffff;
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
        padding: 12px;
        border-radius: var(--r-md);
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all var(--t-fast) var(--ease-out);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.01), inset 0 1px 0 #ffffff;
    }

    .pos-cart-row:hover {
        border-color: rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.03), inset 0 1px 0 #ffffff;
    }

    /* Quantity controllers */
    .qty-btn {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #ffffff;
        color: var(--ink);
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--t-fast) var(--ease-out);
        box-shadow: 0 2px 4px rgba(0,0,0,0.03), inset 0 1px 0 #fff;
    }

    .qty-btn:hover {
        background: var(--ink);
        color: #ffffff;
        border-color: var(--ink);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05), inset 0 1px 0 #fff;
    }

    .qty-input {
        width: 44px;
        height: 24px;
        border-radius: var(--r-xs);
        border: 1.5px solid var(--hairline);
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
        padding: 18px 20px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        background: linear-gradient(180deg, #f8fafc, #f1f5f9);
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
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 100px;
        padding: 5px 0;
        font-size: 10px;
        font-weight: 700;
        color: var(--body);
        cursor: pointer;
        text-align: center;
        transition: all var(--t-fast) var(--ease-out);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02), inset 0 1px 0 #fff;
    }

    .denom-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary-muted);
        color: var(--primary);
        transform: translateY(-1.5px);
        box-shadow: 0 4px 8px rgba(225, 29, 72, 0.08), inset 0 1px 0 #fff;
    }

    /* 3D POS Checkout Button styling */
    .btn-pos-checkout {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 800;
        font-size: 13px;
        height: 42px;
        border-radius: 100px;
        width: 100%;
        border: 1px solid rgba(0,0,0,0.1) !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255,255,255,0.3) !important;
        cursor: pointer;
        transition: all var(--t-fast) var(--ease-out);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-pos-checkout:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(225, 29, 72, 0.28), inset 0 1px 0 rgba(255,255,255,0.4) !important;
    }
    .btn-pos-checkout:active:not(:disabled) {
        transform: translateY(0);
    }
    .btn-pos-checkout:disabled {
        background: #cbd5e1 !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
        cursor: not-allowed;
        box-shadow: none !important;
        transform: none !important;
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

    /* SweetAlert Glass Overlays */
    .swal-overlay {
        background-color: rgba(15, 23, 42, 0.4) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        transition: all var(--t-base) var(--ease-out) !important;
        opacity: 0;
        pointer-events: none;
    }
    .swal-overlay.active {
        opacity: 1;
        pointer-events: auto;
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

            <!-- Member NIK Lookup Terminal Panel (Ultra-Compact) -->
            <div style="padding: 8px 12px; border-bottom: 1px solid var(--hairline-soft); background: #fdfdfd; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; font-weight: 700; color: var(--muted); white-space: nowrap;">NIK:</span>
                    <input type="text" id="pos-member-nik" class="text-input" placeholder="Scan Kartu Anggota" style="height: 32px; font-size: 12px; font-weight: 600; padding: 0 10px; background: #ffffff; flex: 1;">
                    <button type="button" class="button-primary" onclick="lookupPOSMember()" style="height: 32px; padding: 0 12px; font-size: 11px; border-radius: var(--r-sm); width: auto;">Cek</button>
                </div>
                <div id="pos-member-result" style="font-size: 11px; margin-top: 6px; font-weight: 700; color: var(--success); display: none; background: var(--success-bg); padding: 6px 10px; border-radius: var(--r-xs); border: 1px solid var(--success-border);">
                    👤 <span id="pos-member-name">-</span>
                    <div style="margin-top: 4px; color: var(--body); font-weight: 600;" id="pos-member-balance-wrapper">
                        💳 Saldo Sukarela: <strong style="color: var(--primary);" id="pos-member-balance">Rp 0</strong>
                    </div>
                </div>
            </div>

            <!-- Cart Items Scrollable List -->
            <div class="pos-cart-scroll" style="flex: 1; overflow-y: auto;">
                <div style="text-align: center; color: var(--muted); font-size: 13px; padding: 32px 0;" id="pos-cart-empty">
                    <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;">🛒</div>
                    Keranjang kosong.<br>Scan barcode atau klik produk.
                </div>
                <div id="pos-cart-list" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- cart rows go here -->
                </div>
            </div>

            <!-- Pricing Summary & Checkout Pay (Ultra-Compact) -->
            <div class="pos-checkout-panel" style="padding: 12px 16px; border-top: 1px solid var(--hairline-soft); background: #f8fafc; flex-shrink: 0;">
                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 12px; margin-bottom: 8px; color: var(--body);">
                    <div class="pos-summary-row" style="margin-bottom: 2px;">
                        <span>Jumlah Barang</span>
                        <strong id="pos-total-items" style="color: var(--ink);">0 Barang</strong>
                    </div>
                    <div class="pos-summary-row" style="margin-bottom: 2px;">
                        <span>Potongan Member</span>
                        <strong id="pos-total-discount" style="color: var(--success); font-weight: 700;">- Rp 0</strong>
                    </div>
                    <div class="pos-summary-total" style="padding-top: 6px; margin-top: 4px; border-top: 1px dashed var(--hairline);">
                        <span>Total Bayar</span>
                        <strong style="font-size: 18px; color: var(--primary); line-height: 1;" id="pos-total-pay" data-value="0">Rp 0</strong>
                    </div>
                </div>

                <!-- Split Payment Input (Displays only when member is active) -->
                <div id="pos-split-payment-wrapper" style="background: #ffffff; padding: 10px; border-radius: var(--r-md); margin-bottom: 8px; border: 1px solid var(--hairline-soft); display: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 9px; font-weight: 800; color: var(--ink); letter-spacing: 0.5px;">POTONG SALDO SUKARELA</span>
                        <label style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: var(--primary); cursor: pointer;">
                            <input type="checkbox" id="pos-use-split-saving" onchange="toggleSplitSaving()" style="cursor: pointer;"> Gunakan Saldo
                        </label>
                    </div>
                    <div id="pos-split-amount-container" style="display: none;">
                        <div style="display: flex; gap: 6px; margin-bottom: 6px;">
                            <button type="button" class="denom-btn" onclick="fillMaxSplitSaving()" style="flex: 1; padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">Max Saldo</button>
                            <button type="button" class="denom-btn" onclick="fillExactSplitSaving()" style="flex: 1; padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">Pas Belanja</button>
                        </div>
                        <input type="number" id="pos-sukarela-pay-amount" class="text-input" placeholder="Nominal Saldo (Rp)" oninput="calculatePOSChange()" style="height: 32px; font-weight: 800; font-size: 13px; color: var(--ink); text-align: right; background: var(--surface-soft); padding: 0 10px; width: 100%; box-sizing: border-box;">
                        <div style="font-size: 10px; color: var(--muted); margin-top: 4px; text-align: right;" id="pos-split-remaining-label">
                            Sisa Cash Dibayar: Rp 0
                        </div>
                    </div>
                </div>

                <!-- Tunai & Kembalian Terminal Card -->
                <div style="background: #ffffff; padding: 8px 10px; border-radius: var(--r-md); margin-bottom: 8px; border: 1px solid var(--hairline-soft);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <span style="font-size: 9px; font-weight: 800; color: var(--ink); letter-spacing: 0.5px;">TUNAI DITERIMA</span>
                        <span style="font-size: 9px; font-weight: 600; color: var(--primary); cursor: pointer; background: var(--primary-light); padding: 1px 6px; border-radius: 100px;" onclick="posFillExactCash()">Uang Pas <span class="kbd-badge" style="font-size: 7px;">F3</span></span>
                    </div>
                    
                    <!-- Quick Cash Denominations panel -->
                    <div class="denom-btn-group" style="margin-bottom: 6px; gap: 4px; display: grid; grid-template-columns: repeat(5, 1fr);">
                        <button type="button" class="denom-btn" onclick="posFillCash(10000)" style="padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">10K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(20000)" style="padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">20K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(50000)" style="padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">50K</button>
                        <button type="button" class="denom-btn" onclick="posFillCash(100000)" style="padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">100K</button>
                        <button type="button" class="denom-btn" onclick="posFillExactCash()" style="padding: 4px 0; font-size: 10px; border-radius: var(--r-xs);">Exact</button>
                    </div>

                    <input type="number" id="pos-cash-received" class="text-input" placeholder="Rp 0" oninput="calculatePOSChange()" style="height: 32px; font-weight: 800; font-size: 14px; color: var(--ink); text-align: right; background: var(--surface-soft); padding: 0 10px; width: 100%; box-sizing: border-box;">
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 6px; font-size: 12px; font-weight: 700; border-top: 1px dashed var(--hairline-soft); padding-top: 4px;">
                        <span style="color: var(--muted); font-size: 10px;">KEMBALIAN</span>
                        <strong id="pos-cash-change" style="color: var(--success); font-size: 14px;">Rp 0</strong>
                    </div>
                </div>

                <button class="btn-pos-checkout" id="btn-pos-checkout" onclick="submitPOSCheckout()" disabled>
                    Selesaikan Bayar <span class="kbd-badge" style="font-size: 8px; color: white; background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.25);">F2</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     struk cetak receipt modal (Overlay)
     ============================================================ -->
<div class="swal-overlay" id="pos-receipt-overlay">
    <div class="swal-modal" style="max-width: 320px; text-align: left; padding: 24px; font-family: 'Courier New', Courier, monospace; color: #000; border-radius: var(--r-lg); background: #ffffff; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15), inset 0 1px 0 #ffffff; border: 1px solid rgba(0,0,0,0.06);" id="pos-receipt-box">
        <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 12px; margin-bottom: 12px;">
            <h4 style="font-weight: 800; font-size: 15px; margin-bottom: 2px;">KDKMP {{ strtoupper(auth()->user()->branch->code) }}</h4>
            <p style="font-size: 11px; margin: 0;">Gerai Sembako Koperasi Desa</p>
            <p style="font-size: 10px; margin: 0; color: #555;">{{ auth()->user()->branch->name }}, Indonesia</p>
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
            <div id="rec-split-rows" style="display: none; flex-direction: column; font-size: 10px; margin-top: 4px; border-top: 1px dotted #000; padding-top: 4px;">
                <div style="display: flex; justify-content: space-between; color: #333;">
                    <span>- Debet Saldo:</span>
                    <span id="rec-sukarela-part">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #333;">
                    <span>- Sisa Cash:</span>
                    <span id="rec-cash-part">Rp 0</span>
                </div>
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

        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 16px;" class="no-print">
            <div style="display: flex; gap: 8px;">
                <button class="btn btn-primary btn-full" onclick="window.print()" style="flex: 1; height: 36px; font-size: 12px; background: #000; border-radius: 4px;">🖨️ Cetak</button>
                <a id="btn-download-pdf-receipt" href="#" class="btn btn-secondary btn-full" style="flex: 1; height: 36px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; border-radius: 4px; border-color: var(--primary); color: var(--primary); text-decoration: none;" target="_blank">📥 Unduh PDF</a>
            </div>
            <button class="btn btn-ghost btn-full" onclick="closePOSReceipt()" style="height: 34px; font-size: 12px; border-color: #ccc; border-radius: 4px; color: #555; background: none; border: 1px solid #ccc;">Selesai (ESC)</button>
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
    <div class="swal-modal" style="max-width: 450px; text-align: center; padding: 24px; border-radius: var(--r-lg); background: #ffffff; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15), inset 0 1px 0 #ffffff; border: 1px solid rgba(0,0,0,0.06);" id="pos-camera-box">
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

    // --- Global Barcode Scanner Listener (Keyboard Emulation Interceptor) ---
    let barcodeBuffer = '';
    let lastKeyTime = 0;
    
    document.addEventListener('keypress', function(e) {
        // Exclude inputs where the user might actually be typing manually
        const activeEl = document.activeElement;
        const isInput = activeEl && (
            activeEl.tagName === 'INPUT' || 
            activeEl.tagName === 'TEXTAREA' || 
            activeEl.tagName === 'SELECT'
        );
        
        // If the focused element is NOT the search box, intercept inputs for barcode
        if (isInput && activeEl.id !== 'pos-search') {
            return;
        }

        const currentTime = new Date().getTime();
        
        // Barcode scanners type very rapidly (time gap usually less than 40ms)
        // If the gap is longer than 50ms, it is probably a human typing, so reset the buffer unless it's the search field
        if (currentTime - lastKeyTime > 50 && activeEl.id !== 'pos-search') {
            barcodeBuffer = '';
        }
        
        lastKeyTime = currentTime;

        // If it's a printable character and not Enter
        if (e.key !== 'Enter' && e.key.length === 1) {
            barcodeBuffer += e.key;
        }
    });

    document.addEventListener('keydown', function(e) {
        const activeEl = document.activeElement;
        
        // Intercept Enter key
        if (e.key === 'Enter') {
            const isInput = activeEl && (
                activeEl.tagName === 'INPUT' || 
                activeEl.tagName === 'TEXTAREA' || 
                activeEl.tagName === 'SELECT'
            );
            
            // If we have a buffered barcode and the active element is not another form field
            if (barcodeBuffer.length >= 3 && (!isInput || activeEl.id === 'pos-search')) {
                const scannedBarcode = barcodeBuffer;
                barcodeBuffer = ''; // Reset buffer
                
                // Audio feedback on scan
                playBeep();
                
                // Populate search field and scan
                const searchInput = document.getElementById('pos-search');
                if (searchInput) {
                    searchInput.value = scannedBarcode;
                    e.preventDefault(); // Stop default form submit
                    
                    // Trigger search
                    handleBarcodeScan({
                        key: 'Enter',
                        preventDefault: () => {},
                        target: searchInput
                    });
                }
            } else {
                barcodeBuffer = ''; // Reset on normal Enter
            }
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
            emptyMsg.style.display = 'block';
            list.style.display = 'none';
            
            countBadge.textContent = '0 Barang';
            discBadge.textContent = '- Rp 0';
            payBadge.textContent = 'Rp 0';
            payBadge.dataset.value = 0;
            checkoutBtn.disabled = true;
            document.getElementById('pos-cash-received').value = '';
            calculatePOSChange();
            return;
        }

        emptyMsg.style.display = 'none';
        list.style.display = 'flex';

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
            row.style.cssText = 'padding: 10px;';
            row.innerHTML = `
                <div style="flex: 1; display: flex; flex-direction: column; gap: 6px; min-width: 0; width: 100%;">
                    <!-- Top row: Product Name & Delete Button -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; width: 100%;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--ink); word-break: break-word; line-height: 1.3;" title="${item.name}">${item.name}</div>
                        <button onclick="removePCItem(${item.id})" style="border: none; background: none; color: var(--muted); font-weight: bold; cursor: pointer; padding: 2px 6px; font-size: 14px; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'" title="Hapus">✕</button>
                    </div>
                    
                    <!-- Bottom row: Price detail, Qty controllers, and Subtotal -->
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; width: 100%; margin-top: 2px;">
                        <div style="font-size: 11px; color: var(--muted); min-width: 90px; white-space: nowrap;">
                            Rp ${price.toLocaleString('id-ID')} / ${item.unit}
                        </div>
                        <div style="display: flex; align-items: center; gap: 3px;">
                            <button class="qty-btn" onclick="adjustPCQty(${item.id}, -1)">-</button>
                            <input type="number" class="qty-input" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="updatePCQtyDirect(${item.id}, this.value)" onkeydown="preventCartEnterSubmit(event)">
                            <button class="qty-btn" onclick="adjustPCQty(${item.id}, 1)">+</button>
                        </div>
                        <div style="font-weight: 800; font-size: 13px; color: var(--ink); text-align: right; min-width: 80px;">
                            Rp ${subtotal.toLocaleString('id-ID')}
                        </div>
                    </div>
                </div>
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
            document.getElementById('pos-split-payment-wrapper').style.display = 'none';
            document.getElementById('pos-use-split-saving').checked = false;
            document.getElementById('pos-sukarela-pay-amount').value = '';
            document.getElementById('pos-split-amount-container').style.display = 'none';
            renderPOSCart();
            return;
        }

        fetch(`/staff/pos/member/${nik}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                selectedMember = { name: data.name, nomor_anggota: data.nomor_anggota, nik: nik, sukarela_balance: data.sukarela_balance };
                nameNode.innerHTML = '👤 ' + data.name + ' (' + data.nomor_anggota + ') <span style="margin-left:4px;color:#10b981;">✓ Terverifikasi</span>';
                document.getElementById('pos-member-balance').textContent = 'Rp ' + data.sukarela_balance.toLocaleString('id-ID');
                resultNode.style.display = 'block';
                document.getElementById('pos-split-payment-wrapper').style.display = 'block';
                window.showSweetAlert('Anggota Ditemukan! 👤', 'Warga "' + data.name + '" terverifikasi. Diskon harga anggota diterapkan.', 'success');
            } else {
                selectedMember = null;
                resultNode.style.display = 'none';
                document.getElementById('pos-split-payment-wrapper').style.display = 'none';
                document.getElementById('pos-use-split-saving').checked = false;
                document.getElementById('pos-sukarela-pay-amount').value = '';
                document.getElementById('pos-split-amount-container').style.display = 'none';
                window.showSweetAlert('Gagal', data.message || 'Anggota tidak ditemukan.', 'error');
            }
            renderPOSCart();
        })
        .catch(() => {
            selectedMember = null;
            resultNode.style.display = 'none';
            document.getElementById('pos-split-payment-wrapper').style.display = 'none';
            document.getElementById('pos-use-split-saving').checked = false;
            document.getElementById('pos-sukarela-pay-amount').value = '';
            document.getElementById('pos-split-amount-container').style.display = 'none';
            window.showSweetAlert('Error', 'Terjadi kesalahan sistem saat mencari anggota.', 'error');
            renderPOSCart();
        });
    }

    // Helper functions for Split Payment
    function toggleSplitSaving() {
        const checkbox = document.getElementById('pos-use-split-saving');
        const container = document.getElementById('pos-split-amount-container');
        if (checkbox.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.getElementById('pos-sukarela-pay-amount').value = '';
        }
        calculatePOSChange();
    }

    function fillMaxSplitSaving() {
        if (!selectedMember || selectedMember.sukarela_balance === undefined) return;
        const total = parseFloat(document.getElementById('pos-total-pay').dataset.value || 0);
        const balance = parseFloat(selectedMember.sukarela_balance || 0);
        const maxSpend = Math.min(total, balance);
        document.getElementById('pos-sukarela-pay-amount').value = maxSpend;
        calculatePOSChange();
    }

    function fillExactSplitSaving() {
        fillMaxSplitSaving();
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
        
        let sukarelaPay = 0;
        const useSplit = document.getElementById('pos-use-split-saving')?.checked;
        if (useSplit) {
            sukarelaPay = parseFloat(document.getElementById('pos-sukarela-pay-amount').value || 0);
        }
        const remainingCashRequired = Math.max(0, total - sukarelaPay);
        
        document.getElementById('pos-cash-received').value = remainingCashRequired;
        calculatePOSChange();
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
        
        let sukarelaPay = 0;
        const useSplit = document.getElementById('pos-use-split-saving')?.checked;
        if (useSplit) {
            const balance = selectedMember ? parseFloat(selectedMember.sukarela_balance || 0) : 0;
            const enteredAmount = parseFloat(document.getElementById('pos-sukarela-pay-amount').value || 0);
            
            if (enteredAmount > balance) {
                document.getElementById('pos-sukarela-pay-amount').value = balance;
                sukarelaPay = balance;
            } else if (enteredAmount > total) {
                document.getElementById('pos-sukarela-pay-amount').value = total;
                sukarelaPay = total;
            } else {
                sukarelaPay = enteredAmount;
            }
        }
        
        const remainingCashRequired = Math.max(0, total - sukarelaPay);
        document.getElementById('pos-split-remaining-label').textContent = 'Sisa Cash Dibayar: Rp ' + remainingCashRequired.toLocaleString('id-ID');
        
        const changeNode = document.getElementById('pos-cash-change');

        if (cash >= remainingCashRequired) {
            changeNode.textContent = 'Rp ' + (cash - remainingCashRequired).toLocaleString('id-ID');
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
        
        const useSplit = document.getElementById('pos-use-split-saving')?.checked;
        const paySukarelaAmount = useSplit ? parseFloat(document.getElementById('pos-sukarela-pay-amount').value || 0) : 0;
        const remainingCashRequired = Math.max(0, total - paySukarelaAmount);

        if (total <= 0) return;
        if (cash < remainingCashRequired) {
            window.showSweetAlert('Uang Tunai Kurang', 'Masukkan jumlah uang tunai yang mencukupi untuk menyelesaikan sisa pembayaran cash.', 'warning');
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
                member_nik: selectedMember ? selectedMember.nik : null,
                pay_sukarela_amount: paySukarelaAmount
            })
        })
        .then(r => r.json())
        .then(data => {
            checkoutBtn.disabled = false;
            checkoutBtn.textContent = 'Selesaikan Bayar F2';

            if (data.success) {
                // Deduct stock levels locally in real-time
                Object.keys(posCart).forEach(id => {
                    const item = posCart[id];
                    const productCard = document.querySelector(`.pos-product-item[data-id="${item.id}"]`);
                    if (productCard) {
                        let currentStock = parseInt(productCard.dataset.stock) || 0;
                        let newStock = Math.max(0, currentStock - item.quantity);
                        
                        // Update stock in dataset
                        productCard.dataset.stock = newStock;
                        
                        // Update Stock Badge UI
                        const infoDiv = productCard.querySelector('.pos-prod-info > div');
                        if (infoDiv) {
                            const oldBadge = infoDiv.querySelector('.pos-stock-badge');
                            if (oldBadge) oldBadge.remove();
                            
                            const newBadge = document.createElement('span');
                            if (newStock <= 0) {
                                productCard.classList.add('out-of-stock');
                                newBadge.className = 'pos-stock-badge pos-stock-out';
                                newBadge.textContent = 'Habis';
                            } else if (newStock <= 5) {
                                productCard.classList.remove('out-of-stock');
                                newBadge.className = 'pos-stock-badge pos-stock-low';
                                newBadge.textContent = 'Kritis: ' + newStock + ' ' + productCard.dataset.unit;
                            } else {
                                productCard.classList.remove('out-of-stock');
                                newBadge.className = 'pos-stock-badge pos-stock-in';
                                newBadge.textContent = 'Stok: ' + newStock + ' ' + productCard.dataset.unit;
                            }
                            infoDiv.appendChild(newBadge);
                        }
                    }
                });

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
        document.getElementById('btn-download-pdf-receipt').href = '/staff/pos/receipt/' + order.id + '/pdf';
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

        // Check if split payment
        const recSplitRows = document.getElementById('rec-split-rows');
        let isSplit = false;
        let sukarelaAmount = 0.00;
        let cashPaidRequired = totalNet;
        
        if (order.payment_method && order.payment_method.startsWith('split:')) {
            isSplit = true;
            sukarelaAmount = parseFloat(order.payment_method.split(':')[1]);
            cashPaidRequired = Math.max(0, totalNet - sukarelaAmount);
        }

        if (isSplit) {
            recSplitRows.style.display = 'flex';
            document.getElementById('rec-sukarela-part').textContent = 'Rp ' + sukarelaAmount.toLocaleString('id-ID');
            document.getElementById('rec-cash-part').textContent = 'Rp ' + cashPaidRequired.toLocaleString('id-ID');
            
            document.getElementById('rec-cash').textContent = 'Rp ' + cashAmount.toLocaleString('id-ID');
            document.getElementById('rec-change').textContent = 'Rp ' + Math.max(0, cashAmount - cashPaidRequired).toLocaleString('id-ID');
        } else {
            recSplitRows.style.display = 'none';
            document.getElementById('rec-cash').textContent = 'Rp ' + cashAmount.toLocaleString('id-ID');
            document.getElementById('rec-change').textContent = 'Rp ' + Math.max(0, cashAmount - totalNet).toLocaleString('id-ID');
        }

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
        
        // Retrieve list of cameras. If permissions are already granted, we select the preferred camera.
        // Otherwise, we request to start with the default facing environment/user camera.
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 0) {
                let cameraId = devices[0].id;
                
                // For desktop/laptop, prefer built-in webcam if no back camera exists
                let foundBack = false;
                for (let device of devices) {
                    const label = (device.label || '').toLowerCase();
                    if (label.includes('back') || label.includes('environment') || label.includes('belakang') || label.includes('rear')) {
                        cameraId = device.id;
                        foundBack = true;
                        break;
                    }
                }
                
                if (!foundBack) {
                    for (let device of devices) {
                        const label = (device.label || '').toLowerCase();
                        if (label.includes('integrated') || label.includes('webcam') || label.includes('front') || label.includes('depan') || label.includes('camera')) {
                            cameraId = device.id;
                            break;
                        }
                    }
                }
                
                startScanningWithId(cameraId, config);
            } else {
                // Fallback to start scanning using facingMode constraints if getCameras returns empty
                startScanningWithConstraint({ facingMode: "environment" }, config);
            }
        }).catch(err => {
            console.warn("Gagal mendeteksi kamera, mencoba dengan constraint default", err);
            startScanningWithConstraint({ facingMode: "environment" }, config);
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
        ).then(() => {
            // Once scanning starts successfully, update dropdown list with friendly device labels
            updateCameraDropdown(cameraId);
        }).catch(err => {
            console.error("Gagal memutar kamera", err);
            window.showSweetAlert('Gagal Mengakses Kamera', 'Izin kamera ditolak atau sedang aktif di aplikasi lain.', 'error');
            closeCameraScanner();
        });
    }

    function startScanningWithConstraint(constraint, config) {
        html5QrCode.start(
            constraint, 
            config,
            (decodedText, decodedResult) => {
                playBeep();
                const searchInput = document.getElementById('pos-search');
                searchInput.value = decodedText;
                closeCameraScanner();
                handleBarcodeScan({
                    key: 'Enter',
                    preventDefault: () => {},
                    target: searchInput
                });
            },
            (errorMessage) => {}
        ).then(() => {
            updateCameraDropdown(null);
        }).catch(err => {
            console.error("Gagal memutar kamera dengan constraint", err);
            window.showSweetAlert('Gagal Mengakses Kamera', 'Izin kamera ditolak atau tidak terdeteksi.', 'error');
            closeCameraScanner();
        });
    }

    function updateCameraDropdown(activeCameraId) {
        Html5Qrcode.getCameras().then(devices => {
            const selector = document.getElementById('camera-select');
            selector.innerHTML = '';
            if (devices && devices.length > 0) {
                devices.forEach(device => {
                    const opt = document.createElement('option');
                    opt.value = device.id;
                    opt.text = device.label || `Kamera ${selector.length + 1}`;
                    if (device.id === activeCameraId || device.label === activeCameraId || (!activeCameraId && selector.length === 0)) {
                        opt.selected = true;
                    }
                    selector.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = "";
                opt.text = "Kamera Default";
                selector.appendChild(opt);
            }
        }).catch(err => {
            console.error("Gagal memperbarui daftar kamera:", err);
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
