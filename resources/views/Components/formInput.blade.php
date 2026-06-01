@props(['name' => '', 'label', 'type' => 'text', 'value' => '', 'min' => null, 'max' => null])

<label for="input-{{ $name }}">{{ $label }}</label>

<input
    id="input-{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value ?? '') }}"
    @if($min !== null) min="{{ $min }}" @endif
    @if($max !== null) max="{{ $max }}" @endif
    required
    @error($name)
        aria-invalid="true"
        aria-describedby="{{ $name }}-error"
    @enderror
/>

@error($name)
    <span id="{{ $name }}-error" class="error-text" role="alert">
        {{ $message }}
    </span>
@enderror