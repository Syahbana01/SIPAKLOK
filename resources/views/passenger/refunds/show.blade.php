@extends('layouts.app')

@section('title', 'Status Refund')

@section('content')
<section class="section shell detail-section">
    <a class="text-link" href="{{ route('pemesanan.show', $booking->kode_transaksi) }}">← Kembali ke detail pemesanan</a>
    <div class="detail-heading">
        <div><span class="eyebrow">STATUS REFUND</span><h1>{{ $booking->jadwal->rute->nama_rute }}</h1><p>Kode pemesanan <strong>{{ $booking->kode_transaksi }}</strong></p></div>
    </div>

    @forelse ($booking->pembatalan as $cancellation)
        <article class="panel refund-card">
            <div class="history-top"><h2>Pengajuan pembatalan</h2><x-passenger.status-badge group="cancellation" :status="$cancellation->status" /></div>
            <p>{{ $cancellation->alasan }}</p>
            <small class="muted">Diajukan {{ $cancellation->tanggal_pengajuan->format('d/m/Y H:i') }}</small>

            @if ($cancellation->refund)
                <div class="summary-total"><span>Status refund</span><x-passenger.status-badge group="refund" :status="$cancellation->refund->status_refund" /></div>
                <div class="summary-total"><span>Jumlah</span><strong>Rp {{ number_format((float) $cancellation->refund->jumlah_refund, 0, ',', '.') }}</strong></div>
                @if ($cancellation->refund->tanggal_refund)<div class="summary-total"><span>Tanggal pemrosesan</span><strong>{{ $cancellation->refund->tanggal_refund->format('d/m/Y H:i') }}</strong></div>@endif
                @if ($cancellation->refund->referensi_refund)<div class="summary-total"><span>Referensi</span><strong>{{ $cancellation->refund->referensi_refund }}</strong></div>@endif
            @else
                <p class="payment-alert refund-pending"><span>i</span><span>Belum ada catatan refund untuk pengajuan ini. Status akan diperbarui setelah ditinjau.</span></p>
            @endif
        </article>
    @empty
        <div class="empty-card"><span class="empty-icon">◇</span><h2>Belum ada pengajuan pembatalan</h2><p>Informasi refund akan muncul setelah ada pengajuan pembatalan pada pemesanan ini.</p></div>
    @endforelse
</section>
@endsection
