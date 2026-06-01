@extends('layouts.app')

@section('title', 'Penyerapan Hasil Tani Warga - KDKMP')

@section('content')
<style>
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
        width: 100%;
        height: 44px;
        border-radius: var(--r-sm);
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
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
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

    .card-3d {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04),
                    0 1px 2px rgba(0, 0, 0, 0.01),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .card-3d:hover {
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
</style>

<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; text-decoration: none; width: fit-content; transition: transform var(--t-fast);" onmouseover="this.style.transform='translateX(-4px)'" onmouseout="this.style.transform=''">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<div style="margin-bottom: 32px;">
    <h1 style="font-size: 32px; font-weight: 800; color: var(--ink); letter-spacing: -0.8px; margin: 0;">🌾 Penyerapan Komoditas Tani Lokal</h1>
    <p style="color: var(--muted); font-size: 14.5px; margin-top: 4px; margin-bottom: 0;">Jual hasil tani langsung ke koperasi dengan penimbangan transparan.</p>
</div>

<div class="split-layout">
    
    <!-- Left: Crops Sold History -->
    <div class="main-column">
        <div class="card-3d" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface));">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--ink); margin: 0;">📜 Riwayat Penjualan Hasil Tani</h3>
            </div>
            
            @if($crops->isEmpty())
                <div style="padding: 48px; text-align: center; color: var(--muted);">
                    Belum ada riwayat penyerapan hasil tani Anda.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Komoditas</th>
                                <th>Kuantitas</th>
                                <th>Harga Beli</th>
                                <th>Total Payout</th>
                                <th style="text-align: center;">Timbangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crops as $crop)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ink);">{{ $crop->absorption_date->format('d M Y') }}</div>
                                        <span style="font-size: 11px; color: var(--muted);">{{ $crop->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    <td style="font-weight: 800; color: var(--ink);">{{ $crop->product_name }}</td>
                                    <td style="font-weight: 700; color: var(--body);">{{ number_format($crop->quantity, 2) }} <span style="font-size: 11px; color: var(--muted); font-weight: 500;">kg/unit</span></td>
                                    <td style="color: var(--body); font-weight: 500;">Rp {{ number_format($crop->price_per_unit, 0, ',', '.') }}</td>
                                    <td style="font-weight: 800; color: var(--primary); font-size: 14.5px;">
                                        Rp {{ number_format($crop->total_payout, 0, ',', '.') }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($crop->scale_image)
                                            <button type="button" class="btn-3d-secondary" onclick="showScalePopup('{{ $crop->scale_image }}')" style="padding: 0 10px; height: 26px; font-size: 11px; border-radius: 100px;">
                                                👁️ Lihat Foto
                                            </button>
                                        @else
                                            <span style="color: var(--muted); font-size: 11.5px; font-weight: 500; font-style: italic;">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($crop->status === 'paid')
                                            <span class="badge badge-success" style="font-weight: 700;">PAID</span>
                                        @elseif($crop->status === 'received')
                                            <span class="badge badge-info" style="font-weight: 700;">RECEIVED</span>
                                        @else
                                            <span class="badge badge-warning" style="font-weight: 700;">PENDING</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Sell Crop Form -->
    <div class="sticky-rail">
        <div class="card-3d" style="background: linear-gradient(135deg, #ffffff, #f8fafc) !important; padding: 24px;">
            <h3 style="font-size: 17px; font-weight: 800; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 12px; margin-bottom: 20px; color: var(--ink);">
                🌾 Tawarkan Hasil Panen
            </h3>
            
            <form action="{{ route('member.crops.sell') }}" method="POST" id="sell-crop-form" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Mengirim...';" style="margin: 0;">
                @csrf
                
                <div class="form-group">
                    <label for="crop_select">Komoditas Tani</label>
                    <select id="crop_select" class="form-select" onchange="onCropSelectChange(this)" required style="width: 100%; font-weight: 600;">
                        <option value="">-- Pilih Komoditas --</option>
                        @foreach($localProducts as $prod)
                            @php $buyPrice = round($prod->price_member * 0.85); @endphp
                            <option value="{{ $prod->id }}" 
                                    data-name="{{ $prod->name }}" 
                                    data-price="{{ $buyPrice }}" 
                                    data-unit="{{ $prod->unit }}">
                                {{ $prod->name }} (Rp {{ number_format($buyPrice, 0, ',', '.') }}/{{ $prod->unit }})
                            </option>
                        @endforeach
                        <option value="custom">Komoditas Lain (Tulis Manual)</option>
                    </select>
                </div>

                <!-- Hidden or manual name input -->
                <div class="form-group" id="manual-name-group" style="display: none; margin-top: 14px;">
                    <label for="product_name">Nama Komoditas Lain</label>
                    <input type="text" name="product_name" id="product_name" class="text-input" placeholder="Contoh: Cabai Keriting Hijau" style="width: 100%;">
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label for="quantity">Kuantitas Panen (<span id="unit-label">Unit</span>)</label>
                    <input type="number" step="0.01" name="quantity" id="quantity" class="text-input" placeholder="Masukkan jumlah" oninput="calculateTotal()" required style="width: 100%;">
                </div>

                <div class="form-group" style="margin-top: 14px;">
                    <label for="price_per_unit">Harga per <span id="price-unit-label">Unit</span> (Rupiah)</label>
                    <input type="number" name="price_per_unit" id="price_per_unit" class="text-input" placeholder="Rp" oninput="calculateTotal()" required style="width: 100%;">
                </div>

                <!-- Live estimation display -->
                <div style="background-color: var(--surface-soft); padding: 14px; border-radius: var(--r-sm); border: 1px solid var(--hairline-soft); font-size: 13px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted); font-weight: 500;">Estimasi Payout:</span>
                    <strong id="payout-display" style="color: var(--success); font-size: 16px; font-weight: 800;">Rp 0</strong>
                </div>

                <button type="submit" class="btn-3d-primary">Kirim Penawaran Tani ➔</button>
            </form>
        </div>
    </div>

</div>

<script>
    function onCropSelectChange(select) {
        const selected = select.options[select.selectedIndex];
        const manualNameGroup = document.getElementById('manual-name-group');
        const nameInput = document.getElementById('product_name');
        const priceInput = document.getElementById('price_per_unit');
        const unitLabel = document.getElementById('unit-label');
        const priceUnitLabel = document.getElementById('price-unit-label');
        
        if (select.value === 'custom') {
            manualNameGroup.style.display = 'block';
            nameInput.required = true;
            nameInput.value = '';
            
            priceInput.readOnly = false;
            priceInput.value = '';
            priceInput.style.backgroundColor = '';
            
            unitLabel.textContent = 'Kg / Unit';
            priceUnitLabel.textContent = 'Kg / Unit';
        } else if (select.value !== '') {
            manualNameGroup.style.display = 'none';
            nameInput.required = false;
            nameInput.value = selected.dataset.name;
            
            priceInput.readOnly = true;
            priceInput.value = selected.dataset.price;
            priceInput.style.backgroundColor = 'var(--surface-soft)';
            
            unitLabel.textContent = selected.dataset.unit;
            priceUnitLabel.textContent = selected.dataset.unit;
        } else {
            manualNameGroup.style.display = 'none';
            nameInput.required = false;
            nameInput.value = '';
            
            priceInput.readOnly = false;
            priceInput.value = '';
            priceInput.style.backgroundColor = '';
            
            unitLabel.textContent = 'Unit';
            priceUnitLabel.textContent = 'Unit';
        }
        
        calculateTotal();
    }

    function calculateTotal() {
        const qty = parseFloat(document.getElementById('quantity').value) || 0;
        const price = parseFloat(document.getElementById('price_per_unit').value) || 0;
        const total = qty * price;
        
        document.getElementById('payout-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    // Set initial product name target on form submit
    document.getElementById('sell-crop-form').addEventListener('submit', function(e) {
        const select = document.getElementById('crop_select');
        const nameInput = document.getElementById('product_name');
        if (select.value !== '' && select.value !== 'custom') {
            nameInput.value = select.options[select.selectedIndex].dataset.name;
        }
    });
</script>

{{-- Photo Scale Viewer Popup --}}
<div style="position: fixed; inset: 0; background: rgba(17,24,39,0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;" id="scale-viewer-overlay" onclick="closeScalePopup()">
    <img src="" style="max-width: 90%; max-height: 80vh; border-radius: var(--r-xl); border: 2px solid rgba(255,255,255,0.15); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);" id="scale-viewer-img" onclick="event.stopPropagation()">
</div>

<script>
    // --- Scale Viewer Modal ---
    function showScalePopup(base64Src) {
        const overlay = document.getElementById('scale-viewer-overlay');
        const img = document.getElementById('scale-viewer-img');
        img.src = base64Src;
        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 10);
    }

    function closeScalePopup() {
        const overlay = document.getElementById('scale-viewer-overlay');
        const img = document.getElementById('scale-viewer-img');
        overlay.style.opacity = '0';
        img.style.transform = 'scale(0.9)';
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 300);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeScalePopup();
    });
</script>
@endsection
