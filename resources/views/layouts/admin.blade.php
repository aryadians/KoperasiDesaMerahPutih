<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin — KDKMP Digital')</title>
    <meta name="description" content="Panel manajemen Koperasi Desa Merah Putih — KDKMP Digital System">
    <!-- TODO(security): Add X-Frame-Options and CSP headers via Laravel middleware -->

    <!-- Design & Styling -->
    <link rel="stylesheet" href="{{ asset('css/airbnb.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#C0392B">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <style>
        /* ── Admin Layout Variables ── */
        :root {
            --sidebar-w: 272px;
            --header-h:  72px;
            --admin-bg:  #F4F7FB;
        }

        /* ── Body Layout ── */
        body {
            background: var(--admin-bg) !important;
            display: flex;
            min-height: 100vh;
            flex-direction: row;
            overflow-x: hidden;
            margin: 0; padding: 0;
            font-family: var(--font);
            color: var(--ink);
        }

        /* ================================================================
           SIDEBAR — Premium Minimalist
           ================================================================ */
        .admin-sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            flex: 0 0 var(--sidebar-w);
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            transition: all var(--t-base) var(--ease-out);
        }

        /* ── Sidebar Brand ── */
        .sidebar-brand {
            padding: 24px 24px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: -0.3px;
            color: var(--ink);
            text-decoration: none;
        }
        .sidebar-brand-link svg { fill: var(--primary); transition: transform 0.4s var(--ease-bounce); }
        .sidebar-brand-link:hover svg { transform: rotate(-12deg) scale(1.1); }
        .sidebar-brand-name { color: var(--primary); }
        .sidebar-brand-sub { color: var(--muted); font-size: 12.5px; font-weight: 700; letter-spacing: 0.5px; }

        /* Status pill */
        .status-pill {
            display: flex; align-items: center; gap: 6px;
            font-size: 10.5px; font-weight: 800;
            color: var(--success); background: rgba(16, 185, 129, 0.1);
            padding: 4px 10px; border-radius: var(--r-full);
        }
        .pulse-dot {
            width: 6px; height: 6px;
            background: var(--success); border-radius: 50%;
            animation: pulse-green 2s infinite;
        }

        /* ── Sidebar User Profile ── */
        .sidebar-user {
            padding: 20px 24px;
            display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            background: linear-gradient(to bottom, #ffffff, #fafafa);
        }
        .sidebar-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #e11d48 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.2), inset 0 1px 0 rgba(255,255,255,0.3);
        }
        .sidebar-user-info { display: flex; flex-direction: column; overflow: hidden; }
        .sidebar-user-name {
            font-weight: 800; font-size: 14.5px; color: var(--ink);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .sidebar-user-role {
            font-size: 10px; color: var(--primary); font-weight: 800;
            background: var(--primary-light);
            padding: 3px 8px; border-radius: var(--r-full);
            width: fit-content; margin-bottom: 4px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .sidebar-user-branch {
            font-size: 11px; color: var(--muted);
            font-weight: 600; display: flex; align-items: center; gap: 4px;
        }

        /* ── Sidebar Navigation ── */
        .sidebar-menu {
            flex: 1;
            padding: 20px 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 4px;
            scrollbar-width: thin;
        }

        .sidebar-menu-title {
            font-size: 10.5px; font-weight: 800;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--muted-light);
            padding: 16px 12px 6px;
        }

        .sidebar-link {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 14px; border-radius: var(--r-md);
            color: var(--muted); font-weight: 600; font-size: 13.5px;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer; text-decoration: none;
            position: relative; overflow: hidden;
        }
        .sidebar-link-left { display: flex; align-items: center; gap: 12px; }
        .sidebar-link svg {
            width: 18px; height: 18px;
            stroke: currentColor; stroke-width: 2.2; fill: none;
            flex-shrink: 0;
            transition: transform 0.3s var(--ease-bounce);
        }
        .sidebar-link:hover {
            color: var(--ink);
            background: var(--surface-soft);
            transform: translateX(4px);
        }
        .sidebar-link:hover svg { transform: scale(1.1); }
        .sidebar-link.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 800;
        }
        .sidebar-link.active svg { stroke: var(--primary); }

        /* POS Special Button */
        .sidebar-link.pos-special {
            background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(16,185,129,0.05));
            color: var(--success);
            border: 1px solid rgba(16,185,129,0.2);
            margin: 8px 0;
            font-weight: 800;
        }
        .sidebar-link.pos-special:hover {
            background: var(--success);
            color: white;
            border-color: var(--success);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.25);
        }
        .sidebar-link.pos-special:hover svg { stroke: white; }

        .sidebar-badge {
            background: var(--primary);
            color: white; font-size: 10.5px; font-weight: 800;
            padding: 3px 8px; border-radius: var(--r-full);
            min-width: 20px; text-align: center;
            box-shadow: 0 4px 10px rgba(225, 29, 72, 0.25);
        }

        /* ── Sidebar Footer ── */
        .sidebar-footer {
            padding: 16px 20px 20px;
            border-top: 1px solid rgba(0,0,0,0.04);
            display: flex; flex-direction: column; gap: 8px;
            background: #ffffff;
        }
        .sidebar-footer-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: var(--r-md);
            font-size: 13.5px; font-weight: 700; color: var(--muted);
            transition: all var(--t-fast);
            cursor: pointer; border: none; background: none;
            width: 100%; text-align: left; font-family: var(--font);
            text-decoration: none;
        }
        .sidebar-footer-btn svg {
            width: 16px; height: 16px;
            stroke: currentColor; stroke-width: 2.2; fill: none; flex-shrink: 0;
        }
        .sidebar-footer-btn:hover { color: var(--ink); background: var(--surface-soft); }
        .sidebar-footer-btn.logout:hover { color: var(--danger); background: var(--danger-bg); }

        /* ================================================================
           MAIN CONTENT AREA
           ================================================================ */
        .admin-main {
            flex: 1; min-width: 0; min-height: 100vh;
            display: flex; flex-direction: column;
            position: relative; z-index: 10;
        }

        /* ── Top Header Bar ── */
        .admin-header {
            height: var(--header-h);
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px;
            position: sticky; top: 0; z-index: 900;
        }
        .admin-header-title {
            font-size: 15px; font-weight: 600; color: var(--muted);
            display: flex; align-items: center; gap: 10px;
        }
        .admin-header-title strong { color: var(--ink); font-weight: 800; letter-spacing: -0.2px; }
        .admin-header-right { display: flex; align-items: center; gap: 20px; }

        /* ── Page Content ── */
        .admin-content {
            padding: 40px 48px; flex: 1; width: 100%;
            min-width: 0; box-sizing: border-box;
        }

        /* ── Live Clock Badge ── */
        .header-clock {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: 700; color: var(--muted);
            padding: 6px 14px; border-radius: var(--r-full);
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .header-clock-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.2);
        }

        /* ── Notification Bell ── */
        .header-bell {
            position: relative; width: 44px; height: 44px;
            background: #ffffff; border: 1px solid rgba(0,0,0,0.08);
            border-radius: 50%; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--ink); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .header-bell:hover {
            background: var(--primary-light); border-color: var(--primary-muted);
            color: var(--primary); transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.15);
        }
        .header-bell svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; }

        /* Premium Card Styles for Admin */
        .admin-card {
            background: #ffffff;
            border-radius: var(--r-xl);
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05), inset 0 1px 0 #ffffff;
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px -12px rgba(0,0,0,0.08), inset 0 1px 0 #ffffff;
        }

        /* Premium Stat Card */
        .stat-card {
            background: #ffffff;
            border-radius: var(--r-xl);
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04), inset 0 1px 0 #ffffff;
            padding: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--primary);
            opacity: 0.8;
        }
        .stat-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.1), inset 0 1px 0 #ffffff;
            border-color: rgba(0, 0, 0, 0.08);
        }
        .stat-label {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.1;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .stat-desc {
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.4;
        }
        .stat-icon {
            position: absolute;
            top: 24px;
            right: 24px;
            font-size: 28px;
            opacity: 0.15;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .stat-card:hover .stat-icon {
            transform: scale(1.15) rotate(10deg);
            opacity: 0.3;
        }

        /* Clean Table Polish */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .clean-table th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid rgba(0,0,0,0.06);
            padding: 16px 20px;
            text-align: left;
        }
        .clean-table td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            color: var(--ink);
            vertical-align: middle;
        }
        .clean-table tbody tr {
            transition: all 0.2s ease;
        }
        .clean-table tbody tr:hover {
            background: #f1f5f9;
        }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.38);
            backdrop-filter: blur(4px);
            z-index: 950;
            animation: fadeIn 0.22s ease;
        }
        body.sidebar-open .sidebar-overlay { display: block; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .admin-sidebar {
                position: fixed; left: 0; top: 0; bottom: 0;
                transform: translateX(-100%);
                transition: transform var(--t-base) var(--ease-out);
                z-index: 1000;
            }
            body.sidebar-open .admin-sidebar { transform: translateX(0); }
            .admin-header { padding: 0 20px; }
            .mobile-toggle { display: block; }
            .admin-content { padding: 24px 16px; }
            .header-clock { display: none; }
        }
        @media (max-width: 640px) {
            .admin-content { padding: 16px 12px; }
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
@php
    $unreadNotifications = [];
    if (Auth::check()) {
        $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();
    }
@endphp
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay no-print" onclick="toggleSidebar(false)"></div>

    <!-- ── LEFT SIDEBAR ── -->
    <aside class="admin-sidebar no-print">
        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="{{ route('catalog.index') }}" class="sidebar-brand-link">
                <svg width="26" height="26" viewBox="0 0 32 32">
                    <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                </svg>
                <div>
                    <div class="sidebar-brand-name">KDKMP</div>
                    <div class="sidebar-brand-sub">Panel Admin</div>
                </div>
            </a>
            <div class="status-pill">
                <span class="pulse-dot"></span>
                <span>Aktif</span>
            </div>
        </div>

        <!-- User Profile -->
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                <span class="sidebar-user-role">{{ auth()->user()->role }}</span>
                <span class="sidebar-user-branch">📍 {{ auth()->user()->branch->name }}</span>
            </div>
        </div>

        <!-- Navigation Menu -->
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
                    <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <span>🏪 POS Kasir</span>
                </div>
            </a>

            <span class="sidebar-menu-title" style="margin-top: 10px;">Operasional Toko</span>

            @if(in_array(auth()->user()->role, ['admin']))
            <a href="{{ route('staff.products') }}" class="sidebar-link {{ Request::routeIs('staff.products') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    <span>Kelola Inventaris</span>
                </div>
            </a>
            @endif

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

            @if(in_array(auth()->user()->role, ['admin', 'pengurus']))
            <a href="{{ route('staff.crops') }}" class="sidebar-link {{ Request::routeIs('staff.crops') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 1 10 10c0 5.52-4.48 10-10 10S2 17.52 2 12c0-2.76 1.12-5.26 2.93-7.07"></path><path d="M12 6v6l4 2"></path></svg>
                    <span>Hasil Bumi Tani</span>
                </div>
                @php $pendingCrops = \App\Models\CropAbsorption::where('status', 'pending')->count(); @endphp
                @if($pendingCrops > 0)
                    <span class="sidebar-badge">{{ $pendingCrops }}</span>
                @endif
            </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'pengurus']))
            <span class="sidebar-menu-title" style="margin-top: 10px;">Keuangan & Laporan</span>

            <a href="{{ route('staff.loans') }}" class="sidebar-link {{ Request::routeIs('staff.loans') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
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
            @endif

            @if(in_array(auth()->user()->role, ['admin']))
            <span class="sidebar-menu-title" style="margin-top: 10px;">Manajemen Sistem</span>

            <a href="{{ route('staff.members') }}" class="sidebar-link {{ Request::routeIs('staff.members') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>Daftar Anggota</span>
                </div>
            </a>

            <a href="{{ route('staff.shu') }}" class="sidebar-link {{ Request::routeIs('staff.shu') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"></path></svg>
                    <span>Laporan SHU</span>
                </div>
            </a>

            <a href="{{ route('staff.config') }}" class="sidebar-link {{ Request::routeIs('staff.config') ? 'active' : '' }}">
                <div class="sidebar-link-left">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9"></path></svg>
                    <span>Konfigurasi Sistem</span>
                </div>
            </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="{{ route('catalog.index') }}" class="sidebar-footer-btn">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>Lihat Toko Publik</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST" id="sidebar-logout-form" style="width:100%;">
                @csrf
                <button type="submit" class="sidebar-footer-btn logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Keluar Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ── RIGHT MAIN AREA ── -->
    <div class="admin-main">

        <!-- Top Header Bar -->
        <header class="admin-header">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="mobile-toggle no-print" onclick="toggleSidebar(true)" aria-label="Toggle Sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="admin-header-title">
                    <span>KDKMP</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    <strong>@yield('page-title', 'Panel Manajemen')</strong>
                </div>
            </div>

            <div class="admin-header-right">
                <div class="header-clock no-print">
                    <span class="header-clock-dot"></span>
                    <span>{{ date('d M Y') }}</span>
                    <span>·</span>
                    <span id="header-time">{{ date('H:i') }}</span>
                </div>
                <!-- Notifications Bell Dropdown -->
                <div class="notification-dropdown-wrap no-print" style="margin-right: 4px;">
                    <button class="notification-btn" aria-label="Notifikasi" onclick="toggleNotificationDropdown(event)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        @if(count($unreadNotifications) > 0)
                            <span class="notification-badge">{{ count($unreadNotifications) }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown" id="notification-dropdown-panel" style="right: -40px;">
                        <div class="notification-header">
                            <h4>Notifikasi Terbaru</h4>
                            @if(count($unreadNotifications) > 0)
                                <button onclick="markAllNotificationsAsRead(event)">Tandai Semua Dibaca</button>
                            @endif
                        </div>
                        <div class="notification-body">
                            @forelse($unreadNotifications as $n)
                                <div class="notification-item unread">
                                    <div class="notification-item-title">{{ $n->title }}</div>
                                    <p class="notification-item-desc">{{ $n->message }}</p>
                                    <span class="notification-item-time">{{ $n->created_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <div class="notification-empty">Tidak ada notifikasi baru.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="avatar-circle" style="cursor: pointer;" data-tooltip="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Main Content -->
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
                <button type="button" class="btn btn-primary btn-pill" style="height: 44px; padding: 0 32px; font-size: 14px;" onclick="closeSweetAlert()">
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
        document.body.classList.toggle('sidebar-open', open);
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') toggleSidebar(false);
    });

    // ── Live Clock ─────────────────────────────────────────────────
    function updateClock() {
        const el = document.getElementById('header-time');
        if (el) {
            const now = new Date();
            el.textContent = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
        }
    }
    setInterval(updateClock, 30000);

    // ── SweetAlert ─────────────────────────────────────────────────
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
        document.getElementById('custom-swal-overlay').classList.remove('active');
        if (swalCallback) { swalCallback(); swalCallback = null; }
    };

    function handleOverlayClick(e) {
        if (e.target === e.currentTarget) closeSweetAlert();
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSweetAlert(); });

    // ── Session Alerts ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.showSweetAlert('Berhasil! 🎉', '{{ session('success') }}', 'success');
        @endif
        @if($errors->any())
            @php $errText = implode(' ', $errors->all()); @endphp
            window.showSweetAlert('Terjadi Kesalahan', '{!! addslashes($errText) !!}', 'error');
        @endif
    });

    // ── Scroll-Reveal ──────────────────────────────────────────────
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('revealed', entry.isIntersecting);
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });

    document.querySelectorAll('.reveal, .reveal-up, .reveal-left, .reveal-right, .reveal-scale').forEach(el => {
        revealObserver.observe(el);
    });

    // ── 3D Tilt on Cards ──────────────────────────────────────────
    document.querySelectorAll('.stat-card, .dashboard-nav-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const dx   = (e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2);
            const dy   = (e.clientY - rect.top  - rect.height / 2) / (rect.height / 2);
            card.style.transform = `translateY(-5px) rotateX(${(-dy * 5).toFixed(2)}deg) rotateY(${(dx * 5).toFixed(2)}deg) scale(1.01)`;
        });
        card.addEventListener('mouseleave', () => { card.style.transform = ''; });
        card.addEventListener('mousedown',  () => { card.style.transform = 'scale(0.98)'; });
        card.addEventListener('mouseup',    () => { card.style.transform = ''; });
    });

    // ── Ripple Effect ─────────────────────────────────────────────
    function createRipple(e) {
        const btn  = e.currentTarget;
        const rect = btn.getBoundingClientRect();
        const r    = Math.max(rect.width, rect.height);
        const x    = e.clientX - rect.left - r / 2;
        const y    = e.clientY - rect.top  - r / 2;
        const rip  = document.createElement('span');
        rip.className = 'ripple';
        Object.assign(rip.style, { width: r + 'px', height: r + 'px', left: x + 'px', top: y + 'px' });
        btn.appendChild(rip);
        setTimeout(() => rip.remove(), 600);
    }
    document.querySelectorAll('.btn, .button-primary, .button-secondary').forEach(btn => {
        btn.addEventListener('click', createRipple);
    });

    // ── Page Exit Transition ──────────────────────────────────────
    document.querySelectorAll('a[href]:not([href^="#"]):not([href^="mailto"]):not([target])').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || this.closest('form')) return;
            try {
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;
            } catch { return; }
            e.preventDefault();
            document.body.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(-4px)';
            setTimeout(() => { window.location.href = href; }, 190);
        });
    });

    // ── Counter Animation ─────────────────────────────────────────
    function animateCounter(el) {
        const target   = parseFloat(el.dataset.target || el.textContent.replace(/[^0-9.]/g, ''));
        const prefix   = el.dataset.prefix || '';
        const suffix   = el.dataset.suffix || '';
        const duration = 1000;
        const start    = performance.now();
        function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
        function step(now) {
            const p = Math.min((now - start) / duration, 1);
            const v = Math.round(easeOut(p) * target);
            el.textContent = prefix + v.toLocaleString('id-ID') + suffix;
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = prefix + target.toLocaleString('id-ID') + suffix;
        }
        requestAnimationFrame(step);
    }
    const counterObs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { animateCounter(e.target); counterObs.unobserve(e.target); }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-counter]').forEach(el => counterObs.observe(el));
    </script>

    @if(session()->has('sms_notification'))
        <!-- SMS/WhatsApp Notification Widget -->
        <div id="phone-sms-widget" class="no-print" style="position:fixed;bottom:24px;right:24px;z-index:9999;max-width:320px;width:100%;transform:translateY(150%);opacity:0;transition:transform 0.6s cubic-bezier(0.34,1.56,0.64,1),opacity 0.6s ease;font-family:var(--font);">
            <div style="background:rgba(13,13,13,0.96);backdrop-filter:blur(16px);color:white;border-radius:var(--r-lg);border:1.5px solid rgba(255,255,255,0.1);box-shadow:var(--shadow-2xl);overflow:hidden;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.04);">
                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:#25D366;letter-spacing:0.5px;text-transform:uppercase;">
                        <span style="font-size:14px;">💬</span> WhatsApp Desa
                    </div>
                    <div style="font-size:10px;color:rgba(255,255,255,0.4);font-weight:600;">Baru saja</div>
                </div>
                <div style="padding:14px;display:flex;flex-direction:column;gap:6px;">
                    <h4 style="font-size:13px;font-weight:700;color:white;margin:0;">{{ session('sms_notification.title') }}</h4>
                    <p style="font-size:11.5px;color:rgba(255,255,255,0.8);line-height:1.5;margin:0;">{{ session('sms_notification.message') }}</p>
                </div>
                <div onclick="dismissSMSWidget()" style="text-align:center;padding:8px;font-size:10px;color:rgba(255,255,255,0.4);cursor:pointer;border-top:1px solid rgba(255,255,255,0.05);font-weight:700;transition:color 0.15s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                    Ketuk untuk menutup
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const widget = document.getElementById('phone-sms-widget');
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    [880, 1046.5].forEach((f, i) => {
                        setTimeout(() => {
                            const o = ctx.createOscillator(), g = ctx.createGain();
                            o.type = 'sine'; o.frequency.value = f;
                            g.gain.setValueAtTime(0.1, ctx.currentTime);
                            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
                            o.connect(g); g.connect(ctx.destination);
                            o.start(); o.stop(ctx.currentTime + 0.2);
                        }, 200 + i * 120);
                    });
                } catch(e) {}
                setTimeout(() => { widget.style.transform = 'translateY(0)'; widget.style.opacity = '1'; }, 600);
                setTimeout(dismissSMSWidget, 8500);
            });
            function dismissSMSWidget() {
                const w = document.getElementById('phone-sms-widget');
                if (w) { w.style.transform = 'translateY(150%)'; w.style.opacity = '0'; setTimeout(() => w.remove(), 600); }
            }
        </script>
    @endif

    <!-- Notification Scripts -->
    <script>
        function toggleNotificationDropdown(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-dropdown-panel');
            if (panel) {
                panel.classList.toggle('active');
            }
        }

        function markAllNotificationsAsRead(event) {
            event.preventDefault();
            event.stopPropagation();
            
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badges = document.querySelectorAll('.notification-badge');
                    badges.forEach(b => b.remove());
                    
                    const unreadItems = document.querySelectorAll('.notification-item.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        item.style.borderLeft = 'none';
                    });
                    
                    const clearBtn = event.target;
                    if (clearBtn) clearBtn.remove();
                    
                    window.showSweetAlert('Berhasil', 'Semua notifikasi telah ditandai dibaca.', 'success');
                }
            })
            .catch(err => {
                console.error('Error marking notifications read:', err);
            });
        }

        document.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-dropdown-panel');
            if (panel && panel.classList.contains('active')) {
                if (!event.target.closest('.notification-dropdown-wrap')) {
                    panel.classList.remove('active');
                }
            }
        });
    </script>

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function(registration) {
                        // SW registered successfully — no console.log in production
                    })
                    .catch(function() {
                        // SW registration failed — app still works without it
                    });
            });
        }
    </script>
</body>
</html>
