@extends('layouts.app')

@section('title', 'Inventaris Toko Sembako - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('staff.dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--colors-ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard staf
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Inventaris Gerai Sembako</h1>

<div class="split-layout">
    
    <!-- Left: Inventory List Table -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 600; padding: 20px; border-bottom: 1px solid var(--colors-hairline);">Daftar Semua Barang</h3>
            
            @if($products->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--colors-muted);">
                    Belum ada barang di inventaris. Gunakan form di sebelah kanan untuk menambahkan produk baru.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga Anggota</th>
                            <th>Harga Umum</th>
                            <th>Stok</th>
                            <th style="text-align: center; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $product->name }}</div>
                                    <span style="font-size: 11px; color: var(--colors-muted);">Unit: {{ $product->unit }}</span>
                                    @if($product->is_local_product)
                                        <span style="font-size: 10px; color: #1a7f5a; background-color: #e6f6f0; padding: 1px 4px; border-radius: 4px; font-weight: 600; margin-left: 4px;">Hasil Tani</span>
                                    @endif
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>Rp {{ number_format($product->price_member, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($product->price_non_member, 0, ',', '.') }}</td>
                                <td style="font-weight: 700; color: {{ $product->current_stock <= 5 ? 'var(--colors-primary-error-text)' : '#1a7f5a' }}">
                                    {{ $product->current_stock }} {{ $product->unit }}
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                        <button type="button" class="button-secondary" style="height: 28px; font-size: 11px; padding: 0 8px; width: auto;" 
                                            onclick="loadEditForm({{ json_encode($product) }})">
                                            Edit
                                        </button>
                                        <form action="{{ route('staff.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="button-primary" style="height: 28px; font-size: 11px; padding: 0 8px; width: auto; background-color: var(--colors-primary-error-text);">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Create/Edit Form Drawer -->
    <div class="sticky-rail">
        <div class="reservation-card" id="form-panel">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--colors-hairline); padding-bottom: 12px;" id="panel-title">Tambah Produk Baru</h3>
            
            <form action="{{ route('staff.products.store') }}" method="POST" id="product-form">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                
                <div class="form-group">
                    <label for="name">Nama Barang</label>
                    <input type="text" name="name" id="form-name" class="text-input" placeholder="Contoh: Beras Pandan Wangi" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select name="category_id" id="form-category-id" class="text-input" style="height: 48px; padding: 0 12px;" required>
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
                    <label for="unit">Satuan Jual (UOM)</label>
                    <input type="text" name="unit" id="form-unit" class="text-input" placeholder="kg, pcs, liter, bungkus" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div class="form-group">
                        <label for="price_member">Harga Anggota</label>
                        <input type="number" name="price_member" id="form-price-member" class="text-input" placeholder="Rp" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="price_non_member">Harga Umum</label>
                        <input type="number" name="price_non_member" id="form-price-non-member" class="text-input" placeholder="Rp" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="current_stock">Jumlah Stok</label>
                    <input type="number" name="current_stock" id="form-current-stock" class="text-input" placeholder="Contoh: 100" min="0" required>
                </div>

                <div class="form-group" style="flex-direction: row; gap: 12px; align-items: center; margin-bottom: 24px;">
                    <input type="checkbox" name="is_local_product" id="form-local" style="width: 18px; height: 18px; cursor: pointer;">
                    <label for="form-local" style="cursor: pointer; font-weight: 500;">Merupakan Komoditas Lokal Desa</label>
                </div>

                <button type="submit" class="button-primary" id="form-submit-btn">Simpan Produk</button>
                <button type="button" class="button-secondary" id="form-cancel-btn" style="display: none; margin-top: 8px; border-color: var(--colors-border-strong);" onclick="resetForm()">
                    Batal Edit
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    function loadEditForm(product) {
        document.getElementById('panel-title').textContent = 'Edit Produk';
        document.getElementById('form-submit-btn').textContent = 'Perbarui Produk';
        document.getElementById('form-cancel-btn').style.display = 'block';
        
        const form = document.getElementById('product-form');
        form.action = `/staff/products/${product.id}/update`;
        document.getElementById('form-method').value = 'POST'; // We use POST for laravel update here since Route supports it
        
        document.getElementById('form-name').value = product.name;
        document.getElementById('form-category-id').value = product.category_id;
        document.getElementById('form-description').value = product.description || '';
        document.getElementById('form-unit').value = product.unit;
        document.getElementById('form-price-member').value = Math.round(product.price_member);
        document.getElementById('form-price-non-member').value = Math.round(product.price_non_member);
        document.getElementById('form-current-stock').value = product.current_stock;
        document.getElementById('form-local').checked = !!product.is_local_product;
        
        // Scroll slightly to the form panel on mobile
        document.getElementById('form-panel').scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.getElementById('panel-title').textContent = 'Tambah Produk Baru';
        document.getElementById('form-submit-btn').textContent = 'Simpan Produk';
        document.getElementById('form-cancel-btn').style.display = 'none';
        
        const form = document.getElementById('product-form');
        form.action = "{{ route('staff.products.store') }}";
        form.reset();
    }
</script>
@endsection
