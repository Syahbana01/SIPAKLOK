<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jelajah Sungai Martapura') · SIPAKLOK</title>
    <meta name="description" content="Informasi rute dan pemesanan tiket klotok di Banjarmasin.">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>{!! file_get_contents(resource_path('css/app.css')) !!}</style>
        <script>{!! preg_replace('/^import .*;\s*/m', '', file_get_contents(resource_path('js/app.js'))) !!}</script>
    @endif
</head>
<body>
    @include('layouts.partials.header')
    <main>
        @if (session('success')) <x-ui.alert type="success" :message="session('success')" /> @endif
        @if ($errors->any()) <x-ui.alert type="error" :message="$errors->all()" /> @endif
        @yield('content')
    </main>
    @include('layouts.partials.footer')
    @stack('scripts')
</body>
</html>
