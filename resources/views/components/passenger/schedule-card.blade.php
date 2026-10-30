@props(['schedule'])

<article class="schedule-card">
    <div class="schedule-art" aria-hidden="true"><span>◒</span></div>
    <div class="schedule-main">
        <span class="badge badge-blue">{{ ucfirst($schedule->jenis_tiket) }}</span>
        <h3>{{ $schedule->rute->nama_rute }}</h3>
        <p>{{ $schedule->armada->nama_klotok }} <span class="muted">· Motoris {{ $schedule->armada->nama_motoris }}</span></p>
        <div class="schedule-facts"><span>▦ {{ $schedule->tanggal_berangkat->format('d/m/Y') }}</span><span>◷ {{ substr($schedule->jam_berangkat, 0, 5) }} WITA</span><span>⌁ {{ $schedule->kapasitas_tersedia }} kursi tersedia</span></div>
    </div>
    <div class="schedule-price">
        <small>Mulai dari</small><strong>Rp {{ number_format((float) $schedule->rute->{'tarif_'.$schedule->jenis_tiket}, 0, ',', '.') }}</strong><small>per penumpang</small>
        <a class="button button-small" href="{{ route('pemesanan.penumpang', $schedule) }}">@auth Pilih Klotok @else Masuk untuk pesan @endauth</a>
    </div>
</article>
