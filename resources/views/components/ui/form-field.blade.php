@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'autocomplete' => null,
])

@php($id = $attributes->get('id', 'field-'.$name))
<label class="field" for="{{ $id }}">
    <span>{{ $label }}</span>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" @required($required) {{ $attributes->except('id') }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete }}" @required($required) {{ $attributes->except('id') }}>
    @endif
</label>
