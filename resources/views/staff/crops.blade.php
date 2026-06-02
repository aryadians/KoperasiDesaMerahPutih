@extends('layouts.admin')

@section('title', 'Kelola Penyerapan Panen - KDKMP')
@section('page-title', 'Hasil Bumi')

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
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: var(--muted);
        padding: 8px 16px; border-radius: var(--r-full);
        border: 1px solid rgba(0, 0, 0, 0.06);
        white-space: nowrap; cursor: pointer;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02), inset 0 1px 0 #fff;
        transition: all var(--t-fast) var(--ease-out);
        text-decoration: none;
    }
    .filter-tab:hover {
        color: var(--ink);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.04), inset 0 1px 0 #fff;
    }
    .filter-tab.active {
        color: white !important;
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        border-color: rgba(0,0,0,0.08) !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2), inset 0 1px 0 rgba(255,255,255,0.3) !important;
    }

    /* Modal dialog style */
    .crop-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all var(--t-base) var(--ease-out);
    }
    .crop-modal-overlay.active {
        display: flex;
        opacity: 1;
    }
    .crop-modal-box {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: var(--r-lg);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15), inset 0 1px 0 #ffffff;
        width: 100%;
        max-width: 480px;
        padding: 24px;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
        position: relative;
    }
    .crop-modal-overlay.active .crop-modal-box {
        transform: scale(1) translateY(0);
    }

    /* Stat Cards Custom Accents */
    .stat-card.pending {
        border-color: rgba(245, 158, 11, 0.15) !important;
        background: linear-gradient(135deg, var(--canvas), #fffbeb) !important;
    }
    .stat-card.pending:hover {
        box-shadow: 0 14px 28px rgba(245, 158, 11, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
        border-color: rgba(245, 158, 11, 0.25) !important;
    }
    .stat-card.pending::after {
        background: linear-gradient(90deg, #f59e0b, #fbbf24) !important;
    }

    .stat-card.received {
        border-color: rgba(59, 130, 246, 0.15) !important;
        background: linear-gradient(135deg, var(--canvas), #eff6ff) !important;
    }
    .stat-card.received:hover {
        box-shadow: 0 14px 28px rgba(59, 130, 246, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
        border-color: rgba(59, 130, 246, 0.25) !important;
    }
    .stat-card.received::after {
        background: linear-gradient(90deg, #3b82f6, #60a5fa) !important;
    }

    .stat-card.paid {
        border-color: rgba(16, 185, 129, 0.15) !important;
        background: linear-gradient(135deg, var(--canvas), #f0fdf4) !important;
    }
    .stat-card.paid:hover {
        box-shadow: 0 14px 28px rgba(16, 185, 129, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
        border-color: rgba(16, 185, 129, 0.25) !important;
    }
    .stat-card.paid::after {
        background: linear-gradient(90deg, #10b981, #34d399) !important;
    }

    /* Action triggers button 3D styling */
    .btn-3d-primary {
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        transition: all var(--t-fast) var(--ease-out);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
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
        transition: all var(--t-fast) var(--ease-out);
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

    .text-input {
        border-radius: var(--r-sm);
        border: 1.5px solid var(--hairline);
        background: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        transition: all var(--t-fast) var(--ease-out);
    }
    .text-input:focus {
        border-color: var(--ink);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05), inset 0 1px 2px rgba(0,0,0,0.01);
        transform: translateY(-1px);
    }

    .crops-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all var(--t-base) var(--ease-bounce);
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

    /* Weighing modal tabs */
    .weighing-tab-btn {
        background: #f8fafc;
        color: var(--muted);
        border: 1px solid var(--hairline-soft);
        cursor: pointer;
        padding: 8px 12px;
        font-weight: 700;
        font-size: 12px;
        border-radius: var(--r-sm);
        transition: all var(--t-fast) var(--ease-out);
        flex: 1;
        text-align: center;
    }
    .weighing-tab-btn:hover {
        color: var(--ink);
        background: #ffffff;
        border-color: var(--muted);
    }
    .weighing-tab-btn.active {
        color: white !important;
        background: linear-gradient(135deg, var(--primary), #e11d48) !important;
        border-color: rgba(0,0,0,0.08) !important;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.15);
    }
</style>
{{-- Header --}}
<div class="reveal" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Penyerapan <span style="color: var(--primary);">Hasil Bumi</span></h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
            <span style="color: var(--primary);">📍</span> {{ auth()->user()->branch->name }}
        </p>
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
    
    <div class="stat-card reveal pending">
        <span class="stat-label">Pengajuan Pending</span>
        <div class="stat-value" style="color: #d97706;">{{ $totalPending }} Transaksi</div>
        <p class="stat-desc">Menunggu masuk gudang &amp; timbang</p>
        <span class="stat-icon">⏳</span>
    </div>
    
    <div class="stat-card reveal delay-1 received">
        <span class="stat-label">Sudah Diterima (Belum Bayar)</span>
        <div class="stat-value" style="color: #2563eb;">{{ $totalReceived }} Transaksi</div>
        <p class="stat-desc">Komoditas telah diverifikasi di gudang</p>
        <span class="stat-icon">⚖️</span>
    </div>
    
    <div class="stat-card reveal delay-2 paid">
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
<div class="crops-card" style="overflow: hidden;">
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
                                    <button type="button" class="btn-3d-secondary" onclick="showScalePopup('{{ $crop->scale_image }}')" style="padding: 2px 10px; height: auto; font-size: 11px; border-radius: 100px;">
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
                                        <button type="button" class="btn-3d-primary" onclick="openWeighingModal({{ $crop->id }}, '{{ $crop->product_name }}', {{ $crop->quantity }})" style="height: 32px; font-size: 11px; padding: 0 14px; border-radius: 100px;">
                                            Timbang &amp; Terima Gudang ⚖️
                                        </button>
                                    @elseif($crop->status === 'received')
                                        <form action="{{ route('staff.crops.update', [$crop->id, 'paid']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan pembayaran ini? Saldo net akan didepositkan ke Tabungan Sukarela anggota.')" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-3d-primary" style="height: 32px; font-size: 11px; padding: 0 14px; background: linear-gradient(135deg, var(--success), #165c42) !important; box-shadow: 0 4px 12px rgba(16,185,129,0.18), inset 0 1px 0 rgba(255,255,255,0.3) !important; border-radius: 100px;">
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
        
        <form action="" method="POST" id="weighing-form" onsubmit="return validateWeighingForm(event)">
            @csrf
            
            <div style="background: var(--surface-soft); padding: 12px; border-radius: var(--r-sm); margin-bottom: 16px; font-size: 13px;">
                Komoditas: <strong id="weighing-crop-name">-</strong><br>
                Kuantitas Pengajuan Warga: <strong id="weighing-crop-qty">-</strong>
            </div>

            <div class="weighing-tabs" style="display: flex; gap: 8px; margin-bottom: 16px; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 10px;">
                <button type="button" class="weighing-tab-btn active" id="tab-upload-btn" onclick="switchWeighingTab('upload')">📁 Upload File</button>
                <button type="button" class="weighing-tab-btn" id="tab-webcam-btn" onclick="switchWeighingTab('webcam')">📸 Kamera Webcam</button>
            </div>

            <div id="weighing-upload-container">
                <div class="form-group">
                    <label for="scale-file-upload">Foto Timbangan Digital / Fisik (Wajib)</label>
                    <input type="file" id="scale-file-upload" class="text-input" accept="image/*" onchange="convertScaleImageToBase64(this)" style="padding-top: 6px;">
                </div>
            </div>

            <div id="weighing-webcam-container" style="display: none;">
                <div class="form-group">
                    <label>Ambil Foto Timbangan via Webcam</label>
                    <div style="position: relative; background: #000; border-radius: var(--r-md); overflow: hidden; width: 100%; height: 220px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <video id="weighing-video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); display: none;"></video>
                        <div id="webcam-placeholder" style="position: absolute; color: #fff; font-size: 13px; font-weight: 600; text-align: center; z-index: 5;">
                            🎥 Kamera belum aktif
                        </div>
                    </div>
                    <canvas id="weighing-canvas" style="display: none;"></canvas>
                    <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                        <button type="button" class="btn-3d-secondary" id="btn-toggle-camera" onclick="toggleWebcam()" style="flex: 1; font-size: 11px; height: 32px; border-radius: 100px;">
                            🔌 Aktifkan Kamera
                        </button>
                        <button type="button" class="btn-3d-primary" id="btn-capture-snapshot" onclick="captureWebcamSnapshot()" style="flex: 1; font-size: 11px; height: 32px; border-radius: 100px;" disabled>
                            📸 Ambil Snapshot
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="scale_image" id="form-scale-image">
            
            <div id="scale-preview-container" style="margin-top: 12px; display: none; text-align: center; background: var(--surface-soft); padding: 10px; border-radius: var(--r-md); border: 1px dashed var(--hairline);">
                <img id="scale-preview-img" src="" style="max-width: 100%; max-height: 160px; border-radius: var(--r-sm); border: 1px solid var(--hairline); object-fit: cover;">
                <button type="button" class="btn-3d-secondary" onclick="clearScalePreview()" style="color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important; font-size: 11px; padding: 2px 10px; margin-top: 6px; display: inline-flex; align-items: center; gap: 4px; border-radius: 100px;">🗑️ Hapus Gambar</button>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-3d-secondary" onclick="closeWeighingModal()" style="flex: 1; border-radius: 100px; height: 40px; font-size: 13px;">Batal</button>
                <button type="submit" class="btn-3d-primary" style="flex: 2; border-radius: 100px; height: 40px; font-size: 13px;">Konfirmasi Timbangan ➔</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Photo Scale Viewer Popup --}}
<div class="crop-modal-overlay" id="scale-viewer-overlay" onclick="closeScalePopup()" style="background: rgba(0,0,0,0.85);">
    <img src="" style="max-width: 90%; max-height: 80vh; border-radius: var(--r-md); box-shadow: var(--shadow-xl); transform: scale(0.9); transition: transform 0.3s var(--ease-spring);" id="scale-viewer-img" onclick="event.stopPropagation()">
</div>

<script>
    let webcamStream = null;

    // --- Tab Switcher ---
    function switchWeighingTab(tab) {
        document.querySelectorAll('.weighing-tab-btn').forEach(btn => btn.classList.remove('active'));
        if (tab === 'upload') {
            document.getElementById('tab-upload-btn').classList.add('active');
            document.getElementById('weighing-upload-container').style.display = 'block';
            document.getElementById('weighing-webcam-container').style.display = 'none';
            stopWebcam();
        } else {
            document.getElementById('tab-webcam-btn').classList.add('active');
            document.getElementById('weighing-upload-container').style.display = 'none';
            document.getElementById('weighing-webcam-container').style.display = 'block';
        }
    }

    // --- Webcam Logic ---
    function toggleWebcam() {
        if (webcamStream) {
            stopWebcam();
        } else {
            startWebcam();
        }
    }

    function startWebcam() {
        const video = document.getElementById('weighing-video');
        const placeholder = document.getElementById('webcam-placeholder');
        const btnToggle = document.getElementById('btn-toggle-camera');
        const btnCapture = document.getElementById('btn-capture-snapshot');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                webcamStream = stream;
                video.srcObject = stream;
                video.style.display = 'block';
                placeholder.style.display = 'none';
                btnToggle.textContent = '🔌 Matikan Kamera';
                btnCapture.disabled = false;
            })
            .catch(err => {
                console.error("Camera access failed:", err);
                alert("Gagal mengakses kamera. Silakan periksa izin kamera pada peramban Anda.");
            });
    }

    function stopWebcam() {
        const video = document.getElementById('weighing-video');
        const placeholder = document.getElementById('webcam-placeholder');
        const btnToggle = document.getElementById('btn-toggle-camera');
        const btnCapture = document.getElementById('btn-capture-snapshot');

        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        video.srcObject = null;
        video.style.display = 'none';
        placeholder.style.display = 'block';
        btnToggle.textContent = '🔌 Aktifkan Kamera';
        btnCapture.disabled = true;
    }

    function captureWebcamSnapshot() {
        const video = document.getElementById('weighing-video');
        const canvas = document.getElementById('weighing-canvas');
        const context = canvas.getContext('2d');

        if (video.videoWidth > 0 && video.videoHeight > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Mirror flip because preview is mirrored
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            // Reset transformation
            context.setTransform(1, 0, 0, 1, 0, 0);

            const base64String = canvas.toDataURL('image/jpeg');
            document.getElementById('form-scale-image').value = base64String;

            const previewImg = document.getElementById('scale-preview-img');
            const previewContainer = document.getElementById('scale-preview-container');
            previewImg.src = base64String;
            previewContainer.style.display = 'block';

            stopWebcam();
        }
    }

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
        const fileInput = document.getElementById('scale-file-upload');
        if (fileInput) fileInput.value = '';
        document.getElementById('scale-preview-img').src = '';
        document.getElementById('scale-preview-container').style.display = 'none';
    }

    function validateWeighingForm(e) {
        const scaleImage = document.getElementById('form-scale-image').value;
        if (!scaleImage) {
            alert('Harap unggah berkas timbangan atau ambil snapshot dengan kamera webcam terlebih dahulu.');
            e.preventDefault();
            return false;
        }
        stopWebcam();
        return true;
    }

    // --- Modal Open/Close ---
    function openWeighingModal(id, cropName, quantity) {
        const modal = document.getElementById('weighing-modal');
        const form = document.getElementById('weighing-form');
        
        form.action = `/staff/crops/${id}/received`;
        document.getElementById('weighing-crop-name').textContent = cropName;
        document.getElementById('weighing-crop-qty').textContent = quantity.toLocaleString('id-ID') + ' kg/unit';
        
        switchWeighingTab('upload');
        clearScalePreview();
        modal.classList.add('active');
    }

    function closeWeighingModal() {
        const modal = document.getElementById('weighing-modal');
        modal.classList.remove('active');
        stopWebcam();
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
