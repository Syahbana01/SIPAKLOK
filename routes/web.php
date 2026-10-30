<?php

use App\Http\Controllers\PassengerController;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PassengerController::class, 'home'])->name('home');
Route::get('/informasi-rute', [PassengerController::class, 'routes'])->name('rute.index');
Route::get('/jadwal-rute', [PassengerController::class, 'schedules'])->name('jadwal.index');

// URL lama tetap mengarah ke pencarian baru dengan filter yang tidak kosong.
Route::get('/jadwal', fn (Request $request) => redirect()->route(
    'jadwal.index',
    array_filter($request->only(['rute_id', 'tanggal', 'jenis_tiket', 'jumlah_tiket']), fn ($value) => $value !== null && $value !== '')
))->name('legacy.jadwal');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [PassengerController::class, 'registerForm'])->name('register');
    Route::post('/daftar', [PassengerController::class, 'register'])->name('register.store');
    Route::get('/masuk', [PassengerController::class, 'loginForm'])->name('login');
    Route::post('/masuk', [PassengerController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [PassengerController::class, 'logout'])->name('logout');

    Route::get('/data-penumpang/{jadwal}', [PassengerController::class, 'passengerForm'])->name('pemesanan.penumpang');
    Route::post('/data-penumpang/{jadwal}', [PassengerController::class, 'createBooking'])->name('pemesanan.store');
    Route::get('/pembayaran/{pemesanan:kode_transaksi}', [PassengerController::class, 'payment'])->name('pembayaran.show');
    Route::post('/pembayaran/{pemesanan:kode_transaksi}', [PassengerController::class, 'createPayment'])->name('pembayaran.store');
    Route::post('/pembayaran/{pembayaran}/bukti', [PassengerController::class, 'uploadProof'])->name('pembayaran.bukti.store');
    Route::get('/e-tiket/{pemesanan:kode_transaksi}', [PassengerController::class, 'ticket'])->name('e-tiket.show');

    Route::get('/riwayat-pemesanan', [PassengerController::class, 'history'])->name('pemesanan.riwayat');
    Route::get('/detail-pemesanan/{pemesanan:kode_transaksi}', [PassengerController::class, 'detail'])->name('pemesanan.show');
    Route::post('/pembatalan/{pemesanan:kode_transaksi}', [PassengerController::class, 'cancel'])->name('pembatalan.store');
    Route::get('/status-refund/{pemesanan:kode_transaksi}', [PassengerController::class, 'refund'])->name('refund.show');

    // Alias URL lama menjaga tautan tersimpan tetap berfungsi.
    Route::get('/pesan/{jadwal}', fn ($jadwal) => redirect()->route('pemesanan.penumpang', $jadwal))->whereNumber('jadwal')->name('legacy.pemesanan.penumpang');
    Route::post('/pesan/{jadwal}', [PassengerController::class, 'createBooking'])->whereNumber('jadwal')->name('legacy.pemesanan.store');
    Route::get('/pemesanan', fn () => redirect()->route('pemesanan.riwayat'))->name('legacy.pemesanan.riwayat');
    Route::get('/pemesanan/{pemesanan}', function (Pemesanan $pemesanan) {
        abort_unless((int) $pemesanan->user_id === (int) auth()->id(), 404);

        return redirect()->route('pemesanan.show', $pemesanan->kode_transaksi);
    })->name('legacy.pemesanan.show');
    Route::get('/pemesanan/{pemesanan}/pembayaran', function (Pemesanan $pemesanan) {
        abort_unless((int) $pemesanan->user_id === (int) auth()->id(), 404);

        return redirect()->route('pembayaran.show', $pemesanan->kode_transaksi);
    })->name('legacy.pembayaran.show');
    Route::post('/pemesanan/{pemesanan}/pembayaran', [PassengerController::class, 'createPayment'])->name('legacy.pembayaran.store');
    Route::post('/pemesanan/{pemesanan}/pembatalan', [PassengerController::class, 'cancel'])->name('legacy.pembatalan.store');
    Route::get('/pemesanan/{pemesanan}/e-tiket', function (Pemesanan $pemesanan) {
        abort_unless((int) $pemesanan->user_id === (int) auth()->id(), 404);

        return redirect()->route('e-tiket.show', $pemesanan->kode_transaksi);
    })->name('legacy.e-tiket.show');
});
