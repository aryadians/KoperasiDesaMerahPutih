<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — KDKMP Digital</title>
    <link rel="manifest" href="/manifest.json">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a0000 0%, #2d0808 50%, #C0392B 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #fff;
        }
        .container {
            text-align: center;
            padding: 40px 32px;
            max-width: 480px;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 24px;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        p {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
        }
        .btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        .badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>Tidak Ada Koneksi</h1>
        <p>
            Anda sedang offline. Beberapa fitur KDKMP mungkin tidak tersedia.<br>
            Periksa koneksi internet Anda dan coba lagi.
        </p>
        <a href="/" class="btn" onclick="location.reload(); return false;">
            🔄 Coba Lagi
        </a>
        <div class="badge">🏛️ KDKMP Digital System — Koperasi Desa Merah Putih</div>
    </div>
</body>
</html>
