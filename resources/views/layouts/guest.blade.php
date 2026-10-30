@extends('layouts.app')

@section('content')
<section class="auth-page">
    <div class="auth-shell">
        <aside class="auth-promo">
            <a class="brand brand-light" href="{{ route('home') }}">
                <span class="brand-mark"><svg viewBox="0 0 36 36"><path d="M4 22c3-4 6-4 9 0s6 4 9 0 6-4 10 0M5 28c3-4 6-4 9 0s6 4 9 0 6-4 10 0M8 14l10-8 10 8v4H8z"/></svg></span>
                <span><strong>SIPAKLOK</strong><small>Jelajah sungai Banjarmasin</small></span>
            </a>
            <span class="eyebrow eyebrow-light">@yield('intro-eyebrow')</span>
            <h1>@yield('intro-title')</h1>
            <p>@yield('intro-description')</p>
            <div class="auth-promo-art" aria-hidden="true"><div class="visual-sun"></div><div class="visual-river"></div><div class="visual-boat"><span></span><i></i><b></b></div></div>
        </aside>
        <div class="auth-card @yield('auth-card-class')">
            @yield('auth-form')
        </div>
    </div>
</section>
@endsection
