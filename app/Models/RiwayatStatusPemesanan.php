<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatusPemesanan extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'riwayat_status_pemesanan';

    protected $fillable = ['pemesanan_id', 'user_id', 'status_sebelumnya', 'status_baru', 'keterangan', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
