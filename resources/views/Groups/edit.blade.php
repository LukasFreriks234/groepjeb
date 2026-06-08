<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Group</title>
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
</head>

<body>

    <div class="group-page">

        <h1>Edit Group</h1>

        <div class="group-form-container">

            <form method="POST" action="{{ route('groups.update', $group->id) }}">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">
                        Group Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ $group->name }}"
                        required
                    >
                </div>

                <div class="form-group">

                    <label for="function_id">
                        Function
                    </label>

                    <select id="function_id" name="function_id">
                        <option value="">
                            -- Select Function --
                        </option>

                        @foreach($functions as $function)
                            <option
                                value="{{ $function->id }}"
                                {{ $group->functions->contains($function->id) ? 'selected' : '' }}
                            >
                                {{ $function->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <h2>Group Effects</h2>

                <div class="effects-grid">

                    <label for="safety">
                        Safety
                    </label>

                    <input
                        type="number"
                        id="safety"
                        name="safety"
                        min="-10"
                        max="10"
                        value="{{ old('safety', $group->safety) }}"
                        class="effect-input"
                    >

                    <label for="recreation">
                        Recreation
                    </label>

                    <input
                        type="number"
                        id="recreation"
                        name="recreation"
                        min="-10"
                        max="10"
                        value="{{ old('recreation', $group->recreation) }}"
                        class="effect-input"
                    >

                    <label for="environmental_quality">
                        Environmental Quality
                    </label>

                    <input
                        type="number"
                        id="environmental_quality"
                        name="environmental_quality"
                        min="-10"
                        max="10"
                        value="{{ old('environmental_quality', $group->environmental_quality) }}"
                        class="effect-input"
                    >

                    <label for="services">
                        Services
                    </label>

                    <input
                        type="number"
                        id="services"
                        name="services"
                        min="-10"
                        max="10"
                        value="{{ old('services', $group->services) }}"
                        class="effect-input"
                    >

                    <label for="mobility">
                        Mobility
                    </label>

                    <input
                        type="number"
                        id="mobility"
                        name="mobility"
                        min="-10"
                        max="10"
                        value="{{ old('mobility', $group->mobility) }}"
                        class="effect-input"
                    >

                </div>

                <div class="button-group">
                    <button type="submit" class="save-button">
                        Save Changes
                    </button>

                    <a href="{{ route('groups.index') }}" class="back-button">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.effect-input');
            function updateColor(input) {
                const value = parseInt(input.value) || 0;
                input.style.fontWeight = 'bold';
                if (value > 0) {
                    input.style.color = 'green';
                }
                else if (value < 0) {
                    input.style.color = 'red';
                }
                else {
                    input.style.color = 'black';
                }
            }

            inputs.forEach(input => {
                updateColor(input);
                input.addEventListener('input', function () {
                    updateColor(input);
                });
            });
        });
        </script>
</body>
</html>