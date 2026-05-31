@extends('layouts.admin')

@section('title', 'Kelola Penyerapan Panen - KDKMP')

@section('content')

<style>
    /* Status filters tab */
    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--hairline);
        background: var(--surface);
        color: var(--muted);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .filter-tab:hover {
        border-color: var(--ink);
        color: var(--ink);
    }
    .filter-tab.active {
        background: var(--ink);
        color: var(--canvas);
        border-color: var(--ink);
    }

    /* Modal dialog style */
    .crop-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .crop-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .crop-modal-box {
        background: var(--canvas);
        border: 1px solid var(--hairline);
        border-radius: var(--r-md);
        box-shadow: var(--shadow-xl);
        width: 100%;
        max-width: 480px;
        padding: 24px;
        transform: scale(0.9);
        transition: transform 0.3s var(--ease-spring);
        position: relative;
    }
    .crop-modal-overlay.active .crop-modal-box {
        transform: scale(1);
    }

    /* Badge styles */
    .badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 100px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }
    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }
    .badge-received { background: rgba(59, 130, 246, 0.1); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.2); }
    .badge-paid { background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
</style>

{{-- Header --}}
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Penyerapan Hasil Bumi</h1>
        <p style="color: var(--muted); font-size: 14px; margin-top: 4px; font-family: var(--font);">📍 Kelola hasil panen lokal yang diserap koperasi di gerai <strong>{{ auth()->user()->branch->name }}</strong></p>
    </div>
</div>

{{-- Stats cards grid --}}
<div class="grid-3" style="margin-bottom: 28px;">
    @php
        $branchId = auth()->user()->branch_id;
        $totalPending = \App\Models\CropAbsorption::where('branch_id', $branchId)->where('status', 'pending')->count();
        $totalReceived = \App\Models\CropAbsorption::where('branch_id', $branchId)->where('status', 'received')->count();
        $totalPaidSum = \App\Models\CropAbsorption::where('branch_id', $branchId)->where('status', 'paid')->sum('total_payout');
    @endphp
    
    <div class="stat-card reveal">
        <span class="stat-label">Pengajuan Pending</span>
        <div class="stat-value" style="color: #d97706;">{{ $totalPending }} Transaksi</div>
        <p class="stat-desc">Menunggu masuk gudang &amp; timbang</p>
        <span class="stat-icon">⏳</span>
    </div>
    
    <div class="stat-card reveal delay-1">
        <span class="stat-label">Sudah Diterima (Belum Bayar)</span>
        <div class="stat-value" style="color: #2563eb;">{{ $totalReceived }} Transaksi</div>
        <p class="stat-desc">Komoditas telah diverifikasi di gudang</p>
        <span class="stat-icon">⚖️</span>
    </div>
    
    <div class="stat-card reveal delay-2">
        <span class="stat-label">Total Dana Terserap (Lunas)</span>
        <div class="stat-value" style="color: #059669;">Rp {{ number_format($totalPaidSum, 0, ',', '.') }}</div>
        <p class="stat-desc">Dana hasil panen disetor ke saldo warga</p>
        <span class="stat-icon">💸</span>
    </div>
</div>

{{-- Filters and Search --}}
<div class="filter-tabs">
    @php $currStatus = request('status'); @endphp
    <a href="{{ route('staff.crops') }}" class="filter-tab {{ is_null($currStatus) ? 'active' : '' }}">Semua Status</a>
    <a href="{{ route('staff.crops', ['status' => 'pending']) }}" class="filter-tab {{ $currStatus === 'pending' ? 'active' : '' }}">Pending ⏳</a>
    <a href="{{ route('staff.crops', ['status' => 'received']) }}" class="filter-tab {{ $currStatus === 'received' ? 'active' : '' }}">Diterima ⚖️</a>
    <a href="{{ route('staff.crops', ['status' => 'paid']) }}" class="filter-tab {{ $currStatus === 'paid' ? 'active' : '' }}">Lunas 💸</a>
</div>

{{-- Crop Table --}}
<div class="card card-flush" style="box-shadow: var(--shadow-sm); overflow: hidden;">
    @php
        $filteredQuery = \App\Models\CropAbsorption::with('member.user')->where('branch_id', $branchId);
        if ($currStatus) {
            $filteredQuery->where('status', $currStatus);
        }
        $filteredCrops = $filteredQuery->latest()->get();
    @endphp

    @if($filteredCrops->isEmpty())
        <div style="padding: 48px; text-align: center; color: var(--muted);">
            <div style="font-size: 44px; margin-bottom: 12px;">🌾</div>
            <p style="font-weight: 600; margin-bottom: 4px;">Tidak Ada Data Penyerapan</p>
            <p style="font-size: 13px;">Belum ada komoditas tani terdaftar dalam kategori ini.</p>
        </div>
    @else
        <div class="clean-table-container">
            <table class="clean-table" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th>Petani (Anggota)</th>
                        <th>Komoditas Tani</th>
                        <th>Kuantitas</th>
                        <th>Harga Beli Koperasi</th>
                        <th>Total Payout</th>
                        <th>Bukti Timbangan</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredCrops as $crop)
                        <tr>
                            <td>
                                <div>
                                    <div style="font-weight: 700; color: var(--ink); font-size: 14px;">{{ $crop->member->user->name }}</div>
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">No. Anggota: {{ $crop->member->nomor_anggota }}</div>
                                </div>
                            </td>
                            <td>
                                <strong style="color: var(--ink);">{{ $crop->product_name }}</strong>
                            </td>
                            <td>
                                <span style="font-weight: 700;">{{ number_format($crop->quantity, 2) }}</span>
                                <span style="font-size: 12px; color: var(--muted);"> kg/unit</span>
                            </td>
                            <td>
                                <span style="font-size: 13px;">Rp {{ number_format($crop->price_per_unit, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <strong style="color: #059669; font-size: 14px;">Rp {{ number_format($crop->total_payout, 0, ',', '.') }}</strong>
                                @if($crop->status === 'paid' && $crop->deducted_loan_payment > 0)
                                    <div style="font-size: 10px; color: var(--danger); margin-top: 2px;">
                                        Potongan Kredit: -Rp {{ number_format($crop->deducted_loan_payment, 0, ',', '.') }}
                                    </div>
                                    <div style="font-size: 10px; color: var(--success); font-weight: 600;">
                                        Net: Rp {{ number_format($crop->net_payout, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($crop->scale_image)
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="showScalePopup('{{ $crop->scale_image }}')" style="padding: 2px 8px; height: auto; font-size: 11px; border-radius: 4px;">
                                        👁️ Lihat Timbangan
                                    </button>
                                @else
                                    <span style="color: var(--muted); font-size: 12px; font-style: italic;">Belum ada foto</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-status badge-{{ $crop->status }}">
                                    {{ $crop->status === 'pending' ? '⏳ pending' : '' }}
                                    {{ $crop->status === 'received' ? '⚖️ diterima' : '' }}
                                    {{ $crop->status === 'paid' ? '💸 lunas' : '' }}
                                </span>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    @if($crop->status === 'pending')
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openWeighingModal({{ $crop->id }}, '{{ $crop->product_name }}', {{ $crop->quantity }})" style="height: 30px; font-size: 12px; padding: 0 14px; background-color: #2563eb; font-weight: 700; border-radius: 100px;">
                                            Timbang &amp; Terima Gudang ⚖️
                                        </button>
                                    @elseif($crop->status === 'received')
                                        <form action="{{ route('staff.crops.update', [$crop->id, 'paid']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan pembayaran ini? Saldo net akan didepositkan ke Tabungan Sukarela anggota.')" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm" style="height: 30px; font-size: 12px; padding: 0 14px; background-color: #059669; font-weight: 700; border-radius: 100px;">
                                                Proses Pembayaran 💸
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 12px; color: var(--muted); font-style: italic;">Selesai &amp; Diarsipkan</span>
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

{{-- MODAL 1: Upload Scale Photo Modal --}}
<div class="crop-modal-overlay" id="weighing-modal">
    <div class="crop-modal-box">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--ink); margin-top: 0; margin-bottom: 14px; border-bottom: 1px solid var(--hairline); padding-bottom: 12px;">
            ⚖️ Verifikasi Timbangan Gudang
        </h3>
        
        <form action="" method="POST" id="weighing-form">
            @csrf
            
            <div style="background: var(--surface-soft); padding: 12px; border-radius: var(--r-sm); margin-bottom: 16px; font-size: 13px;">
                Komoditas: <strong id="weighing-crop-name">-</strong><br>
                Kuantitas Pengajuan Warga: <strong id="weighing-crop-qty">-</strong>
            </div>

            <div class="form-group">
                <label for="scale-file-upload">Foto Timbangan Digital / Fisik (Wajib)</label>
                <input type="file" id="scale-file-upload" class="text-input" accept="image/*" onchange="convertScaleImageToBase64(this)" style="padding-top: 6px;" required>
                <input type="hidden" name="scale_image" id="form-scale-image">
                
                <div id="scale-preview-container" style="margin-top: 12px; display: none; text-align: center; background: var(--surface-soft); padding: 10px; border-radius: var(--r-md); border: 1px dashed var(--hairline);">
                    <img id="scale-preview-img" src="" style="max-width: 100%; max-height: 160px; border-radius: var(--r-sm); border: 1px solid var(--hairline); object-fit: cover;">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="clearScalePreview()" style="color: var(--danger); font-size: 11px; padding: 2px 8px; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px; border-color: var(--danger-border); background: var(--danger-bg);">🗑️ Hapus Gambar</button>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn btn-ghost" onclick="closeWeighingModal()" style="flex: 1; border-radius: 100px; height: 40px; font-size: 13px;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex: 2; border-radius: 100px; height: 40px; font-size: 13px; font-weight: 700; background-color: #2563eb;">Konfirmasi Timbangan ➔</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Photo Scale Viewer Popup --}}
<div class="crop-modal-overlay" id="scale-viewer-overlay" onclick="closeScalePopup()" style="background: rgba(0,0,0,0.85);">
    <img src="" style="max-width: 90%; max-height: 80vh; border-radius: var(--r-md); box-shadow: var(--shadow-xl); transform: scale(0.9); transition: transform 0.3s var(--ease-spring);" id="scale-viewer-img" onclick="event.stopPropagation()">
</div>

<script>
    // --- Scale Image Base64 Converter ---
    function convertScaleImageToBase64(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64String = e.target.result;
                document.getElementById('form-scale-image').value = base64String;
                
                const previewImg = document.getElementById('scale-preview-img');
                const previewContainer = document.getElementById('scale-preview-container');
                previewImg.src = base64String;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearScalePreview() {
        document.getElementById('form-scale-image').value = '';
        document.getElementById('scale-file-upload').value = '';
        document.getElementById('scale-preview-img').src = '';
        document.getElementById('scale-preview-container').style.display = 'none';
    }

    // --- Modal Open/Close ---
    function openWeighingModal(id, cropName, quantity) {
        const modal = document.getElementById('weighing-modal');
        const form = document.getElementById('weighing-form');
        
        form.action = `/staff/crops/${id}/received`;
        document.getElementById('weighing-crop-name').textContent = cropName;
        document.getElementById('weighing-crop-qty').textContent = quantity.toLocaleString('id-ID') + ' kg/unit';
        
        clearScalePreview();
        modal.classList.add('active');
    }

    function closeWeighingModal() {
        const modal = document.getElementById('weighing-modal');
        modal.classList.remove('active');
    }

    // --- Viewer Popup Modal ---
    function showScalePopup(base64Src) {
        const overlay = document.getElementById('scale-viewer-overlay');
        const img = document.getElementById('scale-viewer-img');
        img.src = base64Src;
        overlay.classList.add('active');
    }

    function closeScalePopup() {
        const overlay = document.getElementById('scale-viewer-overlay');
        overlay.classList.remove('active');
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeWeighingModal();
            closeScalePopup();
        }
    });
</script>

@endsection
