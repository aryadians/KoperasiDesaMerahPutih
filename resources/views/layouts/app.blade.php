<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KDKMP Digital Platform')</title>
    
    <!-- Design & Styling -->
    <link rel="stylesheet" href="{{ asset('css/airbnb.css') }}">
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
                <a href="{{ route('catalog.index') }}" class="product-tab {{ Request::routeIs('catalog.*') ? 'active' : '' }}">
                    Gerai Retail
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
                        <a href="{{ route('staff.dashboard') }}" class="product-tab {{ Request::routeIs('staff.*') ? 'active' : '' }}">
                            Dashboard Staf ({{ ucfirst(auth()->user()->role) }})
                        </a>
                    @endif
                @endauth
            </nav>

            <!-- User Menu & Shopping Cart (Right Side) -->
            <div class="nav-right">
                <a href="{{ route('cart.index') }}" style="position: relative; display: flex; align-items: center; padding: 10px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    @if(session()->has('cart') && count(session('cart')) > 0)
                        <span id="cart-badge" style="position: absolute; top: 0; right: 0; background-color: var(--colors-primary); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>

                @auth
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <a href="{{ route('dashboard') }}" class="user-menu-pill">
                            <span style="font-size: 14px; font-weight: 500;">{{ auth()->user()->name }}</span>
                            <div class="avatar-circle">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--colors-muted); cursor: pointer;">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('login') }}" class="product-tab" style="font-weight: 600;">Masuk</a>
                        <a href="{{ route('register') }}" class="product-tab" style="color: var(--colors-primary); font-weight: 600;">Daftar</a>
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

    <!-- Custom SweetAlert Overlay Structure -->
    <div class="swal-overlay" id="custom-swal-overlay">
        <div class="swal-modal">
            <div class="swal-icon" id="swal-modal-icon">✓</div>
            <h3 class="swal-title" id="swal-modal-title">Judul</h3>
            <p class="swal-text" id="swal-modal-text">Keterangan pesan dialog.</p>
            <div class="swal-buttons">
                <button type="button" class="button-primary" style="height: 40px; padding: 0 24px; width: auto;" onclick="closeSweetAlert()">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Scripting for Custom SweetAlert, Skeletons, and AJAX -->
    <script>
        let swalCallback = null;

        window.showSweetAlert = function(title, text, type = 'success', callback = null) {
            const overlay = document.getElementById('custom-swal-overlay');
            const iconNode = document.getElementById('swal-modal-icon');
            const titleNode = document.getElementById('swal-modal-title');
            const textNode = document.getElementById('swal-modal-text');

            swalCallback = callback;

            // Set content
            titleNode.textContent = title;
            textNode.textContent = text;

            // Reset icon types
            iconNode.className = 'swal-icon';
            if (type === 'success') {
                iconNode.classList.add('success');
                iconNode.textContent = '✓';
            } else if (type === 'error') {
                iconNode.classList.add('error');
                iconNode.textContent = '✕';
            } else if (type === 'warning') {
                iconNode.classList.add('warning');
                iconNode.textContent = '⚠';
            }

            // Show
            overlay.classList.add('active');
        };

        window.closeSweetAlert = function() {
            const overlay = document.getElementById('custom-swal-overlay');
            overlay.classList.remove('active');
            if (swalCallback) {
                swalCallback();
                swalCallback = null;
            }
        };

        // Automate Laravel Session Redirect alerts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.showSweetAlert('Berhasil', '{{ session('success') }}', 'success');
            @endif

            @if($errors->any())
                @php $errText = implode('\\n', $errors->all()); @endphp
                window.showSweetAlert('Terjadi Kesalahan', '{!! $errText !!}', 'error');
            @endif
        });
    </script>
</body>
</html>
