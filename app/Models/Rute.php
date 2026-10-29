<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    protected $table = 'rute';

    protected $fillable = ['nama_rute', 'deskripsi', 'tarif_reguler', 'tarif_charter', 'estimasi_waktu', 'status'];

    protected $casts = ['tarif_reguler' => 'decimal:2', 'tarif_charter' => 'decimal:2'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
