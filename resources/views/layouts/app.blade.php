<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KDKMP Digital Platform')</title>
    
    <!-- Design & Styling -->
    <link rel="stylesheet" href="{{ asset('css/airbnb.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
    <style>
        @media (max-width: 1024px) {
            .navbar-search { display: none !important; }
        }
        .navbar-search:focus-within {
            border-color: var(--ink) !important;
            background: var(--canvas) !important;
        }
        .navbar-search button:hover {
            color: var(--ink) !important;
        }
    </style>
</head>
<body>

    <!-- Header Navigation (DESIGN.md top-nav) -->
    <header class="top-nav">
        <div class="container nav-container">
            <!-- Brand Logo -->
            <a href="{{ route('catalog.index') }}" class="logo">
                <svg width="32" height="32" viewBox="0 0 32 32">
                    <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                </svg>
                <span>KDKMP Desa</span>
            </a>

            <!-- Village Switcher -->
            @php
                $globalBranches = \App\Models\Branch::all();
                $activeBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
                $activeBranch = $globalBranches->where('id', $activeBranchId)->first() ?? $globalBranches->first();
            @endphp
            @if($globalBranches->count() > 0)
                <div class="village-switcher no-print" style="position: relative; margin-left: 12px; font-family: var(--font);">
                    @auth
                        <!-- If logged in, show static badge of user's branch (cannot switch since bound to account) -->
                        <div style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1px solid var(--hairline-soft); border-radius: 100px; background: var(--surface); font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap;">
                            <span style="color: var(--primary);">📍</span> {{ $activeBranch->name }}
                        </div>
                    @else
                        <!-- If guest, show interactive dropdown selector -->
                        <form action="" id="branch-switch-form" method="POST" style="margin: 0; display: inline-flex; align-items: center;">
                            @csrf
                            <div style="position: relative; display: flex; align-items: center;">
                                <select onchange="this.form.action='{{ url('/catalog/set-branch') }}/' + this.value; this.form.submit();" style="appearance: none; -webkit-appearance: none; background: var(--surface); border: 1px solid var(--hairline); border-radius: 100px; padding: 6px 32px 6px 12px; font-size: 13px; font-weight: 600; color: var(--ink); cursor: pointer; outline: none; transition: all var(--t-fast); line-height: 1.5; font-family: var(--font); width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                    @foreach($globalBranches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch->id == $activeBranchId ? 'selected' : '' }}>
                                            📍 {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span style="position: absolute; right: 12px; pointer-events: none; font-size: 10px; color: var(--muted);">▼</span>
                            </div>
                        </form>
                    @endauth
                </div>
            @endif

            <!-- Navigation Links (Centered) -->
            <nav class="product-tabs">
                <a href="{{ route('catalog.index') }}" class="product-tab {{ Request::routeIs('catalog.index') ? 'active' : '' }}">
                    Gerai Retail
                </a>
                <a href="{{ route('catalog.agro') }}" class="product-tab {{ Request::routeIs('catalog.agro') ? 'active' : '' }}">
                    Dasbor Agro Tani
                </a>
                @auth
                    @if(auth()->user()->role === 'anggota')
                        <a href="{{ route('member.savings') }}" class="product-tab {{ Request::routeIs('member.savings') ? 'active' : '' }}">
                            Simpanan
                        </a>
                        <a href="{{ route('member.loans') }}" class="product-tab {{ Request::routeIs('member.loans') ? 'active' : '' }}">
                            Pinjaman
                        </a>
                        <a href="{{ route('member.crops') }}" class="product-tab {{ Request::routeIs('member.crops') ? 'active' : '' }}">
                            Penyerapan Tani
                        </a>
                        <a href="{{ route('member.orders') }}" class="product-tab {{ Request::routeIs('member.orders') ? 'active' : '' }}">
                            Belanja Saya
                        </a>
                    @endif
                @endauth
            </nav>

            <!-- Search Bar in Navbar (Hidden on mobile) -->
            <form action="{{ route('catalog.index') }}" method="GET" class="navbar-search no-print" style="display: flex; align-items: center; border: 1px solid var(--hairline); border-radius: var(--r-full); height: 38px; padding: 0 4px 0 12px; background: var(--surface); max-width: 240px; width: 100%; transition: border-color var(--t-fast); margin: 0 16px;">
                <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}" style="border: none; outline: none; background: transparent; font-size: 13px; color: var(--ink); width: 100%; font-family: var(--font);">
                <button type="submit" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; color: var(--muted); transition: color var(--t-fast);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display: block;">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>

            <!-- User Menu & Shopping Cart (Right Side) -->
            <div class="nav-right">
                <a href="{{ route('cart.index') }}" class="cart-icon-wrap" aria-label="Keranjang Belanja" data-tooltip="Keranjang Belanja">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    @if(session()->has('cart') && count(session('cart')) > 0)
                        <span id="cart-badge" class="cart-badge">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>

                @auth
                    @if(in_array(auth()->user()->role, ['admin', 'pengurus', 'kasir', 'staff']))
                        {{-- STAFF: show admin panel link + admin logout --}}
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="{{ route('staff.dashboard') }}" class="user-menu-pill">
                                <span style="font-size: 14px; font-weight: 500;">{{ auth()->user()->name }}</span>
                                <div class="avatar-circle" style="background: linear-gradient(135deg, #ff385c, #6c3de0);">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--muted); cursor: pointer;">Logout</button>
                            </form>
                        </div>
                    @else
                        {{-- ANGGOTA: show member dashboard link + member logout --}}
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="{{ route('member.dashboard') }}" class="user-menu-pill">
                                <span style="font-size: 14px; font-weight: 500;">{{ auth()->user()->name }}</span>
                                <div class="avatar-circle">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--muted); cursor: pointer;">Logout</button>
                            </form>
                        </div>
                    @endif
                @else
                    {{-- GUEST: Daftar Anggota (utama) + link kecil ke Admin Panel --}}
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <a href="{{ route('login') }}" class="product-tab" style="font-weight: 600;">Masuk</a>
                        <a href="{{ route('register') }}" class="product-tab" style="color: var(--primary); font-weight: 600;">Daftar Anggota</a>
                        <a href="{{ route('admin.login') }}" style="font-size: 12px; color: var(--muted); padding: 6px 12px; border: 1px solid var(--hairline-soft); border-radius: 100px; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.borderColor='var(--hairline)';this.style.color='var(--ink)'" onmouseout="this.style.borderColor='var(--hairline-soft)';this.style.color='var(--muted)'">⚙ Admin</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Section -->
    <main style="min-height: 70vh; padding: 32px 0;">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Scroll To Top Button -->
    <button id="scroll-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="Scroll ke atas">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <!-- Footer (DESIGN.md footer-light) -->
    <footer class="footer-light">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>KDKMP Digital</h4>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Halaman Profil Koperasi sedang dalam tahap penyusunan.', 'info')">Tentang Koperasi</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Informasi Syarat Keanggotaan sedang dalam tahap penyusunan.', 'info')">Keanggotaan</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Portal Berita Desa sedang dalam tahap penyusunan.', 'info')">Berita Desa</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="{{ route('catalog.index') }}">Gerai Sembako</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Informasi lengkap Tabungan Warga sedang dalam tahap penyusunan.', 'info')">Tabungan Warga</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Informasi lengkap Kredit Modal Panen sedang dalam tahap penyusunan.', 'info')">Kredit Modal Panen</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Bantuan & Kontak</h4>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Hubungi Pengurus', 'Anda dapat menghubungi pengurus desa di Balai {{ $activeBranch->name }} pada jam kerja.', 'info')">Hubungi Pengurus</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Buku Panduan Aplikasi sedang dalam tahap penyusunan.', 'info')">Panduan Aplikasi</a></li>
                        <li><a href="javascript:void(0)" onclick="window.showSweetAlert('Segera Hadir', 'Dokumen Syarat & Ketentuan sedang dalam tahap penyusunan.', 'info')">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="legal-band">
                <span>© 2026 Koperasi {{ $activeBranch->name }} (KDKMP {{ strtoupper($activeBranch->code) }}). All Rights Reserved.</span>
                <span>{{ $activeBranch->address ?? $activeBranch->name }}</span>
            </div>
        </div>
    </footer>

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
    // ── SweetAlert System ──────────────────────────────────────────
    let swalCallback = null;

    window.showSweetAlert = function(title, text, type = 'success', callback = null) {
        const overlay   = document.getElementById('custom-swal-overlay');
        const iconNode  = document.getElementById('swal-modal-icon');
        const titleNode = document.getElementById('swal-modal-title');
        const textNode  = document.getElementById('swal-modal-text');
        const topBar    = document.querySelector('.swal-modal::before');

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

    // ── Scroll-based Nav Shadow ───────────────────────────────────
    const nav = document.querySelector('header.top-nav');
    const scrollTopBtn = document.getElementById('scroll-to-top');

    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        if (nav) nav.classList.toggle('scrolled', y > 20);
        if (scrollTopBtn) scrollTopBtn.classList.toggle('visible', y > 300);
    }, { passive: true });

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
    document.querySelectorAll('.property-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect   = card.getBoundingClientRect();
            const cx     = rect.left + rect.width / 2;
            const cy     = rect.top  + rect.height / 2;
            const dx     = (e.clientX - cx) / (rect.width  / 2);
            const dy     = (e.clientY - cy) / (rect.height / 2);
            const rotX   = (-dy * 8).toFixed(2);
            const rotY   = (dx  * 8).toFixed(2);
            card.style.transform =
                `translateY(-10px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale(1.02)`;
            // Parallax photo
            const photo = card.querySelector('.property-card-photo img');
            if (photo) {
                photo.style.transform = `scale(1.08) translate(${dx * -5}px, ${dy * -5}px)`;
            }
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            const photo = card.querySelector('.property-card-photo img');
            if (photo) photo.style.transform = '';
        });
        card.addEventListener('mousedown', () => {
            card.style.transform = 'scale(0.97) translateY(-4px)';
        });
        card.addEventListener('mouseup', (e) => {
            const rect = card.getBoundingClientRect();
            const dx = (e.clientX - rect.left - rect.width/2) / (rect.width/2);
            const dy = (e.clientY - rect.top  - rect.height/2) / (rect.height/2);
            card.style.transform =
                `translateY(-10px) rotateX(${(-dy*8).toFixed(2)}deg) rotateY(${(dx*8).toFixed(2)}deg) scale(1.02)`;
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
            document.body.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            document.body.style.opacity = '0';
            document.body.style.transform = 'translateY(-8px)';
            setTimeout(() => { window.location.href = href; }, 260);
        });
    });

    // ── Stat Counter Animation ────────────────────────────────────
    function animateCounter(el) {
        const target = parseFloat(el.dataset.target || el.textContent.replace(/[^0-9.]/g, ''));
        const prefix = el.dataset.prefix || '';
        const suffix = el.dataset.suffix || '';
        const duration = 1200;
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

    // ── Stat Card hover pulse ─────────────────────────────────────
    document.querySelectorAll('.stat-card, .dashboard-nav-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.35s var(--ease-bounce), box-shadow 0.35s';
        });
    });

    // ── Body enter animation ──────────────────────────────────────
    document.body.style.opacity = '0';
    document.body.style.transform = 'translateY(8px)';
    document.body.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
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
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #1a7f5a; letter-spacing: 0.5px; text-transform: uppercase;">
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

    <!-- Mobile Bottom Navigation (App-like UI) -->
    <nav class="mobile-bottom-nav no-print">
        <div class="mobile-bottom-nav-inner">
            <a href="{{ route('catalog.index') }}" class="mobile-nav-item {{ Request::routeIs('catalog.index') || Request::routeIs('catalog.show') ? 'active' : '' }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ Request::routeIs('cart.index') ? 'active' : '' }}" style="position: relative;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span>Keranjang</span>
                @if(session()->has('cart') && count(session('cart')) > 0)
                    <span style="position: absolute; top: 4px; right: 20px; width: 8px; height: 8px; background: var(--primary); border-radius: 50%; border: 2px solid white;"></span>
                @endif
            </a>
            @auth
                @if(auth()->user()->role === 'anggota')
                    <a href="{{ route('member.orders') }}" class="mobile-nav-item {{ Request::routeIs('member.orders') || Request::routeIs('orders.show') ? 'active' : '' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span>Pesanan</span>
                    </a>
                    <a href="{{ route('member.dashboard') }}" class="mobile-nav-item {{ Request::routeIs('member.dashboard') || Request::routeIs('member.savings') || Request::routeIs('member.loans') || Request::routeIs('member.crops') ? 'active' : '' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Profil</span>
                    </a>
                @else
                    <a href="{{ route('staff.dashboard') }}" class="mobile-nav-item {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                        <span>Dasbor</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="mobile-nav-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    <span>Masuk</span>
                </a>
            @endauth
        </div>
    </nav>
</body>
</html>
