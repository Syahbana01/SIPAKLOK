<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenumpang extends Model
{
    protected $table = 'detail_penumpang';

    protected $fillable = ['pemesanan_id', 'nama_lengkap', 'umur', 'jenis_kelamin'];

    protected $casts = ['umur' => 'integer'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function eTiket()
    {
        return $this->hasOne(ETiket::class);
    }
}
