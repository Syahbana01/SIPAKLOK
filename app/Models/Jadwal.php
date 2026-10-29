<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = ['armada_id', 'rute_id', 'tanggal_berangkat', 'jam_berangkat', 'jenis_tiket', 'kapasitas_tersedia', 'status'];

    protected $casts = ['tanggal_berangkat' => 'date', 'kapasitas_tersedia' => 'integer'];

    public function armada()
    {
        return $this->belongsTo(Armada::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }
}
