@props(['name' => '', 'label', 'type' => 'text', 'value' => '', 'min' => null, 'max' => null])

<label for="input-{{ $name }}">
    {{ $label }}
</label>

<span id="{{ $name }}-value" class="sr-only" aria-live="polite"></span>

<input
    id="input-{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value ?? '') }}"
    aria-describedby="{{ $name }}-value"

    @if($min !== null)
        min="{{ $min }}"
    @endif

    @if($max !== null)
        max="{{ $max }}"
    @endif

    required
/>

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