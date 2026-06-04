<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Function</title>

    <link rel="stylesheet"
          href="{{ asset('css/editStyle.css') }}">

    <script src="{{ asset('js/functionForm.js') }}" defer></script>
</head>

<body>

<h1>Edit Function</h1>

@php
    $isAdmin = auth()->user() && auth()->user()->role === 'admin';
@endphp

<div class="container">

    <div class="form-section">

        <form method="POST"
              action="{{ route('functions.update', $function->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @if($isAdmin)
                <x-formInput
                    name="name"
                    label="Name"
                    :value="old('name', $function->name)"
                />
            @else
                <div class="form-group-readonly">
                    <label>Name</label>
                    <p class="readonly-text">{{ $function->name }}</p>
                    <input type="hidden" name="name" value="{{ $function->name }}">
                </div>
            @endif

            <label for="image">Change image</label>
            <input
                type="file"
                id="image"
                name="image"
                accept="image/*"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <label for="category">Category</label>
            <select id="category" name="category" autocomplete="off" {{ !$isAdmin ? 'disabled' : '' }}>
                @foreach($categories as $category)
                    <option value="{{ $category->category }}"
                        {{ old('category', $function->category) == $category->category ? 'selected' : '' }}>
                        {{ $category->category }}
                    </option>
                @endforeach
            </select>

            <h2>Add Relationship</h2>

            <label for="related_function">Select Function</label>
            <select
                id="related_function"
                name="related_function"
                aria-label="Select a function to create a relationship with"
                {{ !$isAdmin ? 'disabled' : '' }}
            >
                <option value="">-- Select Function --</option>

                @foreach($functions as $relatedFunction)
                    @if($relatedFunction->id != $function->id)
                        <option value="{{ $relatedFunction->id }}"
                            {{ old('related_function', $function->related_function_id) == $relatedFunction->id ? 'selected' : '' }}>
                            {{ $relatedFunction->name }}
                        </option>
                    @endif
                @endforeach
            </select>

            <h2>Relationship Effects</h2>

            <label for="relationship_safety">Safety</label>
            <input
                type="number"
                id="relationship_safety"
                name="relationship_safety"
                min="-10"
                max="10"
                value="{{ old('relationship_safety', $function->relationship_safety ?? 0) }}"
                class="relationship-effect-input {{ (old('relationship_safety', $function->relationship_safety ?? 0) > 0) ? 'positiveEffect' : ((old('relationship_safety', $function->relationship_safety ?? 0) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <label for="relationship_recreation">Recreation</label>
            <input
                type="number"
                id="relationship_recreation"
                name="relationship_recreation"
                min="-10"
                max="10"
                value="{{ old('relationship_recreation', $function->relationship_recreation ?? 0) }}"
                class="relationship-effect-input {{ (old('relationship_recreation', $function->relationship_recreation ?? 0) > 0) ? 'positiveEffect' : ((old('relationship_recreation', $function->relationship_recreation ?? 0) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <label for="relationship_environmental">Environmental Quality</label>
            <input
                type="number"
                id="relationship_environmental"
                name="relationship_environmental"
                min="-10"
                max="10"
                value="{{ old('relationship_environmental', $function->relationship_environmental ?? 0) }}"
                class="relationship-effect-input {{ (old('relationship_environmental', $function->relationship_environmental ?? 0) > 0) ? 'positiveEffect' : ((old('relationship_environmental', $function->relationship_environmental ?? 0) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <label for="relationship_services">Services</label>
            <input
                type="number"
                id="relationship_services"
                name="relationship_services"
                min="-10"
                max="10"
                value="{{ old('relationship_services', $function->relationship_services ?? 0) }}"
                class="relationship-effect-input {{ (old('relationship_services', $function->relationship_services ?? 0) > 0) ? 'positiveEffect' : ((old('relationship_services', $function->relationship_services ?? 0) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <label for="relationship_mobility">Mobility</label>
            <input
                type="number"
                id="relationship_mobility"
                name="relationship_mobility"
                min="-10"
                max="10"
                value="{{ old('relationship_mobility', $function->relationship_mobility ?? 0) }}"
                class="relationship-effect-input {{ (old('relationship_mobility', $function->relationship_mobility ?? 0) > 0) ? 'positiveEffect' : ((old('relationship_mobility', $function->relationship_mobility ?? 0) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
                {{ !$isAdmin ? 'disabled' : '' }}
            >

            <!-- EFFECTS -->
            <h2>Effects</h2>

            <label for="Safety">Safety</label>
            <input
                type="number"
                id="Safety"
                name="Safety"
                min="-10"
                max="10"
                value="{{ old('Safety', data_get($function->effects, 'Safety', 0)) }}"
                class="{{ (old('Safety', data_get($function->effects, 'Safety', 0)) > 0) ? 'positiveEffect' : ((old('Safety', data_get($function->effects, 'Safety', 0)) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
            >

            <label for="Recreation">Recreation</label>
            <input
                type="number"
                id="Recreation"
                name="Recreation"
                min="-10"
                max="10"
                value="{{ old('Recreation', data_get($function->effects, 'Recreation', 0)) }}"
                class="{{ (old('Recreation', data_get($function->effects, 'Recreation', 0)) > 0) ? 'positiveEffect' : ((old('Recreation', data_get($function->effects, 'Recreation', 0)) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
            >

            <label for="Environmental_Quality">Environmental Quality</label>
            <input
                type="number"
                id="Environmental_Quality"
                name="Environmental_Quality"
                min="-10"
                max="10"
                value="{{ old('Environmental_Quality', data_get($function->effects, 'Environmental Quality', 0)) }}"
                class="{{ (old('Environmental_Quality', data_get($function->effects, 'Environmental Quality', 0)) > 0) ? 'positiveEffect' : ((old('Environmental_Quality', data_get($function->effects, 'Environmental Quality', 0)) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
            >

            <label for="Services">Services</label>
            <input
                type="number"
                id="Services"
                name="Services"
                min="-10"
                max="10"
                value="{{ old('Services', data_get($function->effects, 'Services', 0)) }}"
                class="{{ (old('Services', data_get($function->effects, 'Services', 0)) > 0) ? 'positiveEffect' : ((old('Services', data_get($function->effects, 'Services', 0)) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
            >

            <label for="Mobility">Mobility</label>
            <input
                type="number"
                id="Mobility"
                name="Mobility"
                min="-10"
                max="10"
                value="{{ old('Mobility', data_get($function->effects, 'Mobility', 0)) }}"
                class="{{ (old('Mobility', data_get($function->effects, 'Mobility', 0)) > 0) ? 'positiveEffect' : ((old('Mobility', data_get($function->effects, 'Mobility', 0)) < 0) ? 'negativeEffect' : 'neutralEffect') }}"
            >
            <div class="button-group">
                <!-- SAVE -->
                <button type="submit">
                    Save changes
                </button>
                <a href="{{ route('functions.index') }}">
                    <button type="button">Back</button>
                </a>
            </div>
        </form>

    </div>

    <div class="image-section">
        <p>Current image:</p>
        <img src="{{ asset($function->image) }}"
             alt="">
    </div>

</div>

</body>
</html>