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
                    @else
                        <a href="{{ route('staff.dashboard') }}" class="product-tab {{ Request::routeIs('staff.dashboard') ? 'active' : '' }}">
                            Dashboard Staf
                        </a>
                        <a href="{{ route('staff.pos') }}" class="product-tab {{ Request::routeIs('staff.pos') ? 'active' : '' }}" style="color: var(--primary); font-weight: 600;">
                            🏪 POS Kasir
                        </a>
                    @endif
                @endauth
            </nav>

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
                                <button type="submit" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--colors-muted); cursor: pointer;">Logout</button>
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
                                <button type="submit" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--colors-muted); cursor: pointer;">Logout</button>
                            </form>
                        </div>
                    @endif
                @else
                    {{-- GUEST: Daftar Anggota (utama) + link kecil ke Admin Panel --}}
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <a href="{{ route('login') }}" class="product-tab" style="font-weight: 600;">Masuk</a>
                        <a href="{{ route('register') }}" class="product-tab" style="color: var(--colors-primary); font-weight: 600;">Daftar Anggota</a>
                        <a href="{{ route('admin.login') }}" style="font-size: 12px; color: var(--colors-muted); padding: 6px 12px; border: 1px solid var(--colors-hairline-soft); border-radius: 100px; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.borderColor='var(--colors-hairline)';this.style.color='var(--colors-ink)'" onmouseout="this.style.borderColor='var(--colors-hairline-soft)';this.style.color='var(--colors-muted)'">⚙ Admin</a>
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
                        <li><a href="#">Tentang Koperasi</a></li>
                        <li><a href="#">Keanggotaan</a></li>
                        <li><a href="#">Berita Desa</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="{{ route('catalog.index') }}">Gerai Sembako</a></li>
                        <li><a href="#">Tabungan Warga</a></li>
                        <li><a href="#">Kredit Modal Panen</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Bantuan & Kontak</h4>
                    <ul>
                        <li><a href="#">Hubungi Pengurus</a></li>
                        <li><a href="#">Panduan Aplikasi</a></li>
                        <li><a href="#">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="legal-band">
                <span>© 2026 Koperasi Desa Merah Putih (KDKMP). All Rights Reserved.</span>
                <span>Desa Merah Putih, Indonesia</span>
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
</body>
</html>
