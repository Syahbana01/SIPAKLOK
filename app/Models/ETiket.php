<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ETiket extends Model
{
    protected $table = 'e_tiket';

    protected $fillable = ['detail_penumpang_id', 'kode_tiket', 'qr_code', 'status_tiket', 'tanggal_terbit'];

    protected $casts = ['tanggal_terbit' => 'datetime'];

    public function detailPenumpang()
    {
        return $this->belongsTo(DetailPenumpang::class);
    }
}
