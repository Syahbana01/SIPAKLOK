@props(['current' => 1])

@php($steps = ['Pilih perjalanan', 'Data penumpang', 'Pembayaran', 'E-Tiket'])
<nav class="steps-progress" aria-label="Tahapan pemesanan">
    @foreach ($steps as $index => $label)
        @if ($index > 0)<span @class(['progress-line', 'active' => $current > $index]) aria-hidden="true"></span>@endif
        <span @class(['progress-step', 'done' => $current > $index + 1, 'current' => $current === $index + 1]) aria-current="{{ $current === $index + 1 ? 'step' : 'false' }}">
            <i>{{ $current > $index + 1 ? '✓' : $index + 1 }}</i><b>{{ $label }}</b>
        </span>
    @endforeach
</nav>
