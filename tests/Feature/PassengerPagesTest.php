<?php

namespace Tests\Feature;

use App\Models\Armada;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\Role;
use App\Models\Rute;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PassengerPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_home_route_catalog_and_schedule_search(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Jelajahi Banjarmasin');
        $this->get(route('rute.index'))->assertOk()->assertSee('Rute belum tersedia');
        $this->get(route('jadwal.index'))->assertOk()->assertSee('Belum ada jadwal yang cocok');
    }

    public function test_registration_assigns_passenger_role_and_authenticates_user(): void
    {
        $this->seed(RoleSeeder::class);

        $this->post(route('register.store'), [
            'nama_lengkap' => 'Dewi Sungai',
            'email' => 'dewi@example.test',
            'no_telepon' => '081234567890',
            'password' => 'rahasia-aman-123',
            'password_confirmation' => 'rahasia-aman-123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'dewi@example.test',
            'role_id' => Role::query()->where('nama_role', 'penumpang')->value('id'),
        ]);
    }

    public function test_booking_recalculates_total_and_decrements_capacity_atomically(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $route = Rute::create([
            'nama_rute' => 'Pasar Terapung', 'tarif_reguler' => 25000, 'tarif_charter' => 90000, 'status' => 'aktif',
        ]);
        $boat = Armada::create(['nama_klotok' => 'Klotok Uji', 'nama_motoris' => 'Motoris Uji', 'kapasitas' => 8, 'status' => 'aktif']);
        $schedule = Jadwal::create([
            'armada_id' => $boat->id, 'rute_id' => $route->id,
            'tanggal_berangkat' => today()->addDays(2), 'jam_berangkat' => '09:30:00',
            'jenis_tiket' => 'reguler', 'kapasitas_tersedia' => 8, 'status' => 'tersedia',
        ]);

        $this->actingAs($user)->post(route('pemesanan.store', $schedule), [
            'jumlah_tiket' => 2,
            'jenis_tiket' => 'reguler',
            'total_harga' => 1,
            'penumpang' => [
                ['nama_lengkap' => 'Ayu', 'umur' => 25, 'jenis_kelamin' => 'perempuan'],
                ['nama_lengkap' => 'Bima', 'umur' => 30, 'jenis_kelamin' => 'laki-laki'],
            ],
        ])->assertRedirect();

        $booking = Pemesanan::query()->firstOrFail();
        $this->assertSame('50000.00', $booking->total_harga);
        $this->assertSame(2, $booking->detailPenumpang()->count());
        $this->assertDatabaseHas('jadwal', ['id' => $schedule->id, 'kapasitas_tersedia' => 6]);
        $this->assertDatabaseHas('riwayat_status_pemesanan', ['pemesanan_id' => $booking->id, 'status_baru' => 'pending']);
    }

    public function test_passenger_cannot_open_another_users_booking(): void
    {
        $this->seed(RoleSeeder::class);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $route = Rute::create(['nama_rute' => 'Rute Uji', 'tarif_reguler' => 10000, 'tarif_charter' => 50000, 'status' => 'aktif']);
        $boat = Armada::create(['nama_klotok' => 'Klotok Uji', 'nama_motoris' => 'Motoris Uji', 'kapasitas' => 8, 'status' => 'aktif']);
        $schedule = Jadwal::create(['armada_id' => $boat->id, 'rute_id' => $route->id, 'tanggal_berangkat' => today()->addDay(), 'jam_berangkat' => '10:00:00', 'jenis_tiket' => 'reguler', 'kapasitas_tersedia' => 8, 'status' => 'tersedia']);
        $booking = Pemesanan::create(['kode_transaksi' => 'SPK-TEST-OWNER', 'user_id' => $owner->id, 'jadwal_id' => $schedule->id, 'jumlah_tiket' => 1, 'jenis_tiket' => 'reguler', 'total_harga' => 10000, 'status_pemesanan' => 'pending', 'sumber_pemesanan' => 'website']);

        $this->actingAs($other)->get(route('pemesanan.show', $booking->kode_transaksi))->assertNotFound();
    }
}
