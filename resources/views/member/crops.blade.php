@extends('layouts.app')

@section('title', 'Penyerapan Hasil Tani Warga - KDKMP')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 600; color: var(--ink); display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali ke dashboard
    </a>
</div>

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px;">Penyerapan Komoditas Lokal Desa</h1>

<div class="split-layout">
    
    <!-- Left: Crops Sold History -->
    <div class="main-column">
        <div class="standard-card" style="padding: 0; overflow: hidden;">
            <h3 style="font-size: 18px; font-weight: 600; padding: 20px; border-bottom: 1px solid var(--hairline);">Riwayat Penjualan Hasil Tani</h3>
            
            @if($crops->isEmpty())
                <div style="padding: 32px; text-align: center; color: var(--muted);">
                    Belum ada riwayat penyerapan hasil tani Anda.
                </div>
            @else
                <table class="clean-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Tanggal Diajukan</th>
                            <th>Nama Hasil Tani</th>
                            <th>Kuantitas</th>
                            <th>Harga per Kg/Unit</th>
                            <th>Total Payout</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($crops as $crop)
                            <tr>
                                <td>{{ $crop->absorption_date->format('d M Y') }}</td>
                                <td style="font-weight: 600;">{{ $crop->product_name }}</td>
                                <td>{{ number_format($crop->quantity, 2) }} kg/unit</td>
                                <td>Rp {{ number_format($crop->price_per_unit, 0, ',', '.') }}</td>
                                <td style="font-weight: 600; color: #1a7f5a;">
                                    Rp {{ number_format($crop->total_payout, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span style="font-weight: 600; text-transform: uppercase; font-size: 11px;
                                        {{ $crop->status === 'paid' ? 'color:#1a7f5a;' : '' }}
                                        {{ $crop->status === 'received' ? 'color:#0052cc;' : '' }}
                                        {{ $crop->status === 'pending' ? 'color:#b28900;' : '' }}
                                    ">
                                        {{ $crop->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right: Sell Crop Form -->
    <div class="sticky-rail">
        <div class="reservation-card">
            <h3 style="font-size: 18px; font-weight: 600; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">Tawarkan Hasil Panen</h3>
            
            <form action="{{ route('member.crops.sell') }}" method="POST" id="sell-crop-form">
                @csrf
                
                <div class="form-group">
                    <label for="crop_select">Komoditas Tani</label>
                    <select id="crop_select" class="form-select" onchange="onCropSelectChange(this)" required>
                        <option value="">-- Pilih Komoditas --</option>
                        @foreach($localProducts as $prod)
                            @php $buyPrice = round($prod->price_member * 0.85); @endphp
                            <option value="{{ $prod->id }}" 
                                    data-name="{{ $prod->name }}" 
                                    data-price="{{ $buyPrice }}" 
                                    data-unit="{{ $prod->unit }}">
                                {{ $prod->name }} (Beli: Rp {{ number_format($buyPrice, 0, ',', '.') }}/{{ $prod->unit }})
                            </option>
                        @endforeach
                        <option value="custom">Komoditas Lain (Tulis Manual)</option>
                    </select>
                </div>

                <!-- Hidden or manual name input -->
                <div class="form-group" id="manual-name-group" style="display: none;">
                    <label for="product_name">Nama Komoditas Lain</label>
                    <input type="text" name="product_name" id="product_name" class="text-input" placeholder="Contoh: Cabai Keriting Hijau">
                </div>

                <div class="form-group">
                    <label for="quantity">Kuantitas Panen (<span id="unit-label">Unit</span>)</label>
                    <input type="number" step="0.01" name="quantity" id="quantity" class="text-input" placeholder="Masukkan jumlah" oninput="calculateTotal()" required>
                </div>

                <div class="form-group">
                    <label for="price_per_unit">Harga Penawaran Koperasi per <span id="price-unit-label">Unit</span> (Rupiah)</label>
                    <input type="number" name="price_per_unit" id="price_per_unit" class="text-input" placeholder="Rp" oninput="calculateTotal()" required>
                </div>

                <!-- Live estimation display -->
                <div style="background-color: var(--surface); padding: 12px; border-radius: var(--r-sm); font-size: 14px; margin-bottom: 24px;">
                    Estimasi Payout: <strong id="payout-display" style="color: #1a7f5a; font-size: 15px;">Rp 0</strong>
                </div>

                <button type="submit" class="button-primary">Kirim Penawaran</button>
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
            manualNameGroup.style.display = 'flex';
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
@endsection
