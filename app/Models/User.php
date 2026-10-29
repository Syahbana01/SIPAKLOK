<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['role_id', 'nama_lengkap', 'email', 'no_telepon', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function aktivitasPengguna()
    {
        return $this->hasMany(AktivitasPengguna::class);
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function pembatalan()
    {
        return $this->hasMany(Pembatalan::class);
    }

    public function riwayatStatusPemesanan()
    {
        return $this->hasMany(RiwayatStatusPemesanan::class);
    }

    public function buktiPembayaranTerverifikasi()
    {
        return $this->hasMany(BuktiPembayaran::class, 'diverifikasi_oleh');
    }
}
