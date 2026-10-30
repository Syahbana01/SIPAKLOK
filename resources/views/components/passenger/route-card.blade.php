@props(['route'])

<article class="route-card">
    <div class="route-art" aria-hidden="true"><span class="art-sun"></span><span class="art-boat">◒</span><span class="art-river"></span><span class="art-river art-river-two"></span></div>
    <div class="route-card-body">
        <div class="route-title-row"><h3>{{ $route->nama_rute }}</h3><span class="badge badge-green">Rute aktif</span></div>
        <p>{{ $route->deskripsi ?: 'Nikmati perjalanan menyusuri sungai dengan klotok.' }}</p>
        <div class="route-meta"><span>◷ &nbsp;{{ $route->estimasi_waktu ?: 'Durasi belum tersedia' }}</span></div>
        <div class="route-fares"><div><small>Tiket reguler</small><strong>Rp {{ number_format((float) $route->tarif_reguler, 0, ',', '.') }}</strong></div><div><small>Sewa klotok</small><strong>Rp {{ number_format((float) $route->tarif_charter, 0, ',', '.') }}</strong></div></div>
        <a class="text-link" href="{{ route('jadwal.index', ['rute_id' => $route->id]) }}">Lihat jadwal <span aria-hidden="true">→</span></a>
    </div>
</article>
