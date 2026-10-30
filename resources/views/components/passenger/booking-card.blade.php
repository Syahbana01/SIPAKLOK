@props(['booking'])

<article class="history-card">
    <div class="history-art" aria-hidden="true"><span>◒</span></div>
    <div class="history-main">
        <div class="history-top"><span class="eyebrow">{{ $booking->kode_transaksi }}</span><x-passenger.status-badge :status="$booking->status_pemesanan" /></div>
        <h2>{{ $booking->jadwal->rute->nama_rute }}</h2>
        <div class="history-facts"><span>▦ {{ $booking->jadwal->tanggal_berangkat->format('d/m/Y') }}</span><span>◷ {{ substr($booking->jadwal->jam_berangkat, 0, 5) }}</span><span>♙ {{ $booking->jumlah_tiket }} penumpang</span></div>
        <div class="history-bottom">
            <div><small>{{ ucfirst($booking->jenis_tiket) }} · Total</small><strong>Rp {{ number_format((float) $booking->total_harga, 0, ',', '.') }}</strong></div>
            <div class="history-actions"><a class="button button-outline button-small" href="{{ route('pemesanan.show', $booking->kode_transaksi) }}">Detail</a>@if($booking->status_pemesanan === 'pending')<a class="button button-small" href="{{ route('pembayaran.show', $booking->kode_transaksi) }}">Lanjutkan pembayaran</a>@endif @if($booking->status_pemesanan === 'selesai')<a class="button button-small" href="{{ route('e-tiket.show', $booking->kode_transaksi) }}">Buka E-Tiket</a>@endif</div>
        </div>
    </div>
</article>
