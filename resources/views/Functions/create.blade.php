<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Function</title>
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
</head>

<body>
    <h1>Create Function</h1>

    <div class="container">
        <div class="form-section">
            <form method="POST" action="{{ route('functions.store') }}" enctype="multipart/form-data">
                @csrf

                <x-formInput
                    name="name"
                    label="Name"
                    :value="old('name')"
                />

                <label for="image">Image</label>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    alt="Upload an image for the new function"
                >

                <label for="category">Category</label>
                <select id="category" name="category">
                    @foreach($categories as $category)
                        <option value="{{ $category->category }}"
                            {{ old('category') == $category->category ? 'selected' : '' }}>
                            {{ $category->category }}
                        </option>
                    @endforeach
                </select>

                <h2>Add Relationship</h2>

                <label for="related_function">Select Function</label>
                <select id="related_function" name="related_function">
                    <option value="">-- Select Function --</option>

                    @foreach(($functions ?? []) as $relatedFunction)
                        <option value="{{ $relatedFunction->id }}"
                            {{ old('related_function') == $relatedFunction->id ? 'selected' : '' }}>
                            {{ $relatedFunction->name }}
                        </option>
                    @endforeach
                </select>

                <h2>Relationship Effects</h2>

                <label for="relationship_safety">Safety</label>
                <input
                    type="number"
                    id="relationship_safety"
                    name="relationship_safety"
                    class="relationship-effect-input"
                    value="{{ old('relationship_safety', 0) }}"
                    min="-10"
                    max="10"
                >

                <label for="relationship_recreation">Recreation</label>
                <input
                    type="number"
                    id="relationship_recreation"
                    name="relationship_recreation"
                    class="relationship-effect-input"
                    value="{{ old('relationship_recreation', 0) }}"
                    min="-10"
                    max="10"
                >

                <label for="relationship_environmental">Environmental Quality</label>
                <input
                    type="number"
                    id="relationship_environmental"
                    name="relationship_environmental"
                    class="relationship-effect-input"
                    value="{{ old('relationship_environmental', 0) }}"
                    min="-10"
                    max="10"
                >

                <label for="relationship_services">Services</label>
                <input
                    type="number"
                    id="relationship_services"
                    name="relationship_services"
                    class="relationship-effect-input"
                    value="{{ old('relationship_services', 0) }}"
                    min="-10"
                    max="10"
                >

                <label for="relationship_mobility">Mobility</label>
                <input
                    type="number"
                    id="relationship_mobility"
                    name="relationship_mobility"
                    class="relationship-effect-input"
                    value="{{ old('relationship_mobility', 0) }}"
                    min="-10"
                    max="10"
                >

                <h2>Effects</h2>

                <x-formInput
                    name="Safety"
                    label="Safety"
                    type="number"
                    :value="old('Safety', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Recreation"
                    label="Recreation"
                    type="number"
                    :value="old('Recreation', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Environmental_Quality"
                    label="Environmental Quality"
                    type="number"
                    :value="old('Environmental_Quality', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Services"
                    label="Services"
                    type="number"
                    :value="old('Services', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Mobility"
                    label="Mobility"
                    type="number"
                    :value="old('Mobility', 0)"
                    min="-10"
                    max="10"
                />

                <button type="submit">Create function</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const relatedFunctionSelect = document.getElementById('related_function');
            const relationshipInputs = document.querySelectorAll('.relationship-effect-input');

            function toggleRelationshipInputs() {
                const hasRelationship = relatedFunctionSelect.value !== '';

                relationshipInputs.forEach(function (input) {
                    input.disabled = !hasRelationship;

                    if (!hasRelationship) {
                        input.value = 0;
                    }
                });
            }

            relatedFunctionSelect.addEventListener('change', toggleRelationshipInputs);

            toggleRelationshipInputs();
        });
    </script>
</body>

</html>