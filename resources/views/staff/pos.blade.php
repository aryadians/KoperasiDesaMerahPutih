@extends('layouts.admin')

@section('title', 'KDKMP — POS Kasir Gerai Offline')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <h1 style="font-size: 28px; font-weight: 800; color: var(--ink);">🏪 POS Kasir Gerai</h1>
    <div style="background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); padding: 6px 14px; border-radius: 100px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
        <span class="pulse-ring" style="width: 8px; height: 8px;"></span> Kasir Aktif: {{ auth()->user()->name }}
    </div>
</div>

<div class="split-layout">
    
    <!-- LEFT: Product Search and Catalog Grid -->
    <div class="main-column">
        <div class="card card-flush" style="margin-bottom: 16px; box-shadow: var(--shadow-sm); padding: 16px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted);" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="pos-search" class="text-input" placeholder="Scan Barcode / Cari nama barang (Tekan F1)" oninput="filterPOSProducts()" onkeydown="handleBarcodeScan(event)" style="height: 48px; padding-left: 44px; font-weight: 600; font-size: 15px;" autofocus autocomplete="off">
                </div>
                <select id="pos-category" class="text-input" onchange="filterPOSProducts()" style="height: 48px; width: 220px; font-weight: 500;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="category-strip" style="margin: 16px 0 0 0; padding-bottom: 4px;">
                <button class="category-tab active" id="btn-cat-all" onclick="selectCategory('')">🏪 Semua</button>
                @foreach($categories as $cat)
                    <button class="category-tab" id="btn-cat-{{ $cat->id }}" onclick="selectCategory('{{ $cat->id }}')">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; max-height: calc(100vh - 260px); overflow-y: auto; padding-right: 4px; padding-bottom: 24px;" id="pos-products-grid">
            @foreach($products as $prod)
                <div class="property-card reveal-scale pos-product-item" 
                     data-id="{{ $prod->id }}" 
                     data-name="{{ strtolower($prod->name) }}" 
                     data-category="{{ $prod->category_id }}"
                     data-member-price="{{ $prod->price_member }}"
                     data-guest-price="{{ $prod->price_non_member }}"
                     data-stock="{{ $prod->current_stock }}"
                     data-unit="{{ $prod->unit }}"
                     onclick="addPCToCart(this)"
                     style="cursor: pointer; border-radius: var(--r-md); box-shadow: var(--shadow-sm); border: 1px solid var(--hairline-soft);">
                    
                    <div class="property-card-photo" style="aspect-ratio: 1.25; height: 110px;">
                        <img src="{{ $prod->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=200&q=80' }}" alt="{{ $prod->name }}" style="height: 100%; object-fit: cover;">
                        @if($prod->is_local_product)
                            <span class="local-badge" style="font-size: 9px; padding: 2px 6px;">🌾 Tani Lokal</span>
                        @endif
                    </div>
                    
                    <div style="padding: 12px; display: flex; flex-direction: column; justify-content: space-between; height: 105px;">
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3;" title="{{ $prod->name }}">{{ $prod->name }}</div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px; font-weight: 500;">Stok: {{ $prod->current_stock }} {{ $prod->unit }}</div>
                        </div>
                        <div style="margin-top: 4px;">
                            <div style="font-size: 14px; font-weight: 800; color: var(--primary); line-height: 1;">Rp {{ number_format($prod->price_non_member, 0, ',', '.') }}</div>
                            <div style="font-size: 10px; color: var(--success); font-weight: 600; margin-top: 4px;">Anggota: Rp {{ number_format($prod->price_member, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT: Cashier POS Cart -->
    <div class="sticky-rail">
        <div class="card" style="padding: 24px; height: calc(100vh - 120px); display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-lg); border: 1.5px solid var(--hairline);">
            <div>
                <h3 style="font-size: 16px; font-weight: 700; border-bottom: 1px solid var(--hairline); padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; color: var(--ink);">
                    <span>🛒 Keranjang Kasir</span>
                    <button class="btn btn-ghost btn-sm" onclick="clearPOSCart()" style="height: 28px; font-size: 11px; padding: 0 10px; border-color: var(--danger); color: var(--danger);">Reset (F4)</button>
                </h3>

                <!-- Member NIK Section -->
                <div style="background: var(--surface); padding: 12px; border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid var(--hairline-soft);">
                    <label class="field-label" style="font-size: 11px; font-weight: 700; margin-bottom: 6px; display: block; color: var(--body);">SCAN KARTU ANGGOTA (NIK)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="pos-member-nik" class="text-input" placeholder="Barcode NIK / Ketik manual" style="height: 38px; font-size: 13px; font-weight: 600;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="lookupPOSMember()" style="height: 38px; width: 60px; padding: 0;">Cek</button>
                    </div>
                    <div id="pos-member-result" style="font-size: 12px; margin-top: 8px; font-weight: 700; color: var(--success); display: none; background: var(--success-bg); padding: 6px 10px; border-radius: var(--r-xs); border: 1px solid var(--success-border);">
                        👤 <span id="pos-member-name">-</span>
                    </div>
                </div>

                <!-- Cart Items List -->
                <div style="flex-grow: 1; max-height: calc(100vh - 480px); overflow-y: auto; display: flex; flex-direction: column; gap: 10px; border-bottom: 1px dashed var(--hairline); padding-bottom: 12px; margin-bottom: 16px;" id="pos-cart-list">
                    <div style="text-align: center; color: var(--muted); font-size: 13px; padding: 48px 0;" id="pos-cart-empty">
                        <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;">🛒</div>
                        Keranjang kosong.<br>Scan barcode atau klik produk.
                    </div>
                </div>
            </div>

            <!-- Pricing Summary and Pay checkout -->
            <div style="background: var(--surface-md); padding: 16px; border-radius: var(--r-md); border: 1px solid var(--hairline-soft);">
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; margin-bottom: 16px; color: var(--body);">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Jumlah Barang</span>
                        <strong id="pos-total-items" style="color: var(--ink);">0 Barang</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Potongan Member</span>
                        <strong id="pos-total-discount" style="color: var(--success); font-weight: 700;">- Rp 0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 16px; border-top: 1px dashed var(--hairline); padding-top: 8px; margin-top: 4px; font-weight: 700; color: var(--ink);">
                        <span>Total Bayar</span>
                        <strong style="font-size: 24px; color: var(--primary); line-height: 1;" id="pos-total-pay">Rp 0</strong>
                    </div>
                </div>

                <div style="background: var(--canvas); padding: 12px; border-radius: var(--r-md); margin-bottom: 16px; border: 1px solid var(--hairline);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 12px; font-weight: 700; color: var(--ink);">TUNAI DITERIMA</span>
                        <span style="font-size: 11px; font-weight: 600; color: var(--primary); cursor: pointer; background: var(--primary-light); padding: 2px 8px; border-radius: 100px;" onclick="posFillExactCash()">Uang Pas (F3)</span>
                    </div>
                    <input type="number" id="pos-cash-received" class="text-input" placeholder="Rp 0" oninput="calculatePOSChange()" style="height: 44px; font-weight: 800; font-size: 18px; color: var(--ink); text-align: right;">
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 12px; font-size: 14px; font-weight: 700; border-top: 1px dashed var(--hairline-soft); padding-top: 8px;">
                        <span style="color: var(--muted);">KEMBALIAN</span>
                        <strong id="pos-cash-change" style="color: var(--success); font-size: 18px;">Rp 0</strong>
                    </div>
                </div>

                <button class="btn btn-primary btn-full" id="btn-pos-checkout" onclick="submitPOSCheckout()" style="height: 52px; border-radius: 100px; font-weight: 800; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px;" disabled>
                    Selesaikan Bayar (F2)
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
        // Many barcode scanners send an "Enter" key after the barcode string
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = e.target.value.toLowerCase().trim();
            if (!val) return;
            
            // Look for exactly 1 visible item matching the scan
            const cards = document.querySelectorAll('.pos-product-item');
            let matchedCard = null;
            let visibleCount = 0;

            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    visibleCount++;
                    matchedCard = card;
                }
            });

            // Auto-add if it uniquely filters down to 1 item (typical barcode behavior)
            if (visibleCount === 1 && matchedCard) {
                addPCToCart(matchedCard);
                e.target.value = ''; // clear for next scan
                filterPOSProducts(); // reset view
            }
        }
    }

    // ── Cart Add ──
    function addPCToCart(card) {
        playBeep(); // Audio feedback on scan/click
        
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
    }

    // ── Remove from POS Cart ──
    function removePCItem(id) {
        delete posCart[id];
        renderPOSCart();
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
    } else {
            posCart[id].quantity = newQty;
        }
        renderPOSCart();
    }

    // ── Clear POS Cart
    function clearPOSCart() {
        posCart = {};
        renderPOSCart();
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
            row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; gap: 10px; background: white; padding: 10px; border-radius: var(--r-sm); border: 1px solid var(--hairline-soft);';
            row.innerHTML = `
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</div>
                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                        Rp ${price.toLocaleString('id-ID')} / ${item.unit}
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button class="button-secondary" onclick="adjustPCQty(${item.id}, -1)" style="width: 24px; height: 24px; padding: 0; font-size: 14px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">-</button>
                    <span style="font-size: 13px; font-weight: 700; min-width: 16px; text-align: center;">${item.quantity}</span>
                    <button class="button-secondary" onclick="adjustPCQty(${item.id}, 1)" style="width: 24px; height: 24px; padding: 0; font-size: 14px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">+</button>
                </div>
                <div style="font-weight: 700; font-size: 13px; min-width: 72px; text-align: right;">
                    Rp ${subtotal.toLocaleString('id-ID')}
                </div>
                <button onclick="removePCItem(${item.id})" style="border: none; background: none; color: var(--primary); font-weight: bold; cursor: pointer; padding: 4px; font-size: 13px;" title="Hapus">✕</button>
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
                nameNode.textContent = data.name + ' (' + data.nomor_anggota + ')';
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
        const cards = document.querySelectorAll('#pos-products-grid .property-card');

        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const catId = card.dataset.category;
            const matchesSearch = name.includes(search);
            const matchesCategory = !category || catId === category;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function selectCategory(catId) {
        // Toggle tabs active class
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
            checkoutBtn.textContent = '🛍 Selesaikan & Cetak Struk';

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
            checkoutBtn.textContent = '🛍 Selesaikan & Cetak Struk';
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
            const isMem = selectedMember !== null;
            // estimate original non-member price for guest calculation
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

        const totalDiscount = order.points_earned * 0; // points only logged
        
        document.getElementById('rec-total-gross').textContent = 'Rp ' + totalNet.toLocaleString('id-ID');
        document.getElementById('rec-discount').textContent = selectedMember ? 'Harga Member Koperasi' : 'Rp 0';
        document.getElementById('rec-total-net').textContent = 'Rp ' + totalNet.toLocaleString('id-ID');
        document.getElementById('rec-cash').textContent = 'Rp ' + cashAmount.toLocaleString('id-ID');
        document.getElementById('rec-change').textContent = 'Rp ' + (cashAmount - totalNet).toLocaleString('id-ID');

        // Show overlay modal
        document.getElementById('pos-receipt-overlay').classList.add('active');
    }

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
                // Update UI
                const stockDisplay = productCard.querySelector('div[style*="font-size: 11px"]');
                if (stockDisplay) {
                    stockDisplay.textContent = 'Stok: ' + e.current_stock + ' ' + productCard.dataset.unit;
                }
            }
        });
</script>
@endsection
