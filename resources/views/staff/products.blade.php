@extends('layouts.admin')

@section('title', 'Inventaris Toko Sembako - KDKMP')

@section('content')

<style>
    /* Responsive Split Layout & Table compact overrides */
    .split-layout {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }
    .sticky-rail {
        flex: 0 0 360px !important;
        position: sticky;
        top: 96px;
        height: fit-content;
    }
    .main-column {
        flex: 1;
        min-width: 0;
    }
    @media (max-width: 1280px) {
        .split-layout {
            flex-direction: column;
            gap: 24px;
        }
        .sticky-rail {
            position: static;
            flex: 1;
            width: 100%;
        }
    }
    /* Compact table typography and padding */
    table.clean-table th {
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 12px 14px !important;
        background: var(--surface-md);
        border-bottom: 1.5px solid var(--hairline);
    }
    table.clean-table td {
        padding: 12px 14px !important;
        vertical-align: middle;
    }
    
    /* 3D Glass Card for Form & Lists */
    .card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all var(--t-base) var(--ease-bounce);
    }
    
    /* Form Focus Styles */
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
    
    /* Dynamic floating bulk action dock */
    .bulk-action-bar-floating {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%) translateY(150%);
        background: rgba(17, 24, 39, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: var(--r-full);
        padding: 12px 28px;
        display: flex;
        align-items: center;
        gap: 20px;
        z-index: 1000;
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transition: transform 0.4s var(--ease-spring), opacity 0.4s ease;
        opacity: 0;
        pointer-events: none;
    }
    .bulk-action-bar-floating.active {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }
    .bulk-action-text {
        font-size: 13px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.9);
    }
    
    /* 3D Checkbox Toggle */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: var(--hairline-soft);
        border: 1.5px solid var(--hairline);
        border-radius: 24px;
        transition: 0.3s var(--ease-out);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 2.5px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s var(--ease-bounce);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2), inset 0 1px 0 white;
    }
    input:checked + .toggle-slider {
        background-color: var(--success-bg);
        border-color: var(--success-border);
    }
    input:checked + .toggle-slider:before {
        transform: translateX(20px);
        background-color: var(--success);
    }
    
    /* Inline Stock Opname Input */
    .inline-stock-input {
        border: 1.5px solid var(--hairline) !important;
        background: var(--surface) !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.04) !important;
        font-family: monospace;
        transition: all var(--t-fast) !important;
    }
    .inline-stock-input:focus {
        background: #ffffff !important;
        border-color: var(--primary) !important;
        box-shadow: 0 4px 10px var(--primary-glow), inset 0 1px 2px rgba(0,0,0,0.01) !important;
        transform: scale(1.08);
    }
    
    /* 3D button upgrades */
    .btn-3d {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05),
                    inset 0 1px 0 rgba(255,255,255,0.25);
        transition: all var(--t-fast) var(--ease-out);
    }
    .btn-3d:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08),
                    inset 0 1px 0 rgba(255,255,255,0.25);
    }
    .btn-3d:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Inventaris Gerai Sembako</h1>
        <p style="color: var(--muted); font-size: 14px; margin-top: 4px; font-family: var(--font);">📍 Kelola produk &amp; stok untuk <strong>{{ auth()->user()->branch->name }}</strong></p>
    </div>
    <a href="{{ route('staff.products.export') }}" class="btn btn-secondary btn-sm btn-3d" style="font-weight: 700; border-radius: 100px; padding: 0 20px; height: 36px; display: inline-flex; align-items: center; gap: 6px;" data-no-loading>
        📥 Export CSV
    </a>
</div>

<div class="split-layout">
    
    <!-- Left: Inventory List Table -->
    <div class="main-column">
        <div class="card card-flush" style="box-shadow: var(--shadow-sm);">
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: var(--surface);">
                <h3 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--ink); letter-spacing: -0.3px;">Daftar Semua Barang</h3>
            </div>

            <!-- Search & Filter Panel (Stock Opname optimization) -->
            <div style="padding: 12px 20px; background: var(--surface); border-bottom: 1px solid var(--hairline-soft); display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <form action="{{ route('staff.products') }}" method="GET" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap; margin: 0;">
                    <div style="position: relative; flex: 1; min-width: 200px;">
                        <input type="text" name="search" placeholder="Cari nama barang / barcode..." value="{{ request('search') }}" class="text-input" style="height: 36px; padding-left: 12px; font-size: 13px;">
                    </div>
                    <div style="width: 180px;">
                        <select name="category_id" class="form-select" style="height: 36px; font-size: 13px; padding: 0 10px; margin: 0;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="height: 36px; padding: 0 16px; font-size: 13px;">Filter 🔍</button>
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('staff.products') }}" class="btn btn-ghost btn-sm" style="height: 36px; display: inline-flex; align-items: center; font-size: 13px; color: var(--danger); border-color: var(--danger); text-decoration: none; padding: 0 12px;">Reset</a>
                    @endif
                </form>
            </div>
            
            @if($products->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--muted);">
                    Belum ada barang di inventaris. Gunakan form di sebelah kanan untuk menambahkan produk baru.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead style="background: var(--surface);">
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px;">
                                </th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th style="white-space: nowrap;">Harga Detail</th>
                                <th>Stok</th>
                                <th style="text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input type="checkbox" class="row-checkbox" value="{{ $product->id }}" onchange="updateBulkActionBar()" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=80&q=80' }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; border: 1px solid var(--hairline-soft); flex-shrink: 0; background: var(--surface);">
                                            <div style="min-width: 0;">
                                                <div style="font-weight: 700; color: var(--ink); line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px;" title="{{ $product->name }}">{{ $product->name }}</div>
                                                <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap;">
                                                    <span style="font-size: 10px; background: var(--surface-soft); padding: 1px 6px; border-radius: 4px; color: var(--muted); border: 1px solid var(--hairline-soft); font-weight: 600;">{{ $product->unit }}</span>
                                                    @if($product->barcode)
                                                        <span style="font-size: 10px; color: var(--muted); font-family: monospace; background: var(--surface-soft); padding: 1px 6px; border-radius: 4px; border: 1px solid var(--hairline-soft);">
                                                            📋 {{ $product->barcode }}
                                                        </span>
                                                    @endif
                                                    @if($product->is_local_product)
                                                        <span style="font-size: 9px; color: var(--success); background-color: var(--success-bg); padding: 1px 6px; border-radius: var(--r-full); font-weight: 700; border: 1px solid var(--success-border); white-space: nowrap;">🌾 Tani Lokal</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="background: var(--surface-md); padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 600; color: var(--body); white-space: nowrap;">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="font-size: 13px; font-weight: 700; color: var(--primary);">Rp {{ number_format($product->price_member, 0, ',', '.') }} <span style="font-size: 10px; color: var(--muted); font-weight: 500;">(Anggota)</span></div>
                                        <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">Rp {{ number_format($product->price_non_member, 0, ',', '.') }} <span style="font-size: 9px; color: var(--muted); font-weight: 500;">(Umum)</span></div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <input type="number" 
                                                   class="text-input inline-stock-input" 
                                                   value="{{ $product->current_stock }}" 
                                                   data-id="{{ $product->id }}" 
                                                   min="0"
                                                   style="width: 56px; height: 28px; padding: 2px 4px; font-size: 13px; font-weight: 800; text-align: center; margin: 0; box-sizing: border-box; border-radius: var(--r-sm);"
                                                   onchange="quickSaveStock({{ $product->id }}, this.value)">
                                            <span style="font-size: 12px; color: var(--muted); font-weight: 600; white-space: nowrap;">{{ $product->unit }}</span>
                                        </div>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button type="button" class="btn btn-secondary btn-sm" style="border-radius: 100px; padding: 0 12px; height: 28px; font-size: 12px; font-weight: 600;"
                                            data-product="{{ json_encode($product) }}"
                                            onclick="loadEditForm(this)">
                                            ✏️ Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Custom Basic Pagination -->
                @if($products->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid var(--hairline); display: flex; justify-content: space-between; align-items: center; background: var(--surface);">
                    <div style="font-size: 13px; color: var(--muted);">
                        Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if($products->onFirstPage())
                            <span class="btn btn-sm btn-ghost" style="opacity: 0.5; pointer-events: none;">&laquo; Prev</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="btn btn-sm btn-secondary">&laquo; Prev</a>
                        @endif

                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="btn btn-sm btn-secondary">Next &raquo;</a>
                        @else
                            <span class="btn btn-sm btn-ghost" style="opacity: 0.5; pointer-events: none;">Next &raquo;</span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Right: Create/Edit Form Drawer -->
    <div class="sticky-rail">
        <div class="card" id="form-panel" style="box-shadow: var(--shadow-lg);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 16px; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.3px; display: flex; align-items: center; gap: 8px;" id="panel-title">
                    <span style="font-size: 18px;">📦</span> Tambah Produk
                </h3>
                
                <!-- Dynamic Delete Button (Appears only on Edit) -->
                <form action="" method="POST" id="delete-form" style="display: none;" onsubmit="return confirm('Yakin menghapus produk ini?');" class="no-print">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm btn-pill btn-3d" style="height: 28px; padding: 0 12px; font-size: 11px; font-weight: 700;" title="Hapus Produk Ini">🗑️ Hapus</button>
                </form>
            </div>
            
            <form action="{{ route('staff.products.store') }}" method="POST" id="product-form">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="form-group">
                    <label for="name">Nama Barang</label>
                    <input type="text" name="name" id="form-name" class="text-input" placeholder="Contoh: Beras Pandan Wangi" required>
                </div>

                <div class="form-group">
                    <label for="barcode">Kode Barcode (Scan / Ketik Manual)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="barcode" id="form-barcode" class="text-input" placeholder="Contoh: 8991234567890" style="flex: 1; height: 44px;">
                        <button type="button" class="btn btn-secondary btn-3d" onclick="generateBarcodeField()" style="height: 44px; padding: 0 16px; font-size: 12px; font-weight: 700; white-space: nowrap; border-radius: var(--r-sm);">⚡ Generate</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select name="category_id" id="form-category-id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Singkat</label>
                    <input type="text" name="description" id="form-description" class="text-input" placeholder="Keterangan isi/berat bersih">
                </div>

                <div class="form-group">
                    <label for="form-image-file">Gambar Produk (File Upload / Base64)</label>
                    <input type="file" id="form-image-file" class="text-input" accept="image/*" onchange="convertImageToBase64(this)" style="padding-top: 8px; height: 44px;">
                    <input type="hidden" name="image_url" id="form-image-url">
                    <div id="image-preview-container" style="margin-top: 12px; display: none; text-align: center; background: var(--surface-md); padding: 12px; border-radius: var(--r-md); border: 1.5px dashed var(--hairline); box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);">
                        <img id="image-preview" src="" style="max-width: 100%; max-height: 120px; border-radius: var(--r-sm); border: 1px solid var(--hairline); object-fit: cover; box-shadow: var(--shadow-sm);">
                        <div style="margin-top: 8px;">
                            <button type="button" class="btn btn-ghost btn-sm btn-3d" onclick="clearPreviewImage()" style="color: var(--danger); font-size: 11px; padding: 4px 12px; border-color: var(--danger-border); background: var(--danger-bg); font-weight: 700; border-radius: 100px;">🗑️ Hapus Gambar</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="unit">Satuan Jual (UOM)</label>
                    <input type="text" name="unit" id="form-unit" class="text-input" placeholder="kg, pcs, liter, bungkus" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="form-group">
                        <label for="price_member" style="color: var(--primary);">Harga Anggota</label>
                        <input type="number" name="price_member" id="form-price-member" class="text-input" placeholder="Rp" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="price_non_member">Harga Umum</label>
                        <input type="number" name="price_non_member" id="form-price-non-member" class="text-input" placeholder="Rp" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="current_stock">Jumlah Stok Awal</label>
                    <input type="number" name="current_stock" id="form-current-stock" class="text-input" placeholder="Contoh: 100" min="0" required>
                </div>

                <div class="form-group" style="flex-direction: row; gap: 16px; align-items: center; margin: 20px 0; background: rgba(16, 185, 129, 0.03); padding: 14px 16px; border-radius: var(--r-md); border: 1.5px solid rgba(16, 185, 129, 0.15);">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_local_product" id="form-local">
                        <span class="toggle-slider"></span>
                    </label>
                    <label for="form-local" style="cursor: pointer; font-weight: 700; margin: 0; color: var(--success); text-transform: none; letter-spacing: normal; font-size: 13px;">🌾 Merupakan Komoditas Lokal Desa</label>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-lg btn-3d" id="form-submit-btn" style="height: 48px; border-radius: var(--r-sm); font-weight: 700; font-size: 15px;">Simpan Produk</button>
                <button type="button" class="btn btn-ghost btn-full btn-md btn-3d" id="form-cancel-btn" style="display: none; margin-top: 10px; height: 42px; border-radius: var(--r-sm); font-weight: 700; font-size: 13.5px;" onclick="resetForm()">
                    Batal / Form Baru
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Floating Bulk Action Bar -->
<div id="floating-bulk-bar" class="bulk-action-bar-floating no-print">
    <span class="bulk-action-text">⚡ <span id="selected-count-float">0</span> Item Terpilih</span>
    <form action="{{ route('staff.products.bulk-delete') }}" method="POST" style="margin: 0; display: flex; align-items: center; gap: 8px;">
        @csrf
        <input type="hidden" name="ids" id="bulk-ids-input-float">
        <button type="submit" class="btn btn-danger btn-sm btn-pill btn-3d" style="height: 36px; padding: 0 20px; font-weight: 700;" onclick="return confirm('Yakin ingin menghapus produk yang dipilih?')">
            🗑️ Hapus Massal
        </button>
        <button type="button" class="btn btn-secondary btn-sm btn-pill btn-3d" style="height: 36px; padding: 0 16px;" onclick="cancelBulkSelection()">
            Batal
        </button>
    </form>
</div>

<script>
    // --- Barcode Generator for Local Goods ---
    function generateBarcodeField() {
        let barcode = '899'; // Indonesia EAN prefix
        for (let i = 0; i < 9; i++) {
            barcode += Math.floor(Math.random() * 10);
        }
        // Calculate EAN-13 check digit
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            sum += parseInt(barcode[i]) * (i % 2 === 0 ? 1 : 3);
        }
        const checkDigit = (10 - (sum % 10)) % 10;
        barcode += checkDigit;
        
        document.getElementById('form-barcode').value = barcode;
    }

    // --- Edit Form Logic ---
    function loadEditForm(btn) {
        const product = JSON.parse(btn.getAttribute('data-product'));
        document.getElementById('panel-title').textContent = '✏️ Edit: ' + product.name.substring(0, 15) + '...';
        document.getElementById('form-submit-btn').textContent = 'Perbarui Produk';
        document.getElementById('form-cancel-btn').style.display = 'inline-flex';
        
        // Show delete button
        const deleteForm = document.getElementById('delete-form');
        deleteForm.action = `/staff/products/${product.id}/delete`;
        deleteForm.style.display = 'block';
        
        const form = document.getElementById('product-form');
        form.action = `/staff/products/${product.id}/update`;
        document.getElementById('form-method').value = 'POST'; // We use POST for laravel update here since Route supports it
        
        document.getElementById('form-name').value = product.name;
        document.getElementById('form-barcode').value = product.barcode || '';
        document.getElementById('form-category-id').value = product.category_id;
        document.getElementById('form-description').value = product.description || '';
        
        // Populate image preview
        const imageUrl = product.image_url || '';
        document.getElementById('form-image-url').value = imageUrl;
        const previewImg = document.getElementById('image-preview');
        const previewContainer = document.getElementById('image-preview-container');
        if (imageUrl) {
            previewImg.src = imageUrl;
            previewContainer.style.display = 'block';
        } else {
            previewImg.src = '';
            previewContainer.style.display = 'none';
        }
        document.getElementById('form-image-file').value = '';

        document.getElementById('form-unit').value = product.unit;
        document.getElementById('form-price-member').value = Math.round(product.price_member);
        document.getElementById('form-price-non-member').value = Math.round(product.price_non_member);
        document.getElementById('form-current-stock').value = product.current_stock;
        document.getElementById('form-local').checked = !!product.is_local_product;
        
        // Scroll slightly to the form panel on mobile
        document.getElementById('form-panel').scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.getElementById('panel-title').textContent = '📦 Tambah Produk';
        document.getElementById('form-submit-btn').textContent = 'Simpan Produk';
        document.getElementById('form-cancel-btn').style.display = 'none';
        document.getElementById('delete-form').style.display = 'none';
        
        const form = document.getElementById('product-form');
        form.action = "{{ route('staff.products.store') }}";
        form.reset();
        document.getElementById('form-barcode').value = '';
        
        clearPreviewImage();
    }

    // --- Base64 File upload helpers ---
    function convertImageToBase64(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64String = e.target.result;
                document.getElementById('form-image-url').value = base64String;
                
                const previewImg = document.getElementById('image-preview');
                const previewContainer = document.getElementById('image-preview-container');
                previewImg.src = base64String;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearPreviewImage() {
        document.getElementById('form-image-url').value = '';
        document.getElementById('form-image-file').value = '';
        document.getElementById('image-preview').src = '';
        document.getElementById('image-preview-container').style.display = 'none';
    }

    // --- AJAX Stock Opname Inline Quick Update ---
    function quickSaveStock(productId, value) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const input = document.querySelector(`.inline-stock-input[data-id="${productId}"]`);
        
        // Disable temporarily to prevent multiple inputs
        input.disabled = true;
        input.style.opacity = '0.5';

        fetch(`/staff/products/${productId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                current_stock: parseInt(value)
            })
        })
        .then(r => r.json())
        .then(data => {
            input.disabled = false;
            input.style.opacity = '1';
            
            if (data.success) {
                // Success visual flash effect
                const originalBg = input.style.backgroundColor;
                const originalBorder = input.style.borderColor;
                input.style.backgroundColor = '#e6f7ed';
                input.style.borderColor = 'var(--success)';
                setTimeout(() => {
                    input.style.backgroundColor = originalBg;
                    input.style.borderColor = originalBorder;
                }, 800);
            } else {
                window.showSweetAlert('Gagal', data.message || 'Gagal menyimpan stok.', 'error');
            }
        })
        .catch(err => {
            input.disabled = false;
            input.style.opacity = '1';
            window.showSweetAlert('Error Jaringan', 'Terjadi kesalahan jaringan saat memperbarui stok.', 'error');
        });
    }

    // --- Bulk Selection Logic ---
    function toggleSelectAll(masterCb) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCb.checked);
        updateBulkActionBar();
    }

    function updateBulkActionBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        const bulkBar = document.getElementById('floating-bulk-bar');
        
        if (count > 0) {
            bulkBar.classList.add('active');
            document.getElementById('selected-count-float').textContent = count;
            
            // Build comma-separated IDs
            const ids = Array.from(checked).map(cb => cb.value).join(',');
            document.getElementById('bulk-ids-input-float').value = ids;
        } else {
            bulkBar.classList.remove('active');
            document.getElementById('select-all').checked = false;
        }
    }

    function cancelBulkSelection() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateBulkActionBar();
    }
</script>
@endsection
