@extends('layouts.admin')

@section('title', 'Manajemen Anggota & Staf — KDKMP')
@section('page-title', 'Anggota & Staf')

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
    
    /* 3D Glass Cards for Members */
    .members-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: var(--r-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .members-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }
    
    .members-form-card {
        background: linear-gradient(135deg, #ffffff, #f8fafc) !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.02),
                    inset 0 1px 0 #ffffff !important;
        border-radius: var(--r-lg);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .members-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.08), inset 0 1px 0 #ffffff !important;
    }
    
    /* Role badges */
    .badge-role {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 100px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .badge-anggota { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-kasir { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    .badge-pengurus { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); }

    /* Custom popup image overlay */
    .ktp-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .ktp-overlay.active {
        display: flex;
        opacity: 1;
    }
    .ktp-modal-content {
        max-width: 90%;
        max-height: 80vh;
        border-radius: var(--r-md);
        box-shadow: var(--shadow-xl);
        transform: scale(0.9);
        transition: transform 0.3s var(--ease-spring);
    }
    .ktp-overlay.active .ktp-modal-content {
        transform: scale(1);
    }
    
    /* View-Specific 3D Polish Styles */
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
        border-radius: 100px;
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
    
    @keyframes emoji-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
</style>

<div class="reveal" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.5px;">Anggota <span style="color: var(--primary);">&amp; Staf</span></h1>
        <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
            <span style="color: var(--primary);">📍</span> {{ auth()->user()->branch->name }}
            <span class="badge badge-neutral">{{ $users->total() ?? 0 }} Akun</span>
        </p>
    </div>
    <a href="{{ route('staff.members.export') }}" class="btn btn-md btn-secondary btn-pill" data-no-loading>
        📥 Export CSV
    </a>
</div>

<div class="split-layout">
    
    <!-- Left: Members & Users List Table -->
    <div class="main-column">
        <div class="members-card card-flush" style="overflow: hidden;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid var(--hairline-soft); background: linear-gradient(to bottom, var(--surface-soft), var(--surface)); border-top-left-radius: var(--r-lg); border-top-right-radius: var(--r-lg);">
                <h3 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--ink); letter-spacing: -0.3px; display: flex; align-items: center; gap: 6px;">
                    <span style="animation: emoji-bounce 2s ease-in-out infinite;">👥</span> Daftar Anggota &amp; Staf
                </h3>
            </div>

            <!-- Search & Filter Panel -->
            <div style="padding: 14px 20px; background: var(--surface-soft); border-bottom: 1px solid var(--hairline-soft); display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <form action="{{ route('staff.members') }}" method="GET" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap; margin: 0; align-items: center;">
                    <div style="position: relative; flex: 1; min-width: 200px;">
                        <input type="text" name="search" placeholder="Cari nama, email, NIK, no. anggota..." value="{{ request('search') }}" class="text-input" style="height: 38px; padding-left: 12px; font-size: 13px; border-radius: 100px;">
                    </div>
                    <div style="width: 140px;">
                        <select name="role" class="form-select" style="height: 38px; font-size: 13px; padding: 0 10px; margin: 0; border-radius: 100px;" onchange="this.form.submit()">
                            <option value="">Semua Peran</option>
                            <option value="anggota" {{ request('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            <option value="kasir" {{ request('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                            <option value="pengurus" {{ request('role') == 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                        </select>
                    </div>
                    <div style="width: 140px;">
                        <select name="status" class="form-select" style="height: 38px; font-size: 13px; padding: 0 10px; margin: 0; border-radius: 100px;" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-3d-secondary" style="height: 38px; padding: 0 16px; font-size: 13px; border-radius: 100px;">Filter 🔍</button>
                    @if(request('search') || request('role') || request('status'))
                        <a href="{{ route('staff.members') }}" class="btn-3d-secondary" style="height: 38px; display: inline-flex; align-items: center; font-size: 13px; color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important; text-decoration: none; padding: 0 16px; border-radius: 100px;">Reset</a>
                    @endif
                </form>
            </div>
            
            @if($users->isEmpty())
                <div style="padding: 40px; text-align: center; color: var(--muted);">
                    Data tidak ditemukan. Silakan tambahkan anggota baru dengan panel di sebelah kanan.
                </div>
            @else
                <div class="clean-table-container">
                    <table class="clean-table" style="margin-top: 0;">
                        <thead style="background: var(--surface);">
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px;">
                                </th>
                                <th>Profil</th>
                                <th>Detail Anggota</th>
                                <th style="text-align: center;">Peran</th>
                                <th style="text-align: center;">KTP</th>
                                <th style="text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($user->id !== auth()->id())
                                            <input type="checkbox" class="row-checkbox" value="{{ $user->id }}" onchange="updateBulkActionBar()" style="cursor: pointer; width: 16px; height: 16px;">
                                        @else
                                            <span style="opacity: 0.3;" title="Diri Sendiri">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <div style="font-weight: 700; color: var(--ink); font-size: 14px;">
                                                {{ $user->name }}
                                                @if($user->id === auth()->id())
                                                    <span style="font-size: 10px; background: var(--info-bg); color: var(--info); padding: 1px 6px; border-radius: 4px; font-weight: 600; margin-left: 4px;">Anda</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">{{ $user->email }}</div>
                                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px;">
                                                @if($user->status === 'active')
                                                    <span style="font-size: 9px; font-weight: 700; background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); padding: 1px 6px; border-radius: 4px; text-transform: uppercase;">Aktif</span>
                                                @else
                                                    <span style="font-size: 9px; font-weight: 700; background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); padding: 1px 6px; border-radius: 4px; text-transform: uppercase;">Tidak Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->role === 'anggota' && $user->member)
                                            <div style="font-size: 12px; line-height: 1.5;">
                                                <div><strong>NIK:</strong> <span style="font-family: monospace; font-size: 12px;">{{ $user->member->nik }}</span></div>
                                                <div><strong>No:</strong> <span style="font-weight: 600; color: var(--primary);">{{ $user->member->nomor_anggota }}</span></div>
                                                <div><strong>WhatsApp:</strong> <span style="color: var(--success); font-weight: 600;">{{ $user->member->no_hp ?? '-' }}</span></div>
                                                <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--muted);" title="{{ $user->member->alamat_desa }}"><strong>Desa:</strong> {{ $user->member->alamat_desa }}</div>
                                                <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">🎯 Poin: <strong>{{ $user->member->total_poin }}</strong> | Bergabung: {{ $user->member->tanggal_bergabung->format('Y-m-d') }}</div>
                                            </div>
                                        @else
                                            <span style="color: var(--muted); font-size: 12px; font-style: italic;">Bukan data anggota</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <span class="badge-role badge-{{ $user->role }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($user->role === 'anggota' && $user->member && $user->member->ktp_image)
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                                <button type="button" class="btn-3d-secondary" onclick="showKtpPopup('{{ $user->member->ktp_image }}')" style="padding: 2px 10px; height: 26px; font-size: 11px; border-radius: 100px;">
                                                    👁️ Lihat
                                                </button>
                                                <a href="{{ $user->member->ktp_image }}" download="ktp_{{ $user->member->nik }}.png" class="btn-3d-secondary" style="padding: 2px 10px; height: 26px; font-size: 11px; border-radius: 100px; color: var(--success) !important; border-color: rgba(16,185,129,0.2) !important; background: #e6f7ed !important;" data-no-loading>
                                                    📥 Unduh
                                                </a>
                                            </div>
                                        @else
                                            <span style="color: var(--muted); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button type="button" class="btn-3d-secondary" style="border-radius: 100px; padding: 0 12px; height: 28px; font-size: 11px;"
                                            data-user="{{ json_encode($user) }}"
                                            data-member="{{ $user->member ? json_encode($user->member) : 'null' }}"
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
                @if($users->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid var(--hairline-soft); display: flex; justify-content: space-between; align-items: center; background: var(--surface-soft);">
                    <div style="font-size: 13px; color: var(--muted); font-weight: 600;">
                        Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} akun
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if($users->onFirstPage())
                            <span class="btn-3d-secondary" style="opacity: 0.5; pointer-events: none; height: 32px; padding: 0 14px; font-size: 12px; border-radius: 100px;">&laquo; Prev</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="btn-3d-secondary" style="height: 32px; padding: 0 14px; font-size: 12px; border-radius: 100px;">&laquo; Prev</a>
                        @endif

                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="btn-3d-secondary" style="height: 32px; padding: 0 14px; font-size: 12px; border-radius: 100px;">Next &raquo;</a>
                        @else
                            <span class="btn-3d-secondary" style="opacity: 0.5; pointer-events: none; height: 32px; padding: 0 14px; font-size: 12px; border-radius: 100px;">Next &raquo;</span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Right: Create/Edit Form Drawer -->
    <div class="sticky-rail">
        <div class="members-form-card" id="form-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--hairline-soft); padding-bottom: 16px; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--ink); margin: 0; display: flex; align-items: center; gap: 8px;" id="panel-title">
                    <span style="font-size: 18px; animation: emoji-bounce 2.5s infinite;">👤</span> Tambah Pengguna
                </h3>
                
                <!-- Dynamic Delete Button (Appears only on Edit) -->
                <form action="" method="POST" id="delete-form" style="display: none; margin: 0;" onsubmit="return confirm('Yakin ingin menghapus akun ini secara permanen?');">
                    @csrf
                    <button type="submit" class="btn-3d-secondary" style="height: 28px; padding: 0 12px; font-size: 11px; color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important; border-radius: 100px;" title="Hapus Akun Ini">🗑️ Hapus</button>
                </form>
            </div>
            
            <form action="{{ route('staff.members.store') }}" method="POST" id="user-form">
                @csrf
                
                <!-- Name -->
                <div class="form-group">
                    <label for="form-name">Nama Lengkap</label>
                    <input type="text" name="name" id="form-name" class="text-input" placeholder="Nama lengkap sesuai KTP" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="form-email">Alamat Email</label>
                    <input type="email" name="email" id="form-email" class="text-input" placeholder="name@domain.com" required>
                </div>

                <!-- Password -->
                <div class="form-group" style="position: relative;">
                    <label for="form-password" id="form-password-label">Kata Sandi</label>
                    <input type="password" name="password" id="form-password" class="text-input" placeholder="Min. 8 karakter" required style="padding-right: 40px;">
                    <button type="button" onclick="togglePasswordVisibility('form-password', this)" style="position: absolute; right: 12px; top: 34px; background: none; border: none; cursor: pointer; color: var(--muted); font-size: 14px; padding: 0;">👁️</button>
                </div>

                <!-- Role Selection -->
                <div class="form-group">
                    <label for="form-role">Peran / Hak Akses</label>
                    <select name="role" id="form-role" class="form-select" onchange="handleRoleChange(this.value)" required>
                        <option value="anggota">Anggota Koperasi (Warga)</option>
                        <option value="kasir">Kasir Ritel / POS</option>
                        <option value="pengurus">Pengurus / Admin Cabang</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="form-status">Status Akun</label>
                    <select name="status" id="form-status" class="form-select" required>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>

                <!-- ANGGOTA-SPECIFIC FIELDS -->
                <div id="anggota-fields-container" style="display: block; border-top: 1px dashed var(--hairline-soft); padding-top: 16px; margin-top: 16px; display: flex; flex-direction: column; gap: 16px;">
                    <!-- NIK -->
                    <div class="form-group" style="margin: 0;">
                        <label for="form-nik">NIK KTP (16 Digit)</label>
                        <input type="text" name="nik" id="form-nik" class="text-input" placeholder="3201xxxxxxxxxxxx" maxlength="16" minlength="16">
                    </div>

                    <!-- No. WhatsApp -->
                    <div class="form-group" style="margin: 0;">
                        <label for="form-no-hp">No. WhatsApp</label>
                        <input type="text" name="no_hp" id="form-no-hp" class="text-input" placeholder="Contoh: 081234567890">
                    </div>

                    <!-- Alamat Desa -->
                    <div class="form-group" style="margin: 0;">
                        <label for="form-alamat">Alamat Lengkap Desa</label>
                        <textarea name="alamat_desa" id="form-alamat" class="text-input" placeholder="RT/RW, Dusun, Desa" style="height: 80px; padding: 10px 12px; resize: none;"></textarea>
                    </div>

                    <!-- KTP Image Upload -->
                    <div class="form-group" style="margin: 0;">
                        <label for="ktp-file-upload">Foto KTP Anggota</label>
                        <input type="file" id="ktp-file-upload" class="text-input" accept="image/*" onchange="convertKtpToBase64(this)" style="padding-top: 8px; height: 44px;">
                        <input type="hidden" name="ktp_image" id="form-ktp-image">
                        
                        <div id="ktp-preview-container" style="margin-top: 12px; display: none; text-align: center; background: var(--surface-md); padding: 12px; border-radius: var(--r-md); border: 1.5px dashed var(--hairline-soft);">
                            <img id="ktp-preview-img" src="" style="max-width: 100%; max-height: 120px; border-radius: var(--r-sm); border: 1px solid var(--hairline-soft); object-fit: cover;">
                            <div style="margin-top: 8px;">
                                <button type="button" class="btn-3d-secondary" onclick="clearKtpPreview()" style="color: var(--danger) !important; border-color: rgba(220,38,38,0.2) !important; background: #fff0f3 !important; font-size: 11px; padding: 4px 12px; border-radius: 100px;">🗑️ Hapus KTP</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-3d-primary" id="form-submit-btn" style="width: 100%; height: 48px; border-radius: var(--r-sm); margin-top: 24px; font-size: 15px;">Simpan Akun</button>
                <button type="button" class="btn-3d-secondary" id="form-cancel-btn" style="display: none; margin-top: 12px; width: 100%; height: 42px; border-radius: var(--r-sm); font-size: 13.5px;" onclick="resetForm()">
                    Batal / Akun Baru
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Floating Bulk Action Bar -->
<div id="floating-bulk-bar" class="bulk-action-bar-floating no-print">
    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 18px; animation: emoji-bounce 2s ease-in-out infinite;">👥</span>
        <span style="font-size: 13px; font-weight: 700; color: white;">
            <span id="selected-count-float">0</span> akun terpilih
        </span>
    </div>
    <form action="{{ route('staff.members.bulk-delete') }}" method="POST" style="margin: 0; display: flex; align-items: center; gap: 8px;">
        @csrf
        <input type="hidden" name="ids" id="bulk-ids-input-float">
        <button type="submit" class="btn-3d-secondary" style="font-size: 11px; height: 32px; padding: 0 14px; border-radius: 100px; color: #ef4444 !important; border-color: rgba(239,68,68,0.2) !important; background: #fff0f3 !important; font-weight: 700;" onclick="return confirm('Yakin ingin menghapus akun terpilih?')">
            🗑️ Hapus Massal
        </button>
        <button type="button" class="btn-3d-secondary" style="font-size: 11px; height: 32px; padding: 0 14px; border-radius: 100px; color: white !important; background: rgba(255,255,255,0.15) !important; border-color: rgba(255,255,255,0.2) !important; box-shadow: none !important;" onclick="cancelBulkSelection()">
            Batal
        </button>
    </form>
</div>

<!-- Image Overlay KTP Viewer Popup -->
<div class="ktp-overlay" id="ktp-viewer-overlay" onclick="closeKtpPopup()">
    <img src="" class="ktp-modal-content" id="ktp-viewer-img" onclick="event.stopPropagation()">
</div>

<script>
    // --- Toggle Password Input Visibility ---
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }

    // --- Show/Hide Anggota Fields Based on Role ---
    function handleRoleChange(role) {
        const container = document.getElementById('anggota-fields-container');
        const nikInput = document.getElementById('form-nik');
        const alamatInput = document.getElementById('form-alamat');
        const noHpInput = document.getElementById('form-no-hp');
        
        if (role === 'anggota') {
            container.style.display = 'flex';
            nikInput.required = true;
            alamatInput.required = true;
            noHpInput.required = true;
        } else {
            container.style.display = 'none';
            nikInput.required = false;
            alamatInput.required = false;
            noHpInput.required = false;
        }
    }

    // --- Base64 KTP Upload Helper ---
    function convertKtpToBase64(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64String = e.target.result;
                document.getElementById('form-ktp-image').value = base64String;
                
                const previewImg = document.getElementById('ktp-preview-img');
                const previewContainer = document.getElementById('ktp-preview-container');
                previewImg.src = base64String;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearKtpPreview() {
        document.getElementById('form-ktp-image').value = '';
        document.getElementById('ktp-file-upload').value = '';
        document.getElementById('ktp-preview-img').src = '';
        document.getElementById('ktp-preview-container').style.display = 'none';
    }

    // --- KTP Image Modal Overlay ---
    function showKtpPopup(base64Src) {
        const overlay = document.getElementById('ktp-viewer-overlay');
        const img = document.getElementById('ktp-viewer-img');
        img.src = base64Src;
        overlay.classList.add('active');
    }

    function closeKtpPopup() {
        const overlay = document.getElementById('ktp-viewer-overlay');
        overlay.classList.remove('active');
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeKtpPopup();
    });

    // --- Load Edit Form ---
    function loadEditForm(btn) {
        const user = JSON.parse(btn.getAttribute('data-user'));
        const member = JSON.parse(btn.getAttribute('data-member'));
        
        document.getElementById('panel-title').textContent = '✏️ Edit: ' + user.name.substring(0, 15) + '...';
        document.getElementById('form-submit-btn').textContent = 'Perbarui Akun';
        document.getElementById('form-cancel-btn').style.display = 'inline-flex';
        
        // Show delete button (skip self account)
        const deleteForm = document.getElementById('delete-form');
        if (user.id !== {{ auth()->id() }}) {
            deleteForm.action = `/staff/members/${user.id}/delete`;
            deleteForm.style.display = 'block';
        } else {
            deleteForm.style.display = 'none';
        }
        
        const form = document.getElementById('user-form');
        form.action = `/staff/members/${user.id}/update`;
        
        document.getElementById('form-name').value = user.name;
        document.getElementById('form-email').value = user.email;
        
        // Password is optional during update
        const passwordInput = document.getElementById('form-password');
        passwordInput.required = false;
        passwordInput.placeholder = 'Biarkan kosong jika tidak diubah';
        document.getElementById('form-password-label').textContent = 'Kata Sandi Baru (Opsional)';
        
        document.getElementById('form-role').value = user.role;
        document.getElementById('form-status').value = user.status;
        
        // Handle member specific fields
        handleRoleChange(user.role);
        
        if (user.role === 'anggota' && member) {
            document.getElementById('form-nik').value = member.nik;
            document.getElementById('form-alamat').value = member.alamat_desa;
            document.getElementById('form-no-hp').value = member.no_hp || '';
            
            const ktpImage = member.ktp_image || '';
            document.getElementById('form-ktp-image').value = ktpImage;
            const previewImg = document.getElementById('ktp-preview-img');
            const previewContainer = document.getElementById('ktp-preview-container');
            
            if (ktpImage) {
                previewImg.src = ktpImage;
                previewContainer.style.display = 'block';
            } else {
                previewImg.src = '';
                previewContainer.style.display = 'none';
            }
        } else {
            document.getElementById('form-nik').value = '';
            document.getElementById('form-alamat').value = '';
            document.getElementById('form-no-hp').value = '';
            clearKtpPreview();
        }
        
        document.getElementById('form-panel').scrollIntoView({ behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('panel-title').textContent = '👤 Tambah Pengguna';
        document.getElementById('form-submit-btn').textContent = 'Simpan Akun';
        document.getElementById('form-cancel-btn').style.display = 'none';
        document.getElementById('delete-form').style.display = 'none';
        
        const form = document.getElementById('user-form');
        form.action = "{{ route('staff.members.store') }}";
        form.reset();
        
        const passwordInput = document.getElementById('form-password');
        passwordInput.required = true;
        passwordInput.placeholder = 'Min. 8 karakter';
        document.getElementById('form-password-label').textContent = 'Kata Sandi';
        
        document.getElementById('form-role').value = 'anggota';
        handleRoleChange('anggota');
        clearKtpPreview();
    }

    // Initialize on page load (starts with role: anggota)
    document.addEventListener('DOMContentLoaded', function() {
        handleRoleChange('anggota');
    });

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
