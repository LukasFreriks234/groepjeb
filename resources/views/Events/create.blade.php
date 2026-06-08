<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/createEvent.css') }}">
    <script src="{{ asset('js/createEvent.js') }}" defer></script>
</head>

<body>
    <h1>Create Event</h1>

    <div class="container">
        <div class="form-section">
            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div style="color:red;">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif


                <x-formInput name="name" label="Name" :value="old('name')" />

                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*" alt="Upload an image for the new function"
                    :value="old('name')">

                <!-- Link to destination -->

                <h2>Effect while active</h2>

                <x-formInput name="Safety" label="Safety" type="number" :value="old('Safety', 0)" min="-10" max="10" />

                <x-formInput name="Recreation" label="Recreation" type="number" :value="old('Recreation', 0)" min="-10"
                    max="10" />

                <x-formInput name="Environmental_Quality" label="Environmental Quality" type="number"
                    :value="old('Environmental_Quality', 0)" min="-10" max="10" />

                <x-formInput name="Services" label="Services" type="number" :value="old('Services', 0)" min="-10"
                    max="10" />

                <x-formInput name="Mobility" label="Mobility" type="number" :value="old('Mobility', 0)" min="-10"
                    max="10" />


                <h2>Event settings</h2>
                <fieldset>
                    <legend>Event type</legend>
                    <div class="containerRadio">
                        <input type="radio" id="oneOff" name="typeEvent" value="oneOff" checked />
                        <label for="oneOff">One-off</label>
                    </div>
                    <div class="containerRadio">
                        <input type="radio" id="recurring" name="typeEvent" value="recurring" />
                        <label for="recurring">Recurring</label>
                    </div>
                </fieldset>

                <!-- Length -->
                <label for="input-length" class="label">Length active</label>

                <div class="length-container">
                    <input id="input-length" name="length" type="number" value="{{ old('length') }}" required>

                    <select name="lengthUnit">
                        <option value="hours">Hours</option>
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                    </select>
                </div>

                <!-- Date -->
                <h2>Date and Time</h2>
                <div class="dateTime">
                    <label for="startTime" class="label">Start time</label>
                    <input type="time" id="startTime" name="startTime" required>
                    <label for="startDate" class="label">Start date</label>
                    <input type="date" id="startDate" name="startDate" required>
                </div>

                <!-- Recurring -->
                <div id="recurringFields">
                    <p class="information">The system will activate the event on the next possible day
                        accourding to
                        information before.</p>
                    <div class="label">
                        <label for="endDate">End date</label>
                        <input type="date" id="endDate" name="endDate">
                        <p class="informationEnddate">After this date the event will no longer activate.</p>
                        <p class="information">Want it to never end? Keep this field empty.</p>
                    </div>

                    <div>
                        <label class="label" for="frequency">Frequency</label>
                        <select name="recurrencePattern" id="frequency">
                            <option value="daily">daily</option>
                            <option value="weekly">weekly</option>
                            <option value="monthly">monthly</option>
                            <option value="yearly">yearly</option>
                        </select>
                    </div>

                    <div id="dailyFields">
                        <div class="every label">
                            <label for="everyDay">Every</label>
                            <select name="amountDay" id=everyDay>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="everyDay">Day(s)</label>
                        </div>
                    </div>

                    <div id="weeklyFields">
                        <div class="every label">
                            <label for=everyWeek>Every</label>
                            <select name="amountWeek" id="everyWeek">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="everyWeek">Week(s)</label>
                        </div>
                        <div class="weekDays label">
                            <div class="containerRadio">
                                <input type="checkbox" id="monday" name="weekdays[]" value="monday">
                                <label for="monday">Monday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="tuesday" name="weekdays[]" value="tuesday">
                                <label for="tuesday">Tuesday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="wednesday" name="weekdays[]" value="wednesday">
                                <label for="wednesday">Wednesday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="thursday" name="weekdays[]" value="thursday">
                                <label for="thursday">Thursday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="friday" name="weekdays[]" value="friday">
                                <label for="friday">Friday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="saturday" name="weekdays[]" value="saturday">
                                <label for="saturday">Saturday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="sunday" name="weekdays[]" value="sunday">
                                <label for="sunday">Sunday</label>
                            </div>
                        </div>
                    </div>

                    <div id="monthlyFields">
                        <div class="every label">
                            <label for=everyMonth>Every</label>
                            <select name="amountMonth" id="everyMonth">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="everyMonth">Month(s)</label>
                        </div>

                        <div class="containerRadio label">
                            <input type="radio" id="each" name="typeMonth" value="each" />
                            <label for="each">Each</label>
                        </div>
                        <div class="containerRadio label">
                            <input type="radio" id="onThe" name="typeMonth" value="onThe" />
                            <label for="onThe">On the...</label>
                        </div>

                        <div id="eachFields" class="label">
                            <label for="monthdate">Choose the days of the month this event will be active.</label>

                            <div class="month-days-grid">
                                <?php for ($day = 1; $day <= 31; $day++): ?>
                                <label class="day-box">
                                    <input type="checkbox" id="monthdate" name="monthDays[]" value="<?= $day ?>">
                                    <span><?= $day ?></span>
                                </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div id="onTheFields">
                            <div class="onThe label">
                                <select name="ordinalNumber">
                                    <option value="first">First</option>
                                    <option value="second">Second</option>
                                    <option value="third">Third</option>
                                    <option value="fourth">Fourth</option>
                                    <option value="fifth">Fifth</option>
                                    <option value="next to last">Next to last</option>
                                    <option value="last">Last</option>
                                </select>
                                <select name="dayMonth">
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                    <option value="day">Day</option>
                                    <option value="weekday">Weekday</option>
                                    <option value="weekendday">Weekendday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="yearlyFields">
                        <div class="every label">
                            <label for="everyYear">Every</label>
                            <select name="amountYear" id="everyYear">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="everyYear">Year(s)</label>
                        </div>
                    </div>
                </div>

                <h2>Dynamic event</h2>

                <div class="containerRadio label">
                    <input type="checkbox" id="dynamic" name="dynamic" value="1" />
                    <label for="dynamic">Dynamic event</label>
                </div>

                <div id="dynamicEventBox" style="display:none;">
                <h3>Select route</h3>

                <div 
                    class="miniGrid"
                >
                    @foreach($cells as $cell)

                        <div
                            class="miniGridCell {{ $cell->is_available ? 'available' : 'occupied' }}"
                            data-grid-id="{{ $cell->id }}"
                            data-x="{{ $cell->x_coordinate }}"
                            data-y="{{ $cell->y_coordinate }}"
                        >

                            @if(!$cell->is_available)

                                <img
                                    src="{{ asset($cell->cityFunction->image_url ?? '') }}"
                                    class="miniGridImage"
                                >

                            @endif

                        </div>

                    @endforeach
                </div>

                <input type="hidden" name="route_cells" id="routeCells">
            </div>
                <button type="submit">Create event</button>
            </form>
        </div>
    </div>
</body>

</html>