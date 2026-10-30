@props(['schedule' => null, 'booking' => null, 'liveTotal' => false])

@php
    $trip = $booking?->jadwal ?? $schedule;
    $route = $trip->rute;
    $boat = $trip->armada;
    $ticketType = $booking?->jenis_tiket ?? $trip->jenis_tiket;
    $quantity = (int) ($booking?->jumlah_tiket ?? 1);
    $unitFare = (float) $route->{'tarif_'.$ticketType};
    $total = (float) ($booking?->total_harga ?? ($unitFare * $quantity));
@endphp

<aside class="trip-summary">
    <span class="eyebrow">{{ $booking ? 'RINGKASAN PEMESANAN' : 'RINGKASAN PERJALANAN' }}</span>
    @if ($booking)<span class="booking-code">{{ $booking->kode_transaksi }}</span>@endif
    <h2>{{ $route->nama_rute }}</h2>
    <div class="summary-boat"><span class="boat-icon">◒</span><div><strong>{{ $boat->nama_klotok }}</strong><small>@if($booking){{ $quantity }} penumpang · @endif Motoris {{ $boat->nama_motoris }}</small></div></div>
    <div class="summary-details">
        <div><span>Tanggal</span><strong>{{ $trip->tanggal_berangkat->format('d/m/Y') }}</strong></div>
        <div><span>Berangkat</span><strong>{{ substr($trip->jam_berangkat, 0, 5) }} WITA</strong></div>
        <div><span>Jenis tiket</span><strong>{{ ucfirst($ticketType) }}</strong></div>
        @unless($booking)<div><span>Durasi</span><strong>{{ $route->estimasi_waktu ?: 'Belum tersedia' }}</strong></div>@endunless
    </div>
    @if ($liveTotal)
        <div class="summary-total"><span>Tarif per penumpang</span><strong>Rp {{ number_format($unitFare, 0, ',', '.') }}</strong></div>
        <div class="summary-total summary-grand"><span>Perkiraan total</span><strong data-total>Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
        <p class="summary-note">Total akhir dihitung ulang oleh sistem saat pemesanan dibuat.</p>
    @else
        <div class="summary-total summary-grand"><span>{{ $booking ? 'Total pembayaran' : 'Tarif per penumpang' }}</span><strong>Rp {{ number_format($booking ? $total : $unitFare, 0, ',', '.') }}</strong></div>
        @if ($booking)<x-passenger.status-badge :status="$booking->status_pemesanan" />@endif
    @endif
</aside>
