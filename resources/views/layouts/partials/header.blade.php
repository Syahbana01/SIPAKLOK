<header class="site-header">
    <div class="shell nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="SIPAKLOK, beranda">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 36 36"><path d="M4 22c3-4 6-4 9 0s6 4 9 0 6-4 10 0M5 28c3-4 6-4 9 0s6 4 9 0 6-4 10 0M8 14l10-8 10 8v4H8z"/></svg></span>
            <span><strong>SIPAKLOK</strong><small>Jelajah sungai Banjarmasin</small></span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="navigasi-utama" data-nav-toggle><span></span><span></span><span></span></button>
        <nav id="navigasi-utama" class="main-nav" data-nav-menu aria-label="Navigasi utama">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('rute.index') }}" @class(['active' => request()->routeIs('rute.*')])>Informasi Rute</a>
            @auth <a href="{{ route('pemesanan.riwayat') }}" @class(['active' => request()->routeIs('pemesanan.*')])>Riwayat Pemesanan</a> @endauth
            <span class="nav-actions">
                @auth
                    <span class="nav-greeting">Halo, {{ auth()->user()->nama_lengkap }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-logout" type="submit">Keluar</button></form>
                @else
                    <a class="nav-login" href="{{ route('login') }}">Masuk</a>
                    <a class="button button-small" href="{{ route('register') }}">Daftar</a>
                @endauth
            </span>
        </nav>
    </div>
</header>
