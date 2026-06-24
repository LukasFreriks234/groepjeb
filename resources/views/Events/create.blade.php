<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>

    <link rel="stylesheet" href="{{ asset('css/navbarStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/createEvent.css') }}">

    <script src="{{ asset('js/createEvent.js') }}" defer></script>
</head>

<body>

    <x-navbar />

    <h1 tabindex="0">Create Event</h1>

    <div class="container event-container">
        <div class="form-section">
            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div style="color:red;" role="alert" aria-live="assertive" tabindex="-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <x-formInput name="name" label="Name" :value="old('name')" />

                <label for="image">Image</label>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    aria-label="Upload an image for the new event"
                    required
                    aria-required="true"
                >

                <h2 tabindex="0">Effect while active</h2>

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

                <h2 tabindex="0">Event settings</h2>

                <fieldset aria-describedby="event-type-help">
                    <legend>Event type</legend>

                    <div class="containerRadio">
                        <input
                            type="radio"
                            id="oneOff"
                            name="typeEvent"
                            value="oneOff"
                            {{ old('typeEvent', 'oneOff') === 'oneOff' ? 'checked' : '' }}
                        >
                        <label for="oneOff">One-off</label>
                    </div>

                    <div class="containerRadio">
                        <input
                            type="radio"
                            id="recurring"
                            name="typeEvent"
                            value="recurring"
                            {{ old('typeEvent') === 'recurring' ? 'checked' : '' }}
                        >
                        <label for="recurring">Recurring</label>
                    </div>
                </fieldset>

                <label for="input-length" class="label">Length active</label>

                <div class="length-container">
                    <input
                        id="input-length"
                        name="length"
                        type="number"
                        value="{{ old('length') }}"
                        required
                        aria-required="true"
                        min="1"
                    >

                    <select name="lengthUnit">
                        <option value="hours" {{ old('lengthUnit') === 'hours' ? 'selected' : '' }}>Hours</option>
                        <option value="days" {{ old('lengthUnit') === 'days' ? 'selected' : '' }}>Days</option>
                        <option value="weeks" {{ old('lengthUnit') === 'weeks' ? 'selected' : '' }}>Weeks</option>
                    </select>
                </div>

                <div id="recurringFields">

                <h2 tabindex="0">Date and Time</h2>

                    <div class="dateTime">

                        <label>
                            Start date
                            <input
                                type="date"
                                id="startDate"
                                name="startDate"
                                value="{{ old('startDate') }}"
                            >
                        </label>
                    </div>

                    <p class="sr-only">
                    The system will activate the event on the next possible day according to the information before.
                    </p>

                    <div class="label">
                        <label for="endDate">End date</label>
                        <input
                            type="date"
                            id="endDate"
                            name="endDate"
                            value="{{ old('endDate') }}"
                        >

                        <p class="sr-only">
                        After this date the event will no longer activate.
                        </p>

                        <p class="sr-only">
                        Want it to never end? Keep this field empty.
                        </p>
                    </div>

                    <div>
                        <label class="label" for="frequency">Frequency</label>

                        <select name="recurrencePattern" id="frequency">
                            <option value="daily" {{ old('recurrencePattern') === 'daily' ? 'selected' : '' }}>daily</option>
                            <option value="weekly" {{ old('recurrencePattern') === 'weekly' ? 'selected' : '' }}>weekly</option>
                            <option value="monthly" {{ old('recurrencePattern') === 'monthly' ? 'selected' : '' }}>monthly</option>
                            <option value="yearly" {{ old('recurrencePattern') === 'yearly' ? 'selected' : '' }}>yearly</option>
                        </select>
                    </div>

                    <div id="dailyFields">
                        <div class="every label">
                            <label for="everyDay">Every</label>

                            <select name="amountDay" id="everyDay">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" {{ old('amountDay') == $i ? 'selected' : '' }}>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>

                            <label for="everyDay">Day(s)</label>
                        </div>
                    </div>

                    <div id="weeklyFields">
                        <div class="every label">
                            <label for="everyWeek">Every</label>

                            <select name="amountWeek" id="everyWeek">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" {{ old('amountWeek') == $i ? 'selected' : '' }}>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>

                            <label for="everyWeek">Week(s)</label>
                        </div>

                        <p id="weekday-help" class="sr-only">
                            Press Space to select.
                        </p>

                        <div class="weekDays label" aria-describedby="weekday-help">
                            <p id="weekday-help" class="sr-only">
                                Use the Space key to select or deselect a day.
                            </p>

                            <div class="weekDays label">

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="monday"
                                        name="weekdays[]"
                                        value="monday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('monday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="monday">Monday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="tuesday"
                                        name="weekdays[]"
                                        value="tuesday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('tuesday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="tuesday">Tuesday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="wednesday"
                                        name="weekdays[]"
                                        value="wednesday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('wednesday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="wednesday">Wednesday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="thursday"
                                        name="weekdays[]"
                                        value="thursday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('thursday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="thursday">Thursday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="friday"
                                        name="weekdays[]"
                                        value="friday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('friday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="friday">Friday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="saturday"
                                        name="weekdays[]"
                                        value="saturday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('saturday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="saturday">Saturday</label>
                                </div>

                                <div class="containerRadio">
                                    <input
                                        type="checkbox"
                                        id="sunday"
                                        name="weekdays[]"
                                        value="sunday"
                                        aria-describedby="weekday-help"
                                        {{ in_array('sunday', old('weekdays', [])) ? 'checked' : '' }}
                                    >
                                    <label for="sunday">Sunday</label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div id="monthlyFields">
                        <div class="every label">
                            <label for="everyMonth">Every</label>

                            <select name="amountMonth" id="everyMonth">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" {{ old('amountMonth') == $i ? 'selected' : '' }}>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>

                            <label for="everyMonth">Month(s)</label>
                        </div>

                        <div class="containerRadio label">
                            <input
                                type="radio"
                                id="each"
                                name="typeMonth"
                                value="each"
                                {{ old('typeMonth') === 'each' ? 'checked' : '' }}
                            >
                            <label for="each">Each</label>
                        </div>

                        <div class="containerRadio label">
                            <input
                                type="radio"
                                id="onThe"
                                name="typeMonth"
                                value="onThe"
                                {{ old('typeMonth') === 'onThe' ? 'checked' : '' }}
                            >
                            <label for="onThe">On the...</label>
                        </div>

                        <fieldset id="eachFields" aria-describedby="month-day-help">

                            <p id="month-day-help" class="sr-only">
                                Use the Space key to select or deselect a day of the month.
                            </p>
                            <legend>Choose the days of the month this event will be active.</legend>

                            <div class="month-days-grid">
                                <?php for ($day = 1; $day <= 31; $day++): ?>
                                    <label class="day-box">
                                        <input
                                            type="checkbox"
                                            name="monthDays[]"
                                            value="<?= $day ?>"
                                            aria-label="Day <?= $day ?>. Press Space to select or deselect."
                                        >
                                        <span aria-hidden="true"><?= $day ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </fieldset>

                        <div id="onTheFields">
                            <div class="onThe label">
                                <select name="ordinalNumber">
                                    <option value="first" {{ old('ordinalNumber') === 'first' ? 'selected' : '' }}>First</option>
                                    <option value="second" {{ old('ordinalNumber') === 'second' ? 'selected' : '' }}>Second</option>
                                    <option value="third" {{ old('ordinalNumber') === 'third' ? 'selected' : '' }}>Third</option>
                                    <option value="fourth" {{ old('ordinalNumber') === 'fourth' ? 'selected' : '' }}>Fourth</option>
                                    <option value="fifth" {{ old('ordinalNumber') === 'fifth' ? 'selected' : '' }}>Fifth</option>
                                    <option value="next to last" {{ old('ordinalNumber') === 'next to last' ? 'selected' : '' }}>Next to last</option>
                                    <option value="last" {{ old('ordinalNumber') === 'last' ? 'selected' : '' }}>Last</option>
                                </select>

                                <select name="dayMonth">
                                    <option value="monday" {{ old('dayMonth') === 'monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="tuesday" {{ old('dayMonth') === 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="wednesday" {{ old('dayMonth') === 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="thursday" {{ old('dayMonth') === 'thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="friday" {{ old('dayMonth') === 'friday' ? 'selected' : '' }}>Friday</option>
                                    <option value="saturday" {{ old('dayMonth') === 'saturday' ? 'selected' : '' }}>Saturday</option>
                                    <option value="sunday" {{ old('dayMonth') === 'sunday' ? 'selected' : '' }}>Sunday</option>
                                    <option value="day" {{ old('dayMonth') === 'day' ? 'selected' : '' }}>Day</option>
                                    <option value="weekday" {{ old('dayMonth') === 'weekday' ? 'selected' : '' }}>Weekday</option>
                                    <option value="weekendday" {{ old('dayMonth') === 'weekendday' ? 'selected' : '' }}>Weekendday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="yearlyFields">
                        <div class="every label">
                            <label for="everyYear">Every</label>

                            <select name="amountYear" id="everyYear">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" {{ old('amountYear') == $i ? 'selected' : '' }}>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>

                            <label for="everyYear">Year(s)</label>
                        </div>
                    </div>
                </div>

                <h2 tabindex="0">Dynamic event</h2>

                <p id="dynamic-help" class="sr-only">
                    Press Space to enable or disable the dynamic event option.
                </p>

                <div class="containerRadio label">
                    <p id="dynamic-help" class="sr-only">
                        Press Space to enable or disable the dynamic event option.
                    </p>
                    <input
                        type="checkbox"
                        id="dynamic"
                        name="dynamic"
                        value="1"
                        aria-label="Dynamic event. Press Space to enable or disable."
                        {{ old('dynamic') ? 'checked' : '' }}
                    >

                    <label for="dynamic">Dynamic event</label>
                </div>

                <div id="dynamicEventBox" style="display:none;">
                    <h3 id="routeHeading" tabindex="0">Select route</h3>

                    <p id="grid-help" class="sr-only">
                        Use Tab to move between cells. Press Space or Enter to select or deselect a cell.
                    </p>

                    <div
                        class="miniGrid"
                        role="grid"
                        aria-labelledby="routeHeading"
                        aria-describedby="grid-help"
                    >
                        @foreach($cells as $cell)
                            <div
                                class="miniGridCell {{ $cell->is_available ? 'available' : 'occupied' }}"
                                role="gridcell"
                                tabindex="0"
                                aria-label="Cell {{ $cell->x_coordinate }}, {{ $cell->y_coordinate }}"
                                aria-selected="false"
                                data-grid-id="{{ $cell->id }}"
                                data-x="{{ $cell->x_coordinate }}"
                                data-y="{{ $cell->y_coordinate }}"
                            >
                            </div>
                        @endforeach
                    </div>
                    <input
                        type="hidden"
                        name="route_cells"
                        id="routeCells"
                        value="{{ old('route_cells') }}"
                        aria-hidden="true"
                    >

                    <x-formInput
                        name="speed"
                        label="Speed (hours per cell)"
                        type="number"
                        :value="old('speed', 1)"
                        min="1"
                    />
                </div>

                <button type="submit">Create event</button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[type="number"]').forEach((input) => {

            const reader = document.getElementById(input.name + '-reader');

            function updateReader() {
                const value = Number(input.value);

                if (isNaN(value) || !reader) {
                    return;
                }

                const spokenText =
                    value < 0
                        ? `minus ${Math.abs(value)}`
                        : `${value}`;

                reader.textContent = spokenText;
            }

            input.addEventListener('input', updateReader);
            input.addEventListener('change', updateReader);

            input.addEventListener('focus', () => {
                const value = Number(input.value);

                if (isNaN(value) || !reader) {
                    return;
                }

                reader.textContent = '';

                setTimeout(() => {
                    reader.textContent =
                        value < 0
                            ? `minus ${Math.abs(value)}`
                            : `${value}`;
                }, 100);
            });

            updateReader();
        });
        
        const startDate = document.getElementById('startDate');
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Tab' && !event.shiftKey) {
                const active = document.activeElement;
                if (active.closest('.timepicker') || active.closest('[role="dialog"]')) {
                    event.preventDefault();
                    startDate.focus();
                }
            }
        });
    </script>

</body>

</html>