@props(['href' => null, 'variant' => 'primary', 'size' => null, 'type' => 'button'])

@php($classes = trim('button '.($variant === 'primary' ? '' : 'button-'.$variant).' '.($size ? 'button-'.$size : '')))
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
