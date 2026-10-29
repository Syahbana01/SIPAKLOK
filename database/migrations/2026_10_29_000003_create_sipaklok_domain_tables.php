<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas_pengguna', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('jenis_aktivitas', 100);
            $t->text('deskripsi')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('rute', function (Blueprint $t) {
            $t->id();
            $t->string('nama_rute', 150);
            $t->text('deskripsi')->nullable();
            $t->decimal('tarif_reguler', 12, 2)->default(0);
            $t->decimal('tarif_charter', 12, 2)->default(0);
            $t->string('estimasi_waktu', 50)->nullable();
            $t->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $t->timestamps();
        });
        Schema::create('armada', function (Blueprint $t) {
            $t->id();
            $t->string('nama_klotok', 100);
            $t->string('nama_motoris', 100);
            $t->unsignedInteger('kapasitas');
            $t->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $t->timestamps();
        });
        Schema::create('jadwal', function (Blueprint $t) {
            $t->id();
            $t->foreignId('armada_id')->constrained('armada')->restrictOnDelete();
            $t->foreignId('rute_id')->constrained('rute')->restrictOnDelete();
            $t->date('tanggal_berangkat');
            $t->time('jam_berangkat');
            $t->enum('jenis_tiket', ['reguler', 'charter']);
            $t->unsignedInteger('kapasitas_tersedia');
            $t->enum('status', ['tersedia', 'penuh', 'dibatalkan'])->default('tersedia');
            $t->timestamps();
        });
        Schema::create('pemesanan', function (Blueprint $t) {
            $t->id();
            $t->string('kode_transaksi', 50)->unique();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('jadwal_id')->constrained('jadwal')->restrictOnDelete();
            $t->unsignedInteger('jumlah_tiket');
            $t->enum('jenis_tiket', ['reguler', 'charter']);
            $t->decimal('total_harga', 12, 2);
            $t->enum('status_pemesanan', ['pending', 'selesai', 'dibatalkan', 'ditolak'])->default('pending');
            $t->enum('sumber_pemesanan', ['website', 'kasir']);
            $t->text('catatan')->nullable();
            $t->timestamps();
        });
        Schema::create('riwayat_status_pemesanan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('status_sebelumnya', 50)->nullable();
            $t->string('status_baru', 50);
            $t->text('keterangan')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('pembatalan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->text('alasan');
            $t->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $t->dateTime('tanggal_pengajuan');
            $t->timestamps();
        });
        Schema::create('refund', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pembatalan_id')->unique()->constrained('pembatalan')->restrictOnDelete();
            $t->decimal('jumlah_refund', 12, 2);
            $t->enum('status_refund', ['menunggu', 'diproses', 'berhasil', 'gagal'])->default('menunggu');
            $t->dateTime('tanggal_refund')->nullable();
            $t->string('referensi_refund', 100)->nullable();
            $t->timestamps();
        });
        Schema::create('detail_penumpang', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $t->string('nama_lengkap', 100);
            $t->unsignedInteger('umur')->nullable();
            $t->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $t->timestamps();
        });
        Schema::create('e_tiket', function (Blueprint $t) {
            $t->id();
            $t->foreignId('detail_penumpang_id')->unique()->constrained('detail_penumpang')->restrictOnDelete();
            $t->string('kode_tiket', 100)->unique();
            $t->string('qr_code', 255)->nullable();
            $t->enum('status_tiket', ['aktif', 'digunakan', 'dibatalkan'])->default('aktif');
            $t->dateTime('tanggal_terbit');
            $t->timestamps();
        });
        Schema::create('pembayaran', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pemesanan_id')->constrained('pemesanan')->restrictOnDelete();
            $t->enum('metode_pembayaran', ['qris', 'transfer', 'e-wallet']);
            $t->decimal('jumlah_bayar', 12, 2);
            $t->enum('status_pembayaran', ['menunggu', 'berhasil', 'gagal'])->default('menunggu');
            $t->dateTime('tanggal_bayar')->nullable();
            $t->string('referensi_pembayaran', 100)->nullable();
            $t->timestamps();
        });
        Schema::create('bukti_pembayaran', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pembayaran_id')->constrained('pembayaran')->restrictOnDelete();
            $t->string('file_bukti', 255);
            $t->enum('status_verifikasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $t->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('tanggal_verifikasi')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['bukti_pembayaran', 'pembayaran', 'e_tiket', 'detail_penumpang', 'refund', 'pembatalan', 'riwayat_status_pemesanan', 'pemesanan', 'jadwal', 'armada', 'rute', 'aktivitas_pengguna'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
