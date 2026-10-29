<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiPembayaran extends Model
{
    protected $table = 'bukti_pembayaran';

    protected $fillable = ['pembayaran_id', 'file_bukti', 'status_verifikasi', 'diverifikasi_oleh', 'tanggal_verifikasi'];

    protected $casts = ['tanggal_verifikasi' => 'datetime'];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
