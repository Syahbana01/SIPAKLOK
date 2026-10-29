<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Armada extends Model
{
    protected $table = 'armada';

    protected $fillable = ['nama_klotok', 'nama_motoris', 'kapasitas', 'status'];

    protected $casts = ['kapasitas' => 'integer'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
