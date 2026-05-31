<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel — KDKMP')</title>
    
    <!-- Design & Styling -->
    <link rel="stylesheet" href="{{ asset('css/airbnb.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
    
    <style>
        /* Admin Sidebar Premium Styles - Bright SaaS Theme */
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #ffffff;
            --sidebar-border: var(--hairline);
            --sidebar-text: var(--muted);
            --sidebar-text-active: var(--primary);
            --sidebar-hover-bg: var(--surface);
            --admin-bg: #f8fafc;
        }

        body {
            background-color: var(--admin-bg) !important;
            display: flex;
            min-height: 100vh;
            flex-direction: row;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Ambient Glow Blobs in Background - Removed for clean look */
        .ambient-glow {
            display: none;
        }
/* Sidebar Container */
.admin-sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--sidebar-border);
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    transition: transform var(--t-base) var(--ease-out);
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
    overflow: hidden;
    flex: 0 0 var(--sidebar-width);
}

        /* Sidebar Brand & Logo */
        .sidebar-brand {
            padding: 24px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 18px;
            color: var(--ink);
            font-family: var(--font);
        }

        .sidebar-brand-link svg {
            fill: var(--primary);
            transition: transform 0.4s var(--ease-bounce);
        }
        .sidebar-brand-link:hover svg {
            transform: rotate(-12deg) scale(1.1);
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #10b981;
            background: var(--success-bg);
            padding: 4px 10px;
            border-radius: 100px;
            border: 1px solid var(--success-border);
        }
        
        .pulse-dot {
            width: 6px;
            height: 6px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-green 2s infinite;
        }
        
        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* User Profile in Sidebar */
        .sidebar-user {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--sidebar-border);
            background: var(--surface);
        }
        
        .sidebar-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #e8305a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            box-shadow: var(--shadow-sm);
        }
        
        .sidebar-user-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .sidebar-user-name {
            font-weight: 700;
            font-size: 14px;
            color: var(--ink);
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            font-family: var(--font);
        }
        
        .sidebar-user-role {
            font-size: 10px;
            color: var(--primary);
            font-weight: 700;
            display: inline-block;
            background: var(--primary-light);
            padding: 2px 8px;
            border-radius: 100px;
            width: fit-content;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: var(--font);
        }

        /* Sidebar Navigation Menu */
        .sidebar-menu {
            flex: 1;
            padding: 24px 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .sidebar-menu-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.5px;
            padding: 8px 12px 6px;
            font-family: var(--font);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: var(--r-md);
            color: var(--sidebar-text);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            cursor: pointer;
            font-family: var(--font);
        }
        
        .sidebar-link-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-link svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2.5;
            fill: none;
            transition: stroke 0.2s ease, transform 0.3s var(--ease-bounce);
        }
        
        .sidebar-link:hover {
            color: var(--ink);
            background: var(--sidebar-hover-bg);
        }
        .sidebar-link:hover svg {
            transform: scale(1.1);
        }
        
        .sidebar-link.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .sidebar-link.active svg {
            stroke: var(--primary);
        }

        .sidebar-badge {
            background: var(--primary);
            color: white;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 100px;
            box-shadow: 0 2px 8px var(--primary-glow);
        }

        /* POS Kasir Special Button Link */
        .sidebar-link.pos-special {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid var(--success-border);
            margin-top: 8px;
        }
        .sidebar-link.pos-special:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .sidebar-link.pos-special:hover svg {
            stroke: white;
        }

        /* Sidebar Footer Section */
        .sidebar-footer {
            padding: 20px 16px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: var(--surface);
        }

        .sidebar-footer-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--r-sm);
            font-size: 13px;
            font-weight: 600;
            color: var(--body);
            transition: all var(--t-fast);
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: var(--font);
        }
        
        .sidebar-footer-btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            stroke-width: 2.5;
            fill: none;
        }

        .sidebar-footer-btn:hover {
            color: var(--ink);
            background: var(--hairline-soft);
        }

        .sidebar-footer-btn.logout:hover {
            color: var(--danger);
            background: var(--danger-bg);
        }

        /* Right Side Content Frame */
        .admin-main {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 10;
        }

        /* Top Header Navigation for Admin Panel (Desktop/Mobile) */
        .admin-header {
            height: 72px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--hairline-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 4px 12px rgba(0,0,0,0.01);
        }
        
        .admin-header-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font);
        }

        .admin-header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Burger Menu for Mobile */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink);
            padding: 8px;
            border-radius: var(--r-sm);
            transition: background var(--t-fast);
        }
        
        .mobile-toggle:hover {
            background: var(--surface-md);
        }

        /* Content Container Wrap */
        .admin-content {
            padding: 40px;
            flex: 1;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            body.sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                padding: 0 20px;
            }

            .mobile-toggle {
                display: block;
            }

            .admin-content {
                padding: 24px 16px;
            }
            
            /* Background Overlay when Sidebar is open on Mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                z-index: 950;
                animation: fadeIn 0.25s ease;
            }
            
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Ambient glowing backgrounds -->
    <div class="ambient-glow glow-1 no-print"></div>
    <div class="ambient-glow glow-2 no-print"></div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay no-print" onclick="toggleSidebar(false)"></div>

    <!-- ── LEFT SIDEBAR ── -->
    <aside class="admin-sidebar no-print">
        <!-- Brand Logo Header -->
        <div class="sidebar-brand">
            <a href="{{ route('catalog.index') }}" class="sidebar-brand-link">
                <svg width="28" height="28" viewBox="0 0 32 32">
                    <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                </svg>
                <span>KDKMP Admin</span>
            </a>
            <div class="status-pill">
                <span class="pulse-dot"></span>
                <span>Aktif</span>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                <span class="sidebar-user-role">{{ auth()->user()->role }}</span>
                <span style="font-size: 10px; color: var(--muted); margin-top: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    📍 {{ auth()->user()->branch->name }}
                </span>
            </div>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="sidebar-menu">
            <span class="sidebar-menu-title">Menu Utama</span>
            
            <a href="{{ route('staff.dashboard') }}" class="sidebar-link {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    <span>Dasbor Ringkasan</span>
                </div>
            </a>

            <a href="{{ route('staff.analytics') }}" class="sidebar-link {{ Request::routeIs('staff.analytics') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20V14"></path></svg>
                        <span>Analitik Penjualan</span>
                    </div>
                </a>

                <a href="{{ route('staff.pos') }}" class="sidebar-link pos-special {{ Request::routeIs('staff.pos') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                        <span>🏪 POS Kasir</span>
                    </div>
                </a>

                <span class="sidebar-menu-title" style="margin-top: 14px;">Operasional Toko</span>

                <a href="{{ route('staff.products') }}" class="sidebar-link {{ Request::routeIs('staff.products') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M12.89 2.24L2 8l10.89 5.76L22 8zM2 17l10.89 5.76L22 17M2 12l10.89 5.76L22 12"></path></svg>
                        <span>Kelola Inventaris</span>
                    </div>
                </a>

                <a href="{{ route('staff.orders') }}" class="sidebar-link {{ Request::routeIs('staff.orders') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        <span>Pesanan Gerai</span>
                    </div>
                    @php $pendingOrders = \App\Models\Order::where('payment_status', 'pending')->count(); @endphp
                    @if($pendingOrders > 0)
                        <span class="sidebar-badge">{{ $pendingOrders }}</span>
                    @endif
                </a>

                <a href="{{ route('staff.crops') }}" class="sidebar-link {{ Request::routeIs('staff.crops') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        <span>Hasil Bumi Tani</span>
                    </div>
                    @php $pendingCrops = \App\Models\CropAbsorption::where('status', 'pending')->count(); @endphp
                    @if($pendingCrops > 0)
                        <span class="sidebar-badge">{{ $pendingCrops }}</span>
                    @endif
                </a>

                <span class="sidebar-menu-title" style="margin-top: 14px;">Keuangan & Sistem</span>

                <a href="{{ route('staff.loans') }}" class="sidebar-link {{ Request::routeIs('staff.loans') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M3 22h18M6 6h12M4 10h3v10H4zm7 0h3v10h-3zm7 0h3v10h-3zM12 2L2 6h20z"></path></svg>
                        <span>Simpan Pinjam</span>
                    </div>
                    @php $pendingLoans = \App\Models\Loan::where('status', 'pending')->count(); @endphp
                    @if($pendingLoans > 0)
                        <span class="sidebar-badge">{{ $pendingLoans }}</span>
                    @endif
                </a>

                <a href="{{ route('staff.purchase-orders') }}" class="sidebar-link {{ Request::routeIs('staff.purchase-orders') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Procurement (PO)</span>
                    </div>
                </a>

                <a href="{{ route('staff.config') }}" class="sidebar-link {{ Request::routeIs('staff.config') ? 'active' : '' }}">
                    <div class="sidebar-link-left">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span>Konfigurasi</span>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="{{ route('catalog.index') }}" class="sidebar-footer-btn">
                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span>Lihat Toko Publik</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST" id="sidebar-logout-form">
                @csrf
                <button type="submit" class="sidebar-footer-btn logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ── RIGHT MAIN WRAPPER ── -->
    <div class="admin-main">
        
        <!-- Header Bar -->
        <header class="admin-header">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="mobile-toggle no-print" onclick="toggleSidebar(true)" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="admin-header-title">
                    <span>Aplikasi Desa</span>
                    <span>/</span>
                    <strong style="color: var(--ink);">Panel Manajemen</strong>
                </div>
            </div>
            
            <div class="admin-header-right">
                <div style="font-size: 13px; color: var(--muted); font-weight: 500;" class="no-print">
                    {{ date('d M Y') }} &nbsp;·&nbsp; <span id="header-time">{{ date('H:i') }}</span>
                </div>
                <div class="avatar-circle" style="background: linear-gradient(135deg, #ff385c, #6c3de0); cursor: pointer;" data-tooltip="User profile">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    <!-- Custom SweetAlert Overlay -->
    <div class="swal-overlay" id="custom-swal-overlay" onclick="handleOverlayClick(event)">
        <div class="swal-modal" id="swal-modal-box">
            <div class="swal-icon" id="swal-modal-icon">✓</div>
            <h3 class="swal-title" id="swal-modal-title">Judul</h3>
            <p class="swal-text" id="swal-modal-text">Keterangan pesan dialog.</p>
            <div class="swal-buttons">
                <button type="button" class="button-primary" style="height: 44px; padding: 0 32px; width: auto; border-radius: 100px; font-size: 14px;" onclick="closeSweetAlert()">
                    OK, Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================
         GLOBAL ANIMATION ENGINE
         ============================================================ -->
    <script>
    // ── Mobile Sidebar Toggle ──────────────────────────────────────
    function toggleSidebar(open) {
        if (open) {
            document.body.classList.add('sidebar-open');
        } else {
            document.body.classList.remove('sidebar-open');
        }
    }
    
    // Live Clock Helper
    function updateClock() {
        const timeEl = document.getElementById('header-time');
        if (timeEl) {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            timeEl.textContent = `${h}:${m}`;
        }
    }
    setInterval(updateClock, 30000);

    // ── SweetAlert System ──────────────────────────────────────────
    let swalCallback = null;

    window.showSweetAlert = function(title, text, type = 'success', callback = null) {
        const overlay   = document.getElementById('custom-swal-overlay');
        const iconNode  = document.getElementById('swal-modal-icon');
        const titleNode = document.getElementById('swal-modal-title');
        const textNode  = document.getElementById('swal-modal-text');

        swalCallback = callback;
        titleNode.textContent = title;
        textNode.textContent  = text;

        iconNode.className = 'swal-icon';
        const icons = { success: ['✓','success'], error: ['✕','error'], warning: ['⚠','warning'], info: ['ℹ','info'] };
        const [icon, cls] = icons[type] || icons.success;
        iconNode.classList.add(cls);
        iconNode.textContent = icon;

        overlay.classList.add('active');
    };

    window.closeSweetAlert = function() {
        const overlay = document.getElementById('custom-swal-overlay');
        overlay.classList.remove('active');
        if (swalCallback) { swalCallback(); swalCallback = null; }
    };

    function handleOverlayClick(e) {
        if (e.target === e.currentTarget) closeSweetAlert();
    }

    // Keyboard close
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSweetAlert(); });

    // ── Laravel session alerts ─────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.showSweetAlert('Berhasil! 🎉', '{{ session('success') }}', 'success');
        @endif
        @if($errors->any())
            @php $errText = implode(' ', $errors->all()); @endphp
            window.showSweetAlert('Terjadi Kesalahan', '{!! addslashes($errText) !!}', 'error');
        @endif
    });

    // ── Scroll-Reveal (IntersectionObserver with Entrance & Exit) ──
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            } else {
                entry.target.classList.remove('revealed');
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });

    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-rotate').forEach(el => {
        revealObserver.observe(el);
    });

    // ── 3D Mouse Tilt on Product Cards ────────────────────────────
    document.querySelectorAll('.property-card, .stat-card, .dashboard-nav-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect   = card.getBoundingClientRect();
            const cx     = rect.left + rect.width / 2;
            const cy     = rect.top  + rect.height / 2;
            const dx     = (e.clientX - cx) / (rect.width  / 2);
            const dy     = (e.clientY - cy) / (rect.height / 2);
            const rotX   = (-dy * 6).toFixed(2);
            const rotY   = (dx  * 6).toFixed(2);
            card.style.transform =
                `translateY(-6px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale(1.01)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
        card.addEventListener('mousedown', () => {
            card.style.transform = 'scale(0.98) translateY(-2px)';
        });
        card.addEventListener('mouseup', (e) => {
            const rect = card.getBoundingClientRect();
            const dx = (e.clientX - rect.left - rect.width/2) / (rect.width/2);
            const dy = (e.clientY - rect.top  - rect.height/2) / (rect.height/2);
            card.style.transform =
                `translateY(-6px) rotateX(${(-dy*6).toFixed(2)}deg) rotateY(${(dx*6).toFixed(2)}deg) scale(1.01)`;
        });
    });

    // ── Ripple Effect on Buttons ──────────────────────────────────
    function createRipple(e) {
        const btn  = e.currentTarget;
        const rect = btn.getBoundingClientRect();
        const r    = Math.max(rect.width, rect.height);
        const x    = e.clientX - rect.left - r / 2;
        const y    = e.clientY - rect.top  - r / 2;
        const rip  = document.createElement('span');
        rip.className = 'ripple';
        Object.assign(rip.style, {
            width: r + 'px', height: r + 'px',
            left: x + 'px', top: y + 'px'
        });
        btn.style.position = 'relative';
        btn.style.overflow = 'hidden';
        btn.appendChild(rip);
        setTimeout(() => rip.remove(), 700);
    }
    document.querySelectorAll('.button-primary, .button-secondary, .quick-buy-btn, .search-orb')
        .forEach(btn => btn.addEventListener('click', createRipple));

    // ── Page Exit Transition ──────────────────────────────────────
    document.querySelectorAll('a[href]:not([href^="#"]):not([href^="mailto"]):not([target])').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || this.closest('form')) return;
            // Only animate for same-origin links
            try {
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;
            } catch { return; }
            e.preventDefault();
            document.body.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(-4px)';
            setTimeout(() => { window.location.href = href; }, 210);
        });
    });

    // ── Stat Counter Animation ────────────────────────────────────
    function animateCounter(el) {
        const target = parseFloat(el.dataset.target || el.textContent.replace(/[^0-9.]/g, ''));
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const duration = 1000;
        const start = performance.now();
        const startVal = 0;
        function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            const current = Math.round(easeOut(progress) * target);
            el.textContent = prefix + current.toLocaleString('id-ID') + suffix;
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = prefix + target.toLocaleString('id-ID') + suffix;
        }
        requestAnimationFrame(step);
    }

    const counterObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

    // ── Body enter animation ──────────────────────────────────────
    document.body.style.opacity = '0';
    document.body.style.transform = 'translateY(4px)';
    document.body.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.body.style.opacity = '1';
            document.body.style.transform = 'translateY(0)';
        });
    });
    </script>

    @if(session()->has('sms_notification'))
        <!-- Phone WhatsApp/SMS Notification Simulator Widget -->
        <div id="phone-sms-widget" class="no-print" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; max-width: 320px; width: 100%; transform: translateY(150%); opacity: 0; transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.6s ease; font-family: var(--font);">
            <div style="background: rgba(26, 26, 26, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); color: white; border-radius: var(--r-md); border: 1.5px solid rgba(255, 255, 255, 0.15); box-shadow: var(--shadow-xl); overflow: hidden;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05);">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #10b981; letter-spacing: 0.5px; text-transform: uppercase;">
                        <span style="font-size: 14px;">💬</span> WhatsApp Desa
                    </div>
                    <div style="font-size: 10px; color: rgba(255, 255, 255, 0.5); font-weight: 600;">Baru saja</div>
                </div>
                <div style="padding: 14px; display: flex; flex-direction: column; gap: 6px;">
                    <h4 style="font-size: 13px; font-weight: 700; color: white; margin: 0;">{{ session('sms_notification.title') }}</h4>
                    <p style="font-size: 11px; color: rgba(255, 255, 255, 0.85); line-height: 1.5; margin: 0;">{{ session('sms_notification.message') }}</p>
                </div>
                <div onclick="dismissSMSWidget()" style="text-align: center; padding: 8px; font-size: 10px; color: rgba(255, 255, 255, 0.5); cursor: pointer; border-top: 1px solid rgba(255, 255, 255, 0.05); font-weight: 600;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255, 255, 255, 0.5)'">
                    Sentuh untuk menutup
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const widget = document.getElementById('phone-sms-widget');
                
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    function playChime(freq, delay, duration) {
                        setTimeout(() => {
                            const osc = audioCtx.createOscillator();
                            const gain = audioCtx.createGain();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                            gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + duration);
                            osc.connect(gain);
                            gain.connect(audioCtx.destination);
                            osc.start();
                            osc.stop(audioCtx.currentTime + duration);
                        }, delay);
                    }
                    
                    playChime(880, 200, 0.15);
                    playChime(1046.5, 320, 0.25);
                } catch (e) {}

                setTimeout(() => {
                    widget.style.transform = 'translateY(0)';
                    widget.style.opacity = '1';
                }, 600);

                setTimeout(() => {
                    dismissSMSWidget();
                }, 8500);
            });

            function dismissSMSWidget() {
                const widget = document.getElementById('phone-sms-widget');
                if (widget) {
                    widget.style.transform = 'translateY(150%)';
                    widget.style.opacity = '0';
                    setTimeout(() => widget.remove(), 600);
                }
            }
        </script>
    @endif
</body>
</html>
