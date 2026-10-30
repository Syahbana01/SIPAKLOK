@extends('layouts.guest')

@section('title', 'Masuk')
@section('intro-eyebrow', 'SELAMAT DATANG KEMBALI')
@section('intro-title', 'Perjalanan Anda berlanjut dari sini.')
@section('intro-description', 'Masuk ke akun untuk melihat riwayat pemesanan dan mengelola perjalanan.')
@section('auth-card-class', 'login-card')

@section('auth-form')
<div class="auth-card-heading">
    <span class="eyebrow">AKUN PENUMPANG</span>
    <h2>Masuk ke SIPAKLOK</h2>
    <p>Gunakan email dan kata sandi akun Anda.</p>
</div>
<form method="POST" action="{{ route('login.store') }}" class="form-stack">
    @csrf
    <label class="field"><span>Alamat email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nama@email.com"></label>
    <label class="field"><span>Kata sandi</span><input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan kata sandi"></label>
    <label class="check-field"><input type="checkbox" name="remember" value="1"><span>Ingat saya</span></label>
    <button class="button button-wide" type="submit">Masuk <span>→</span></button>
</form>
<p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
<p class="auth-footnote">Lupa kata sandi? Fitur pemulihan belum tersedia.</p>
@endsection
