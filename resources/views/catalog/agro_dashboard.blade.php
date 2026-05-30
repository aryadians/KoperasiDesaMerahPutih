@extends('layouts.app')

@section('title', 'Dasbor Komoditas Agro Desa — KDKMP')

@section('content')
<div class="reveal" style="margin-bottom: 32px; text-align: center;">
    <span class="promo-badge" style="background: #e6f6f0; color: #1a7f5a;">🌾 TRANSFARANSI AGRO DESA</span>
    <h1 style="font-size: 32px; font-weight: 800; color: var(--colors-ink); margin-top: 8px; letter-spacing: -0.5px;">Tren Harga Hasil Tani Lokal</h1>
    <p style="color: var(--colors-muted); max-width: 600px; margin: 8px auto 0; font-size: 15px;">
        Koperasi menyerap langsung komoditas tani warga dengan harga transparan dan adil, lalu menyalurkannya ke gerai ritel sembako KDKMP.
    </p>
</div>

<!-- LINE CHART: SVG Price Fluctuations -->
<div class="standard-card reveal-scale" style="padding: 24px 32px; margin-bottom: 40px; box-shadow: var(--shadow-lg);">
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
        📈 Grafik Pergerakan Harga Tani (7 Hari Terakhir)
    </h3>

    <div style="position: relative; width: 100%; height: 320px; overflow-x: auto;">
        <!-- Chart Canvas -->
        <svg viewBox="0 0 700 280" style="width: 100%; height: 100%; min-width: 600px; display: block; overflow: visible;">
            <!-- Grid lines -->
            @for($i = 0; $i <= 4; $i++)
                @php $y = 40 + $i * 50; @endphp
                <line x1="50" y1="{{ $y }}" x2="650" y2="{{ $y }}" stroke="#f0f0f0" stroke-width="1.5" />
                <!-- Price Labels -->
                <text x="42" y="{{ $y + 4 }}" font-size="10" fill="#888" text-anchor="end">
                    Rp {{ number_format(45000 - $i * 10000, 0, ',', '.') }}
                </text>
            @endfor

            <!-- X Axis Days -->
            @foreach($historyDays as $idx => $day)
                @php $x = 80 + $idx * 90; @endphp
                <text x="{{ $x }}" y="260" font-size="11" font-weight="600" fill="#666" text-anchor="middle">
                    {{ $day }}
                </text>
            @endforeach

            <!-- Trends Lines Plotting -->
            @php
                $colors = [
                    'Cabai Rawit Merah Lokal (Super Pedas)' => '#ff385c',
                    'Bawang Merah Brebes Pilihan' => '#b28900',
                    'Tomat Merah Segar Garut' => '#0052cc',
                    'Kentang Dieng Super' => '#7a22e0',
                    'Beras Merah Organik Cianjur' => '#1a7f5a'
                ];
            @endphp

            @foreach($trends as $name => $prices)
                @php
                    $points = '';
                    $color = $colors[$name] ?? '#888';
                    foreach($prices as $idx => $price) {
                        $x = 80 + $idx * 90;
                        // Map price 5000 - 45000 to y-coord 240 - 40
                        $y = 240 - (($price - 5000) / 40000) * 200;
                        $points .= "{$x},{$y} ";
                    }
                @endphp
                <!-- Draw Trend Line -->
                <polyline fill="none" stroke="{{ $color }}" stroke-width="3" stroke-linecap="round" points="{{ trim($points) }}" style="transition: all 0.3s;" />

                <!-- Dots on Data Points -->
                @foreach($prices as $idx => $price)
                    @php
                        $x = 80 + $idx * 90;
                        $y = 240 - (($price - 5000) / 40000) * 200;
                    @endphp
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="white" stroke="{{ $color }}" stroke-width="2.5" />
                @endforeach
            @endforeach
        </svg>
    </div>

    <!-- Chart Legend -->
    <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-top: 20px; border-top: 1px solid var(--colors-hairline-soft); padding-top: 16px;">
        @foreach($colors as $name => $color)
            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 500;">
                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 3px; background-color: {{ $color }};"></span>
                <span>{{ $name }}</span>
            </div>
        @endforeach
    </div>
</div>

<!-- LIST OF LOCAL COMMODITIES -->
<h2 class="reveal-left" style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">🌾 Komoditas yang Saat Ini Diserap Koperasi</h2>

<div class="grid-4 reveal" id="agro-products-grid">
    @foreach($products as $idx => $prod)
        <div class="property-card" style="animation-delay: {{ $idx * 0.05 }}s;">
            <div class="property-card-photo">
                <img src="{{ $prod->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $prod->name }}" loading="lazy">
                <span class="local-badge">🌾 Absorpsi Lokal</span>
            </div>

            <div class="property-card-meta">
                <div class="property-card-title">
                    <span>{{ $prod->name }}</span>
                </div>
                <p class="property-card-description">{{ $prod->description }}</p>
                
                <div style="border-top: 1px solid var(--colors-hairline-soft); padding-top: 12px; margin-top: 12px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                        <span style="color: var(--colors-muted);">Harga Jual Gerai:</span>
                        <strong style="color: var(--colors-primary);">Rp {{ number_format($prod->price_non_member, 0, ',', '.') }}/{{ $prod->unit }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--colors-muted);">Harga Beli Tani:</span>
                        <strong style="color: #1a7f5a;">Rp {{ number_format($prod->price_member * 0.85, 0, ',', '.') }}/{{ $prod->unit }}</strong>
                    </div>
                </div>
                
                <div style="margin-top: 16px; display: flex; gap: 8px;">
                    <a href="{{ route('catalog.index', ['local' => 1]) }}" class="button-primary" style="height: 36px; font-size: 12px; border-radius: 100px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        🛒 Beli di Gerai
                    </a>
                    @auth
                        @if(auth()->user()->role === 'anggota')
                            <a href="{{ route('member.crops') }}" class="button-secondary" style="height: 36px; font-size: 12px; border-radius: 100px; display: flex; align-items: center; justify-content: center; text-decoration: none; border-color: #1a7f5a; color: #1a7f5a;">
                                🌾 Jual Panen Anda
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
