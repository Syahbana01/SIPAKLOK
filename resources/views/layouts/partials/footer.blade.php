<footer class="site-footer">
    <div class="shell footer-grid">
        <div class="footer-about"><a class="brand brand-light" href="{{ route('home') }}"><span class="brand-mark"><svg viewBox="0 0 36 36"><path d="M4 22c3-4 6-4 9 0s6 4 9 0 6-4 10 0M5 28c3-4 6-4 9 0s6 4 9 0 6-4 10 0M8 14l10-8 10 8v4H8z"/></svg></span><span><strong>SIPAKLOK</strong><small>Jelajah sungai Banjarmasin</small></span></a><p>Temukan informasi perjalanan klotok dan rencanakan perjalanan Anda di sungai Banjarmasin.</p></div>
        <div><h2>Layanan Utama</h2><a href="{{ route('rute.index') }}">Informasi Rute</a><a href="{{ route('jadwal.index') }}">Cari Jadwal</a>@auth<a href="{{ route('pemesanan.riwayat') }}">Riwayat Pemesanan</a>@endauth</div>
        <div><h2>Dermaga &amp; Rute</h2><p>Jelajahi pilihan rute dan jadwal yang tersedia di situs.</p><a href="{{ route('rute.index') }}">Lihat informasi rute <span aria-hidden="true">→</span></a></div>
        <div><h2>Alamat dan Kontak</h2><p>Informasi kontak layanan belum dikonfigurasi.</p></div>
    </div>
    <div class="shell footer-bottom"><span>© {{ date('Y') }} SIPAKLOK</span><span>Informasi perjalanan klotok Banjarmasin</span></div>
</footer>
