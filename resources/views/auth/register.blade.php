@extends('layouts.guest')

@section('title', 'Daftar Akun')
@section('intro-eyebrow', 'MULAI PERJALANAN ANDA')
@section('intro-title', 'Setiap perjalanan punya cerita.')
@section('intro-description', 'Buat akun SIPAKLOK untuk merencanakan perjalanan klotok, menyimpan pemesanan, dan melihat informasi tiket Anda.')

@section('auth-form')
<div class="auth-card-heading">
    <span class="eyebrow">AKUN PENUMPANG</span>
    <h2>Buat akun baru</h2>
    <p>Lengkapi informasi berikut untuk mendaftar.</p>
</div>
<form method="POST" action="{{ route('register.store') }}" class="form-stack">
    @csrf
    <label class="field"><span>Nama lengkap</span><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" autocomplete="name" required maxlength="100" placeholder="Masukkan nama lengkap"></label>
    <label class="field"><span>Alamat email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="150" placeholder="nama@email.com"></label>
    <label class="field"><span>Nomor WhatsApp <small>(opsional)</small></span><input type="tel" name="no_telepon" value="{{ old('no_telepon') }}" autocomplete="tel" maxlength="20" placeholder="08xxxxxxxxxx"></label>
    <label class="field"><span>Kata sandi</span><input type="password" name="password" autocomplete="new-password" required minlength="8" placeholder="Minimal 8 karakter"></label>
    <label class="field"><span>Konfirmasi kata sandi</span><input type="password" name="password_confirmation" autocomplete="new-password" required placeholder="Ulangi kata sandi"></label>
    <button class="button button-wide" type="submit">Buat Akun <span>→</span></button>
</form>
<p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
<p class="auth-footnote">Dengan mendaftar, Anda dapat mengelola pemesanan melalui SIPAKLOK.</p>
@endsection
