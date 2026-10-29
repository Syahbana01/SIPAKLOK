<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasPengguna extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'aktivitas_pengguna';

    protected $fillable = ['user_id', 'jenis_aktivitas', 'deskripsi', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
