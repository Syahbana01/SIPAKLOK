<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';

    protected $fillable = ['kode_transaksi', 'user_id', 'jadwal_id', 'jumlah_tiket', 'jenis_tiket', 'total_harga', 'status_pemesanan', 'sumber_pemesanan', 'catatan'];

    protected $casts = ['jumlah_tiket' => 'integer', 'total_harga' => 'decimal:2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function detailPenumpang()
    {
        return $this->hasMany(DetailPenumpang::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusPemesanan::class);
    }

    public function pembatalan()
    {
        return $this->hasMany(Pembatalan::class);
    }
}
