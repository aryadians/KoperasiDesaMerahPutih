@extends('layouts.admin')

@section('title', 'Inventaris Toko Sembako - KDKMP')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
    <h1 style="font-size: 28px; font-weight: 600; color: var(--ink); margin: 0;">Inventaris Gerai Sembako</h1>
    <a href="{{ route('staff.products.export') }}" class="btn btn-secondary btn-sm" style="font-weight: 600;" data-no-loading>
        📥 Export CSV
    </a>
</div>

<div class="split-layout">
    
    <!-- Left: Inventory List Table -->
    <div class="main-column">
        <div class="card card-flush" style="box-shadow: var(--shadow-sm);">
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline); background: var(--surface-md);">
                <h3 style="font-size: 18px; font-weight: 600; margin: 0; color: var(--ink);">Daftar Semua Barang</h3>
                
                <!-- Hidden Bulk Action Bar -->
                <form id="bulk-action-form" action="{{ route('staff.products.bulk-delete') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="ids" id="bulk-ids-input">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 13px; font-weight: 600; color: var(--primary-dark);">
                            <span id="selected-count">0</span> item terpilih
                        </span>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk yang dipilih?')">
                            Hapus Massal
                        </button>
                    </div>
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
                                <th>Harga Anggota</th>
                                <th>Harga Umum</th>
                                <th>Stok</th>
                                <th style="text-align: center; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" class="row-checkbox" value="{{ $product->id }}" onchange="updateBulkActionBar()" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--ink);">{{ $product->name }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">Unit: {{ $product->unit }}</span>
                                        @if($product->is_local_product)
                                            <span style="font-size: 10px; color: var(--success); background-color: var(--success-bg); padding: 2px 6px; border-radius: var(--r-full); font-weight: 600; margin-left: 6px;">🌾 Tani Lokal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="background: var(--surface-md); padding: 2px 8px; border-radius: var(--r-full); font-size: 12px; color: var(--body);">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td><strong style="color: var(--primary);">Rp {{ number_format($product->price_member, 0, ',', '.') }}</strong></td>
                                    <td>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</td>
                                    <td>
                                        @if($product->current_stock <= 5)
                                            <span style="color: var(--danger); font-weight: 700; background: var(--danger-bg); padding: 2px 8px; border-radius: var(--r-full); font-size: 12px;">
                                                {{ $product->current_stock }} {{ $product->unit }}
                                            </span>
                                        @else
                                            <span style="color: var(--success); font-weight: 700; font-size: 14px;">
                                                {{ $product->current_stock }} <span style="font-size: 12px; color: var(--muted); font-weight: 500;">{{ $product->unit }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; gap: 6px; justify-content: center;">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                data-product="{{ json_encode($product) }}"
                                                onclick="loadEditForm(this)">
                                                ✏️ Edit
                                            </button>
                                        </div>
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
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--hairline); padding-bottom: 16px; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;" id="panel-title">📦 Tambah Produk</h3>
                
                <!-- Dynamic Delete Button (Appears only on Edit) -->
                <form action="" method="POST" id="delete-form" style="display: none;" onsubmit="return confirm('Yakin menghapus produk ini?');">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Produk Ini">🗑️ Hapus</button>
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
                    <label for="form-image-url">URL Gambar Produk (dari internet)</label>
                    <input type="url" name="image_url" id="form-image-url" class="text-input" placeholder="https://images.unsplash.com/photo-...">
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

                <div class="form-group" style="flex-direction: row; gap: 12px; align-items: center; margin: 24px 0; background: var(--surface); padding: 12px 16px; border-radius: var(--r-md); border: 1px solid var(--hairline);">
                    <input type="checkbox" name="is_local_product" id="form-local" style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--success);">
                    <label for="form-local" style="cursor: pointer; font-weight: 600; margin: 0; color: var(--success);">Merupakan Komoditas Lokal Desa</label>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-lg" id="form-submit-btn">Simpan Produk</button>
                <button type="button" class="btn btn-ghost btn-full btn-md" id="form-cancel-btn" style="display: none; margin-top: 10px;" onclick="resetForm()">
                    Batal / Form Baru
                </button>
            </form>
        </div>
    </div>

</div>

<script>
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
        document.getElementById('form-category-id').value = product.category_id;
        document.getElementById('form-description').value = product.description || '';
        document.getElementById('form-image-url').value = product.image_url || '';
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
        const bulkBar = document.getElementById('bulk-action-form');
        
        if (count > 0) {
            bulkBar.style.display = 'flex';
            document.getElementById('selected-count').textContent = count;
            
            // Build comma-separated IDs
            const ids = Array.from(checked).map(cb => cb.value).join(',');
            document.getElementById('bulk-ids-input').value = ids;
        } else {
            bulkBar.style.display = 'none';
            document.getElementById('select-all').checked = false;
        }
    }
</script>
@endsection
