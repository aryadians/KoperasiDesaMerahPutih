@extends('layouts.admin')

@section('title', 'Kelola Pesanan Gerai - KDKMP')

@section('content')

<h1 style="font-size: 28px; font-weight: 600; margin-bottom: 24px; color: var(--ink);">Kelola Semua Pesanan Gerai</h1>

<div class="standard-card" style="padding: 0; overflow: hidden; box-shadow: var(--shadow-sm);">
    @if($orders->isEmpty())
        <div style="padding: 32px; text-align: center; color: var(--muted);">
            Belum ada pesanan belanja masuk dari warga.
        </div>
    @else
        <div class="clean-table-container">
            <table class="clean-table" style="margin-top: 0;">
                <thead style="background: var(--surface);">
                    <tr>
                        <th>Nomor Pesanan</th>
                        <th>Warga / Anggota</th>
                        <th>Tanggal Pesan</th>
                        <th>Pengiriman</th>
                        <th>Total Belanja</th>
                        <th>Status Bayar</th>
                        <th style="text-align: center; width: 300px;">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td style="font-weight: 700; color: var(--ink);">{{ $order->order_number }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--ink);">{{ $order->user->name }}</div>
                                <span style="font-size: 11px; color: var(--muted); background: var(--surface-md); padding: 1px 6px; border-radius: var(--r-full);">{{ ucfirst($order->user->role) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge badge-neutral">
                                    {{ $order->delivery_type }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge badge-success">LUNAS</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge badge-warning">PENDING</span>
                                @else
                                    <span class="badge badge-danger">DIBATALKAN</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-sm" style="text-decoration: none;">
                                        👁️ Detail
                                    </a>
                                    @if($order->payment_status === 'pending')
                                        <form action="{{ route('staff.orders.update', [$order->id, 'paid']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                ✓ Tandai Lunas
                                            </button>
                                        </form>
                                        <form action="{{ route('staff.orders.update', [$order->id, 'cancelled']) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                ✕ Batal
                                            </button>
                                        </form>
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
@endsection
