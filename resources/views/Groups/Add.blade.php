<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Group</title>
    <link href="{{ asset('css/group.css') }}" type="text/css" rel="stylesheet" />
</head>

<body>
    <main class="group-page">
        <h1>Add Group</h1>
        <div class="group-form-container">
            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">
                        Group Name
                    </label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label>
                        Functions
                    </label>

                    <div id="selectedFunctions"></div>

                    <button type="button" id="addFunctionButton" class="add-button">
                        Add Function
                    </button>

                    <div id="functionContainer"></div>

                    <select id="functionSelector" style="display:none;margin-top:10px;">
                        <option value="">
                            -- Select Function --
                        </option>

                        @foreach($functions as $function)
                            <option value="{{ $function->id }}">
                                {{ $function->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="related_group">
                        Related Group
                    </label>

                    <select id="related_group" name="related_group">
                        <option value="">
                            -- Select Group --
                        </option>

                        @foreach($groups as $relatedGroup)
                            <option value="{{ $relatedGroup->id }}">
                                {{ $relatedGroup->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
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

                <div class="button-group">
                    <button type="submit" class="add-button">
                        Add Group
                    </button>

                    <a href="{{ route('groups.index') }}" class="back-button">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const relatedGroupSelect = document.getElementById("related_group");

            const relationshipInputs =
                document.querySelectorAll(
                    ".relationship-effect-input"
                );

            if (!relatedGroupSelect || relationshipInputs.length === 0) {
                return;
            }

            function toggleRelationshipInputs() {
                const hasRelationship = relatedGroupSelect.value !== "";
                relationshipInputs.forEach(function (input) {
                    input.disabled = !hasRelationship;
                    if (!hasRelationship) {
                        input.value = 0;
                        input.readOnly = true;
                        input.style.backgroundColor = "#e5e7eb";
                        input.style.color = "#6b7280";
                        input.style.cursor = "not-allowed";
                    }
                    else {
                        input.readOnly = false;
                        input.style.backgroundColor = "";
                        input.style.color = "";
                        input.style.cursor = "";
                    }
                });
            }

            relatedGroupSelect.addEventListener(
                "change",
                toggleRelationshipInputs
            );
            toggleRelationshipInputs();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('addFunctionButton');
            const container = document.getElementById('functionContainer');
            function updateAvailableOptions() {
                const selects =
                    document.querySelectorAll(
                        'select[name="function_ids[]"]'
                    );

                const selectedValues =
                    Array.from(selects)
                        .map(select => select.value)
                        .filter(value => value !== '');

                selects.forEach(select => {
                    const currentValue = select.value;
                    Array.from(select.options)
                        .forEach(option => {
                            if (option.value === '') {
                                return;
                            }
                            option.disabled =
                                selectedValues.includes(option.value)
                                && option.value !== currentValue;
                        });
                });
            }

            addButton.addEventListener('click', function () {
                const row = document.createElement('div');
                row.classList.add('function-row');
                row.innerHTML = `
                    <select name="function_ids[]">
                        <option value="">
                            -- Select Function --
                        </option>
                        @foreach($functions as $function)
                            <option value="{{ $function->id }}">
                                {{ $function->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" class="remove-function">
                        Remove
                    </button>
                `;

                container.appendChild(row);
                const select = row.querySelector('select');
                select.addEventListener('change', function () {
                    updateAvailableOptions();
                });

                row.querySelector('.remove-function')
                    .addEventListener('click', function () {
                        row.remove();
                        updateAvailableOptions();
                    });
                updateAvailableOptions();
            });
            updateAvailableOptions();
            const relatedGroup = document.getElementById('related_group');
            const relationshipInputs =
                document.querySelectorAll(
                    '#relationshipEffects input'
                );

            function updateRelationshipEffects() {
                const enabled = relatedGroup.value !== '';
                relationshipInputs.forEach(input => {
                    input.disabled = !enabled;
                });
            }

            relatedGroup.addEventListener(
                'change',
                updateRelationshipEffects
            );
            updateRelationshipEffects();
        });
    </script>
</body>
</html>