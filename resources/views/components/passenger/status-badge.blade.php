@props(['status', 'group' => 'booking'])

@php
    $labels = [
        'booking' => ['pending' => 'Menunggu', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan', 'ditolak' => 'Ditolak'],
        'payment' => ['menunggu' => 'Menunggu verifikasi', 'berhasil' => 'Berhasil', 'gagal' => 'Gagal'],
        'cancellation' => ['menunggu' => 'Menunggu peninjauan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'],
        'refund' => ['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'berhasil' => 'Berhasil', 'gagal' => 'Gagal'],
        'ticket' => ['aktif' => 'Aktif', 'digunakan' => 'Digunakan', 'dibatalkan' => 'Dibatalkan'],
        'verification' => ['menunggu' => 'Menunggu verifikasi', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'],
    ];
    $label = $labels[$group][$status] ?? 'Status tidak tersedia';
    $tone = match ($status) {
        'selesai', 'berhasil', 'disetujui', 'diterima', 'aktif' => 'green',
        'ditolak', 'dibatalkan', 'gagal' => 'red',
        'menunggu', 'pending', 'diproses' => 'amber',
        default => 'blue',
    };
@endphp
<span {{ $attributes->class(['badge', 'badge-'.$tone]) }}>{{ $label }}</span>
