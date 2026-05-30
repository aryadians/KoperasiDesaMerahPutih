<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel — KDKMP Digital System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #ff385c;
            --primary-dark: #c0163c;
            --gold: #f5a623;
            --gold-dark: #d4891a;
            --dark-bg: #0a0a0f;
            --panel-bg: rgba(255,255,255,0.04);
            --panel-border: rgba(255,255,255,0.10);
            --text: #f0f0f0;
            --text-muted: rgba(255,255,255,0.5);
            --input-bg: rgba(255,255,255,0.07);
            --input-border: rgba(255,255,255,0.12);
            --input-focus: rgba(255,255,255,0.22);
            --error-bg: rgba(255,56,92,0.15);
            --error-border: rgba(255,56,92,0.4);
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--dark-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ===================== ANIMATED BACKGROUND ===================== */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        /* Radial glow orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float-orb 12s ease-in-out infinite alternate;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #ff385c 0%, transparent 70%);
            top: -100px; left: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #6c3de0 0%, transparent 70%);
            bottom: -80px; right: -80px;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #f5a623 0%, transparent 70%);
            top: 50%; left: 55%;
            opacity: 0.2;
            animation-delay: -8s;
        }

        @keyframes float-orb {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(40px, -40px) scale(1.1); }
            100% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Animated grid */
        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: grid-shift 20s linear infinite;
        }

        @keyframes grid-shift {
            0%   { transform: translateY(0); }
            100% { transform: translateY(40px); }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.6);
            animation: particle-float linear infinite;
        }

        @keyframes particle-float {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ===================== LAYOUT SPLIT ===================== */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 980px;
            min-height: 580px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px var(--panel-border),
                0 24px 80px rgba(0,0,0,0.6),
                0 0 120px rgba(255,56,92,0.15);
            margin: 24px;
        }

        /* ── LEFT BRANDING PANEL ── */
        .brand-panel {
            background: linear-gradient(135deg, #1a0a12 0%, #0d0514 40%, #0a1020 100%);
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(255,56,92,0.1) 0%,
                transparent 50%,
                rgba(108,61,224,0.08) 100%);
        }

        .brand-logo-area {
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--primary), #ff7c9e);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(255,56,92,0.35);
            animation: icon-pulse 3s ease-in-out infinite;
        }

        @keyframes icon-pulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(255,56,92,0.35); }
            50%       { box-shadow: 0 8px 32px rgba(255,56,92,0.55); }
        }

        .brand-icon svg { fill: white; }

        .brand-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: white;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .brand-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 260px;
        }

        .brand-stats {
            position: relative;
            z-index: 1;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .stat-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .stat-number {
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245,166,35,0.15);
            border: 1px solid rgba(245,166,35,0.25);
            color: var(--gold);
            font-size: 11px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 100px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .brand-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
            animation: badge-blink 2s ease-in-out infinite;
        }

        @keyframes badge-blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        /* Decorative 3D coin */
        .coin-3d {
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: conic-gradient(
                from 0deg,
                #ff385c, #ff7c9e, #ffd1da, #ff385c
            );
            opacity: 0.12;
            animation: coin-spin 8s linear infinite;
        }

        @keyframes coin-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ── RIGHT LOGIN FORM ── */
        .form-panel {
            background: rgba(10, 10, 20, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-left: 1px solid var(--panel-border);
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Error Banner */
        .error-banner {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #ff8aaa;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%       { transform: translateX(-6px); }
            75%       { transform: translateX(6px); }
        }

        .error-banner svg { flex-shrink: 0; margin-top: 1px; }

        /* Form fields */
        .field-group {
            margin-bottom: 20px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 8px;
            display: block;
        }

        .field-input-wrap {
            position: relative;
        }

        .field-input-wrap svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: color 0.2s;
        }

        .field-input {
            width: 100%;
            height: 52px;
            padding: 0 16px 0 46px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input::placeholder {
            color: rgba(255,255,255,0.25);
        }

        .field-input:focus {
            background: var(--input-focus);
            border-color: rgba(255,56,92,0.5);
            box-shadow: 0 0 0 3px rgba(255,56,92,0.12);
        }

        .field-input:focus + svg,
        .field-input-wrap:focus-within svg {
            color: var(--primary);
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: white; }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .remember-row label {
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* Submit button */
        .submit-btn {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, var(--primary), #ff6b85);
            color: white;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.2px;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(255,56,92,0.45);
        }

        .submit-btn:hover::before { opacity: 1; }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.loading {
            pointer-events: none;
        }

        .btn-spinner {
            display: none;
            width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .submit-btn.loading .btn-spinner { display: block; }
        .submit-btn.loading .btn-text { opacity: 0.7; }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--panel-border);
        }

        .divider span {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Back to storefront */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 14px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: white; }

        /* Security badge */
        .security-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            padding: 12px 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
        }

        .security-note span {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 680px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .brand-panel {
                padding: 40px 32px 32px;
                min-height: 200px;
            }
            .brand-stats { display: none; }
            .form-panel { padding: 36px 28px; }
        }
    </style>
</head>
<body>

    <!-- ===================== ANIMATED BACKGROUND ===================== -->
    <div class="bg-canvas">
        <div class="bg-grid"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Floating particles (generated by JS) -->
    </div>

    <!-- ===================== LOGIN WRAPPER ===================== -->
    <div class="login-wrapper">

        <!-- LEFT: Brand Panel -->
        <div class="brand-panel">
            <div class="brand-logo-area">
                <div class="brand-badge">⚡ Panel Manajemen</div>
                <div class="brand-icon">
                    <svg width="28" height="28" viewBox="0 0 32 32">
                        <path d="M16 1C21 1 24.5 4.5 24.5 9.5C24.5 13.5 21.5 17.5 16 23C10.5 17.5 7.5 13.5 7.5 9.5C7.5 4.5 11 1 16 1ZM16 11.5C17.1 11.5 18 10.6 18 9.5C18 8.4 17.1 7.5 16 7.5C14.9 7.5 14 8.4 14 9.5C14 10.6 14.9 11.5 16 11.5Z"/>
                    </svg>
                </div>
                <h1 class="brand-title">KDKMP<br>Digital System</h1>
                <p class="brand-subtitle">
                    Platform manajemen Koperasi Desa Merah Putih — retail, simpan pinjam, penyerapan tani, dan SHU dalam satu dasbor.
                </p>
            </div>

            <div class="brand-stats">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Modul Sistem</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Terenkripsi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Monitoring</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">v2.0</div>
                        <div class="stat-label">Versi Platform</div>
                    </div>
                </div>
            </div>

            <div class="coin-3d"></div>
        </div>

        <!-- RIGHT: Form Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Masuk ke Panel Admin</h2>
                <p>Khusus untuk Admin, Pengurus, Kasir, dan Staf Koperasi.</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="error-banner">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" flex-shrink="0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="error-banner">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" id="admin-login-form">
                @csrf

                <!-- Email -->
                <div class="field-group">
                    <label class="field-label" for="admin-email">Alamat Email</label>
                    <div class="field-input-wrap">
                        <input
                            type="email"
                            id="admin-email"
                            name="email"
                            class="field-input"
                            placeholder="admin@kdkmp.id"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label class="field-label" for="admin-password">Kata Sandi</label>
                    <div class="field-input-wrap">
                        <input
                            type="password"
                            id="admin-password"
                            name="password"
                            class="field-input"
                            placeholder="Kata sandi Anda"
                            autocomplete="current-password"
                            required
                        >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <button type="button" class="password-toggle" id="pwd-toggle" aria-label="Toggle password">
                            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Ingat saya di perangkat ini</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="submit-btn" id="submit-btn">
                    <span class="btn-text">Masuk ke Panel Admin</span>
                    <span class="btn-spinner"></span>
                </button>
            </form>

            <div class="divider">
                <span>atau</span>
            </div>

            <a href="{{ route('catalog.index') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Gerai Publik KDKMP
            </a>

            <div class="security-note">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>Koneksi aman · Sesi terenkripsi · Khusus staf koperasi</span>
            </div>
        </div>
    </div>

    <script>
        // ── Particle generator
        const canvas = document.querySelector('.bg-canvas');
        for (let i = 0; i < 25; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 4 + 1;
            p.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${Math.random() * 100}%;
                animation-duration: ${Math.random() * 15 + 10}s;
                animation-delay: ${Math.random() * 15}s;
                opacity: ${Math.random() * 0.5 + 0.1};
            `;
            canvas.appendChild(p);
        }

        // ── Password toggle
        const pwdInput = document.getElementById('admin-password');
        const pwdToggle = document.getElementById('pwd-toggle');
        pwdToggle.addEventListener('click', function() {
            const isText = pwdInput.type === 'text';
            pwdInput.type = isText ? 'password' : 'text';
            document.getElementById('eye-icon').innerHTML = isText
                ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>'
                : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        });

        // ── Loading state
        document.getElementById('admin-login-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.classList.add('loading');
        });
    </script>
</body>
</html>
