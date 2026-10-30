@props(['type' => 'success', 'message'])
<div class="shell flash flash-{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
    @if (is_array($message))
        <strong>Periksa kembali isian Anda.</strong>
        <ul>@foreach ($message as $item)<li>{{ $item }}</li>@endforeach</ul>
    @else
        {{ $message }}
    @endif
</div>
