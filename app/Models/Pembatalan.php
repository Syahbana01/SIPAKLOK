<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembatalan extends Model
{
    protected $table = 'pembatalan';

    protected $fillable = ['pemesanan_id', 'user_id', 'alasan', 'status', 'tanggal_pengajuan'];

    protected $casts = ['tanggal_pengajuan' => 'datetime'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
