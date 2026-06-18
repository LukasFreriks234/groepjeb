@props([
    'name' => '',
    'label',
    'type' => 'text',
    'value' => '',
    'min' => null,
    'max' => null
])

<label for="input-{{ $name }}">
    {{ $label }}
</label>

<input
    id="input-{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value ?? '') }}"
    @if($min !== null) min="{{ $min }}" @endif
    @if($max !== null) max="{{ $max }}" @endif
    aria-describedby="{{ $name }}-reader"
    required
/>

<span
    id="{{ $name }}-reader"
    class="sr-only"
    role="status"
    aria-live="assertive"
    aria-atomic="true"
></span>

@error($name)
    <span
        id="{{ $name }}-error"
        class="error-text"
        role="alert"
        aria-live="assertive"
    >
        {{ $message }}
    </span>
@enderror