<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refund';

    protected $fillable = ['pembatalan_id', 'jumlah_refund', 'status_refund', 'tanggal_refund', 'referensi_refund'];

    protected $casts = ['jumlah_refund' => 'decimal:2', 'tanggal_refund' => 'datetime'];

    public function pembatalan()
    {
        return $this->belongsTo(Pembatalan::class);
    }
}
