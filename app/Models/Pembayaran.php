<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = ['pemesanan_id', 'metode_pembayaran', 'jumlah_bayar', 'status_pembayaran', 'tanggal_bayar', 'referensi_pembayaran'];

    protected $casts = ['jumlah_bayar' => 'decimal:2', 'tanggal_bayar' => 'datetime'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function buktiPembayaran()
    {
        return $this->hasMany(BuktiPembayaran::class);
    }
}
