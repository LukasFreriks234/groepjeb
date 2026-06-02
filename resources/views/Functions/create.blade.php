<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Function</title>

    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
    <script src="{{ asset('js/functionForm.js') }}" defer></script>
</head>

<body>
    <h1>Create Function</h1>

    <div class="container">
        <div class="form-section">
            <form method="POST" action="{{ route('functions.store') }}" enctype="multipart/form-data" novalidate>
                @csrf

                <x-formInput
                    name="name"
                    label="Name"
                    :value="old('name')"
                />

                @error('name')
                    <p class="form-error" id="name-error">{{ $message }}</p>
                @enderror

                <div class="form-group">
                    <label for="image">Image</label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        aria-describedby="@error('image') image-error @enderror"
                    >

                    @error('image')
                        <p class="form-error" id="image-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category">Category</label>

                    <select
                        id="category"
                        name="category"
                        aria-describedby="@error('category') category-error @enderror"
                    >
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->category }}"
                                {{ old('category') == $category->category ? 'selected' : '' }}
                            >
                                {{ $category->category }}
                            </option>
                        @endforeach
                    </select>

                    @error('category')
                        <p class="form-error" id="category-error">{{ $message }}</p>
                    @enderror
                </div>

                <fieldset>
                    <legend>Add Relationship</legend>

                    <div class="form-group">
                        <label for="related_function">Select Function</label>

                        <select
                            id="related_function"
                            name="related_function"
                            aria-describedby="@error('related_function') related-function-error @enderror"
                        >
                            <option value="">No related function</option>

                            @foreach(($functions ?? []) as $relatedFunction)
                                <option
                                    value="{{ $relatedFunction->id }}"
                                    {{ old('related_function') == $relatedFunction->id ? 'selected' : '' }}
                                >
                                    {{ $relatedFunction->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('related_function')
                            <p class="form-error" id="related-function-error">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Relationship Effects</legend>

                    <p id="relationship-effects-help" class="sr-only">
                        Relationship effect values can be from minus 10 to 10.
                    </p>

                    <div class="form-group">
                        <label for="relationship_safety">Safety</label>
                        <input
                            type="number"
                            id="relationship_safety"
                            name="relationship_safety"
                            class="relationship-effect-input"
                            value="{{ old('relationship_safety', 0) }}"
                            min="-10"
                            max="10"
                            aria-describedby="relationship-effects-help @error('relationship_safety') relationship-safety-error @enderror"
                        >

                        @error('relationship_safety')
                            <p class="form-error" id="relationship-safety-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="relationship_recreation">Recreation</label>
                        <input
                            type="number"
                            id="relationship_recreation"
                            name="relationship_recreation"
                            class="relationship-effect-input"
                            value="{{ old('relationship_recreation', 0) }}"
                            min="-10"
                            max="10"
                            aria-describedby="relationship-effects-help @error('relationship_recreation') relationship-recreation-error @enderror"
                        >

                        @error('relationship_recreation')
                            <p class="form-error" id="relationship-recreation-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="relationship_environmental">Environmental Quality</label>
                        <input
                            type="number"
                            id="relationship_environmental"
                            name="relationship_environmental"
                            class="relationship-effect-input"
                            value="{{ old('relationship_environmental', 0) }}"
                            min="-10"
                            max="10"
                            aria-describedby="relationship-effects-help @error('relationship_environmental') relationship-environmental-error @enderror"
                        >

                        @error('relationship_environmental')
                            <p class="form-error" id="relationship-environmental-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="relationship_services">Services</label>
                        <input
                            type="number"
                            id="relationship_services"
                            name="relationship_services"
                            class="relationship-effect-input"
                            value="{{ old('relationship_services', 0) }}"
                            min="-10"
                            max="10"
                            aria-describedby="relationship-effects-help @error('relationship_services') relationship-services-error @enderror"
                        >

                        @error('relationship_services')
                            <p class="form-error" id="relationship-services-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="relationship_mobility">Mobility</label>
                        <input
                            type="number"
                            id="relationship_mobility"
                            name="relationship_mobility"
                            class="relationship-effect-input"
                            value="{{ old('relationship_mobility', 0) }}"
                            min="-10"
                            max="10"
                            aria-describedby="relationship-effects-help @error('relationship_mobility') relationship-mobility-error @enderror"
                        >

                        @error('relationship_mobility')
                            <p class="form-error" id="relationship-mobility-error">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Effects</legend>

                    <p id="effects-help" class="sr-only">
                        Effect values can be from minus 10 to 10.
                    </p>

                    <x-formInput
                        name="Safety"
                        label="Safety"
                        type="number"
                        :value="old('Safety', 0)"
                        min="-10"
                        max="10"
                        aria-describedby="effects-help"
                    />

                    <x-formInput
                        name="Recreation"
                        label="Recreation"
                        type="number"
                        :value="old('Recreation', 0)"
                        min="-10"
                        max="10"
                        aria-describedby="effects-help"
                    />

                    <x-formInput
                        name="Environmental_Quality"
                        label="Environmental Quality"
                        type="number"
                        :value="old('Environmental_Quality', 0)"
                        min="-10"
                        max="10"
                        aria-describedby="effects-help"
                    />

                    <x-formInput
                        name="Services"
                        label="Services"
                        type="number"
                        :value="old('Services', 0)"
                        min="-10"
                        max="10"
                        aria-describedby="effects-help"
                    />

                    <x-formInput
                        name="Mobility"
                        label="Mobility"
                        type="number"
                        :value="old('Mobility', 0)"
                        min="-10"
                        max="10"
                        aria-describedby="effects-help"
                    />
                </fieldset>

                <button type="submit">Create function</button>
            </form>
        </div>
    </div>
</body>

</html>