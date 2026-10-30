<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\RiwayatStatusPemesanan;
use App\Models\Role;
use App\Models\Rute;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PassengerController extends Controller
{
    public function home(): View
    {
        return view('passenger.home', ['routes' => Rute::query()->where('status', 'aktif')->orderBy('nama_rute')->limit(3)->get()]);
    }

    public function routes(): View
    {
        return view('passenger.routes.index', ['routes' => Rute::query()->where('status', 'aktif')->orderBy('nama_rute')->paginate(9)]);
    }

    public function schedules(Request $request): View
    {
        $filters = $request->validate([
            'rute_id' => ['nullable', 'integer', 'exists:rute,id'],
            'tanggal' => ['nullable', 'date', 'after_or_equal:today'],
            'jenis_tiket' => ['nullable', Rule::in(['reguler', 'charter'])],
            'jumlah_tiket' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'rute_id.integer' => 'Rute yang dipilih tidak valid.',
            'rute_id.exists' => 'Rute yang dipilih tidak ditemukan.',
            'tanggal.date' => 'Masukkan tanggal keberangkatan yang valid.',
            'tanggal.after_or_equal' => 'Tanggal keberangkatan tidak boleh sebelum hari ini.',
            'jenis_tiket.in' => 'Jenis tiket yang dipilih tidak tersedia.',
            'jumlah_tiket.integer' => 'Jumlah tiket harus berupa angka.',
            'jumlah_tiket.min' => 'Jumlah tiket minimal 1.',
            'jumlah_tiket.max' => 'Jumlah tiket maksimal 100.',
        ]);

        $schedules = Jadwal::query()->with(['rute', 'armada'])
            ->where('status', 'tersedia')->whereDate('tanggal_berangkat', '>=', today())->where('kapasitas_tersedia', '>', 0)
            ->whereHas('rute', fn (Builder $q) => $q->where('status', 'aktif'))
            ->whereHas('armada', fn (Builder $q) => $q->where('status', 'aktif')->whereColumn('jadwal.kapasitas_tersedia', '<=', 'armada.kapasitas'))
            ->when($filters['rute_id'] ?? null, fn (Builder $q, $id) => $q->where('rute_id', $id))
            ->when($filters['tanggal'] ?? null, fn (Builder $q, $date) => $q->whereDate('tanggal_berangkat', $date))
            ->when($filters['jenis_tiket'] ?? null, fn (Builder $q, $type) => $q->where('jenis_tiket', $type))
            ->orderBy('tanggal_berangkat')->orderBy('jam_berangkat')->paginate(8)->withQueryString();

        $activeRoutes = Rute::query()->where('status', 'aktif')->orderBy('nama_rute')->get();

        return view('passenger.schedules.index', [
            'schedules' => $schedules,
            'routes' => $activeRoutes,
            'selectedRoute' => $activeRoutes->firstWhere('id', (int) ($filters['rute_id'] ?? 0)),
            'filters' => $filters,
        ]);
    }

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.', 'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.', 'email.unique' => 'Email tersebut sudah digunakan.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks.', 'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.max' => 'Email maksimal 150 karakter.', 'no_telepon.string' => 'Nomor WhatsApp harus berupa teks.',
            'no_telepon.max' => 'Nomor WhatsApp maksimal 20 karakter.', 'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi tidak valid.',
            'password.min' => 'Kata sandi minimal 8 karakter.', 'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);
        $roleId = Role::query()->where('nama_role', 'penumpang')->value('id');
        abort_unless($roleId, 503, 'Pendaftaran belum dapat digunakan.');

        $user = User::create([
            'role_id' => $roleId, 'nama_lengkap' => $data['nama_lengkap'], 'email' => $data['email'],
            'no_telepon' => $data['no_telepon'] ?? null, 'password' => Hash::make($data['password']),
        ]);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Akun SIPAKLOK berhasil dibuat.');
    }

    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'], 'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.', 'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.', 'password.string' => 'Kata sandi tidak valid.',
        ]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Anda berhasil masuk.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil keluar.');
    }

    public function passengerForm(Jadwal $jadwal): View|RedirectResponse
    {
        abort_unless($jadwal->status === 'tersedia' && $jadwal->kapasitas_tersedia > 0, 404);
        if (! $jadwal->rute()->where('status', 'aktif')->exists() || ! $jadwal->armada()->where('status', 'aktif')->exists()) {
            abort(404);
        }

        return view('passenger.bookings.passenger-details', ['schedule' => $jadwal->load(['rute', 'armada'])]);
    }

    public function createBooking(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $data = $request->validate([
            'jumlah_tiket' => ['required', 'integer', 'min:1', 'max:100'],
            'penumpang' => ['required', 'array', 'min:1', 'max:100'],
            'penumpang.*.nama_lengkap' => ['required', 'string', 'max:100'],
            'penumpang.*.umur' => ['required', 'integer', 'min:0', 'max:120'],
            'penumpang.*.jenis_kelamin' => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ], [
            'jumlah_tiket.required' => 'Jumlah tiket wajib dipilih.', 'jumlah_tiket.min' => 'Pemesanan minimal 1 tiket.',
            'jumlah_tiket.integer' => 'Jumlah tiket harus berupa angka.', 'jumlah_tiket.max' => 'Jumlah tiket tidak boleh melebihi 100.',
            'penumpang.required' => 'Data penumpang wajib diisi.', 'penumpang.array' => 'Data penumpang tidak valid.',
            'penumpang.min' => 'Minimal satu data penumpang harus diisi.', 'penumpang.max' => 'Jumlah data penumpang maksimal 100.',
            'penumpang.*.nama_lengkap.required' => 'Nama penumpang wajib diisi.', 'penumpang.*.umur.required' => 'Umur penumpang wajib diisi.',
            'penumpang.*.jenis_kelamin.required' => 'Jenis kelamin penumpang wajib dipilih.',
            'penumpang.*.nama_lengkap.string' => 'Nama penumpang harus berupa teks.', 'penumpang.*.nama_lengkap.max' => 'Nama penumpang maksimal 100 karakter.',
            'penumpang.*.umur.integer' => 'Umur harus berupa angka.', 'penumpang.*.umur.min' => 'Umur tidak boleh kurang dari 0 tahun.',
            'penumpang.*.umur.max' => 'Umur maksimal 120 tahun.', 'penumpang.*.jenis_kelamin.in' => 'Pilih jenis kelamin yang tersedia.',
            'catatan.string' => 'Catatan harus berupa teks.', 'catatan.max' => 'Catatan maksimal 2.000 karakter.',
        ]);
        if (count($data['penumpang']) !== (int) $data['jumlah_tiket']) {
            throw ValidationException::withMessages(['jumlah_tiket' => 'Jumlah data penumpang harus sama dengan jumlah tiket.']);
        }

        $booking = DB::transaction(function () use ($jadwal, $data, $request) {
            $locked = Jadwal::query()->whereKey($jadwal->id)->lockForUpdate()->with(['rute', 'armada'])->firstOrFail();
            abort_unless($locked->status === 'tersedia' && $locked->rute->status === 'aktif' && $locked->armada->status === 'aktif', 422, 'Jadwal ini tidak tersedia.');
            abort_unless($locked->jenis_tiket === $request->input('jenis_tiket', $locked->jenis_tiket), 422, 'Jenis tiket tidak sesuai dengan jadwal.');
            abort_if((int) $data['jumlah_tiket'] > $locked->kapasitas_tersedia || $locked->kapasitas_tersedia > $locked->armada->kapasitas, 422, 'Kapasitas yang tersedia tidak mencukupi.');

            $jenis = $locked->jenis_tiket;
            $tarifSen = (int) round((float) $locked->rute->{'tarif_'.$jenis} * 100);
            $totalHarga = number_format(($tarifSen * (int) $data['jumlah_tiket']) / 100, 2, '.', '');
            $booking = Pemesanan::create([
                'kode_transaksi' => $this->transactionCode(), 'user_id' => $request->user()->id,
                'jadwal_id' => $locked->id, 'jumlah_tiket' => $data['jumlah_tiket'], 'jenis_tiket' => $jenis,
                'total_harga' => $totalHarga, 'status_pemesanan' => 'pending',
                'sumber_pemesanan' => 'website', 'catatan' => $data['catatan'] ?? null,
            ]);
            foreach ($data['penumpang'] as $passenger) {
                $booking->detailPenumpang()->create($passenger);
            }
            $locked->kapasitas_tersedia -= (int) $data['jumlah_tiket'];
            if ($locked->kapasitas_tersedia === 0) {
                $locked->status = 'penuh';
            }
            $locked->save();
            RiwayatStatusPemesanan::create([
                'pemesanan_id' => $booking->id, 'user_id' => $request->user()->id,
                'status_sebelumnya' => null, 'status_baru' => 'pending', 'keterangan' => 'Pemesanan dibuat melalui situs.',
            ]);

            return $booking;
        });

        return redirect()->route('pembayaran.show', $booking->kode_transaksi)->with('success', 'Data penumpang tersimpan. Pilih metode pembayaran.');
    }

    public function payment(Pemesanan $pemesanan): View
    {
        $this->authorizeBooking($pemesanan);

        return view('passenger.bookings.payment', ['booking' => $pemesanan->load(['jadwal.rute', 'jadwal.armada', 'pembayaran.buktiPembayaran'])]);
    }

    public function createPayment(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorizeBooking($pemesanan);
        abort_unless($pemesanan->status_pemesanan === 'pending', 422, 'Pemesanan ini tidak menunggu pembayaran.');
        abort_if($pemesanan->pembayaran()->where('status_pembayaran', 'berhasil')->exists(), 422, 'Pembayaran untuk pemesanan ini sudah berhasil.');
        $data = $request->validate(['metode_pembayaran' => ['required', Rule::in(['qris', 'transfer', 'e-wallet'])]], [
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
            'metode_pembayaran.in' => 'Metode pembayaran yang dipilih tidak tersedia.',
        ]);
        $payment = DB::transaction(fn () => $pemesanan->pembayaran()->create([
            'metode_pembayaran' => $data['metode_pembayaran'], 'jumlah_bayar' => $pemesanan->total_harga,
            'status_pembayaran' => 'menunggu',
        ]));

        return redirect()->route('pembayaran.show', $pemesanan->kode_transaksi)->with('success', 'Instruksi pembayaran belum dikonfigurasi. Pembayaran tercatat menunggu verifikasi.')->with('payment_id', $payment->id);
    }

    public function uploadProof(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        abort_unless((int) $pembayaran->pemesanan->user_id === (int) $request->user()->id, 404);
        abort_unless($pembayaran->status_pembayaran === 'menunggu', 422, 'Bukti hanya dapat dikirim untuk pembayaran yang menunggu.');
        abort_if($pembayaran->buktiPembayaran()->where('status_verifikasi', 'menunggu')->exists(), 422, 'Bukti pembayaran untuk transaksi ini sudah dikirim dan menunggu verifikasi.');
        $data = $request->validate(['file_bukti' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']], [
            'file_bukti.required' => 'Pilih berkas bukti pembayaran.', 'file_bukti.file' => 'Berkas bukti pembayaran tidak valid.',
            'file_bukti.mimes' => 'Berkas harus berupa JPG, PNG, atau PDF.', 'file_bukti.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);
        $path = $data['file_bukti']->store('bukti-pembayaran', 'local');
        $pembayaran->buktiPembayaran()->create(['file_bukti' => $path, 'status_verifikasi' => 'menunggu']);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi.');
    }

    public function ticket(Pemesanan $pemesanan): View
    {
        $this->authorizeBooking($pemesanan);
        $booking = $pemesanan->load(['jadwal.rute', 'jadwal.armada', 'detailPenumpang.eTiket', 'pembayaran']);
        $verified = $booking->pembayaran->contains(fn ($payment) => $payment->status_pembayaran === 'berhasil');

        return view('passenger.bookings.ticket', ['booking' => $booking, 'verified' => $verified]);
    }

    public function history(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'selesai', 'dibatalkan', 'ditolak'])],
            'q' => ['nullable', 'string', 'max:100'],
        ], [
            'status.in' => 'Filter status yang dipilih tidak tersedia.', 'q.string' => 'Kata pencarian harus berupa teks.',
            'q.max' => 'Kata pencarian maksimal 100 karakter.',
        ]);
        $bookings = Pemesanan::query()->with(['jadwal.rute', 'jadwal.armada'])
            ->where('user_id', $request->user()->id)
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status_pemesanan', $status))
            ->when($filters['q'] ?? null, function (Builder $q, $term) {
                $q->where(function (Builder $query) use ($term) {
                    $query->where('kode_transaksi', 'like', "%{$term}%")
                        ->orWhereHas('jadwal.rute', fn (Builder $route) => $route->where('nama_rute', 'like', "%{$term}%"));
                });
            })->latest()->paginate(8)->withQueryString();

        return view('passenger.bookings.history', compact('bookings', 'filters'));
    }

    public function detail(Pemesanan $pemesanan): View
    {
        $this->authorizeBooking($pemesanan);

        return view('passenger.bookings.show', ['booking' => $pemesanan->load(['jadwal.rute', 'jadwal.armada', 'detailPenumpang.eTiket', 'pembayaran.buktiPembayaran', 'riwayatStatus', 'pembatalan.refund'])]);
    }

    public function refund(Pemesanan $pemesanan): View
    {
        $this->authorizeBooking($pemesanan);

        return view('passenger.refunds.show', [
            'booking' => $pemesanan->load(['jadwal.rute', 'pembatalan.refund']),
        ]);
    }

    public function cancel(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorizeBooking($pemesanan);
        $data = $request->validate(['alasan' => ['required', 'string', 'max:2000']], [
            'alasan.required' => 'Alasan pembatalan wajib diisi.', 'alasan.string' => 'Alasan pembatalan harus berupa teks.',
            'alasan.max' => 'Alasan pembatalan maksimal 2.000 karakter.',
        ]);
        DB::transaction(function () use ($pemesanan, $request, $data) {
            $booking = Pemesanan::query()->whereKey($pemesanan->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($booking->status_pemesanan, ['pending', 'selesai'], true), 422, 'Pemesanan ini tidak dapat diajukan pembatalan.');
            abort_if($booking->pembatalan()->whereIn('status', ['menunggu', 'disetujui'])->exists(), 422, 'Pengajuan pembatalan untuk pemesanan ini sudah tercatat.');
            $booking->pembatalan()->create([
                'user_id' => $request->user()->id, 'alasan' => $data['alasan'], 'status' => 'menunggu', 'tanggal_pengajuan' => now(),
            ]);
        });

        return redirect()->route('pemesanan.show', $pemesanan->kode_transaksi)->with('success', 'Pengajuan pembatalan tercatat dan menunggu peninjauan.');
    }

    private function authorizeBooking(Pemesanan $booking): void
    {
        abort_unless((int) $booking->user_id === (int) auth()->id(), 404);
    }

    private function transactionCode(): string
    {
        do {
            $code = 'SPK-'.now()->format('ymd').'-'.strtoupper(bin2hex(random_bytes(4)));
        } while (Pemesanan::query()->where('kode_transaksi', $code)->exists());

        return $code;
    }
}
