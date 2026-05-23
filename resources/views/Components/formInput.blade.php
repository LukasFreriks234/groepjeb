@props(['name', 'label', 'type' => 'text', 'value' => ''])

<label for="{{ $name }}">{{ $label }}</label>

<input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value ?? '') }}"
    required
    aria-describedby="{{ $name }}-error"
/>

@error($name)
    <span id="{{ $name }}-error" class="error-text">
        {{ $message }}
    </span>
@enderror