@extends('layouts.app')
@section('title', 'Informasi Rute')
@section('content')
<section class="page-hero"><div class="shell"><span class="eyebrow eyebrow-light">KENALI PERJALANAN ANDA</span><h1>Informasi Rute Klotok</h1><p>Temukan rute, estimasi waktu, dan tarif perjalanan yang tersedia.</p><div class="page-hero-decoration" aria-hidden="true">⌁</div></div></section>
<section class="section shell"><div class="section-heading"><div><span class="eyebrow">JELAJAHI SUNGAI</span><h2>Pilih perjalanan yang sesuai</h2><p>Tarif ditampilkan sesuai informasi rute pada sistem.</p></div><span class="result-count">{{ $routes->total() }} rute</span></div>
    @if($routes->isEmpty())<div class="empty-card"><span class="empty-icon">⌁</span><h3>Rute belum tersedia</h3><p>Informasi rute aktif akan muncul di halaman ini setelah ditambahkan.</p></div>@else<div class="route-grid">@foreach($routes as $route)<x-passenger.route-card :route="$route" />@endforeach</div><div class="pagination-wrap">{{ $routes->links() }}</div>@endif
</section>
@endsection
