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
                <div class="containerRadio">
                    <input type="radio" id="oneOff" name="typeEvent" value="oneOff" />
                    <label for="oneOff">One-off</label>
                </div>
                <div class="containerRadio">
                    <input type="radio" id="recurring" name="typeEvent" value="recurring" />
                    <label for="recurring">Recurring</label>
                </div>

                <div id="oneOffFields">
                    <h2>Date and Time</h2>
                    <label for="startDateOneOff" class="label">Start date</label>
                    <input type="date" id="startDateOneOff">
                    <label for=startTime class="label">start Time</label>
                    <input type="time" id=startTime>
                </div>

                <div id="recurringFields">
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
                                <input type="checkbox" id="monday">
                                <label for="monday">Monday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="tuesday">
                                <label for="tuesday">Tuesday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="wednesday">
                                <label for="wednesday">Wednesday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="thursday">
                                <label for="thursday">Thursday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="friday">
                                <label for="friday">Friday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="saturday">
                                <label for="saturday">Saturday</label>
                            </div>
                            <div class="containerRadio">
                                <input type="checkbox" id="sunday">
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
                            <input type="radio" id="each" name="typeMonth" />
                            <label for="each">Each</label>
                        </div>
                        <div class="containerRadio label">
                            <input type="radio" id="onThe" name="typeMonth" />
                            <label for="onThe">On the...</label>
                        </div>

                        <div id="eachFields" class="label">
                            <label for="monthdate">Choose the days of the month</label>

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
                                <select name="amountMonth">
                                    <option>First</option>
                                    <option>Second</option>
                                    <option>Third</option>
                                    <option>Fourth</option>
                                    <option>Fifth</option>
                                    <option>Next to last</option>
                                    <option>Last</option>
                                </select>
                                <select name="dayMonth">
                                    <option>Monday</option>
                                    <option>Tuesday</option>
                                    <option>Wednesday</option>
                                    <option>Thursday</option>
                                    <option>Friday</option>
                                    <option>Saturday</option>
                                    <option>Sunday</option>
                                    <option>Day</option>
                                    <option>Weekday</option>
                                    <option>Weekendday</option>
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
                    <h2>Date and Time</h2>
                    <div class="label">
                        <label for="startDate">Start date</label>
                        <input type="date" id="startDate">
                        <p class="information">The system will activate the event on the next possible day accourding to
                            information before.</p>
                    </div>
                    <div class="label">
                        <label for=startTime>Time</label>
                        <input type="time" id=startTime>
                    </div>
                    <div class="label">
                        <label for="endDate">End date</label>
                        <input type="date" id="endDate">
                        <p class="informationEnddate">After this date the event will no longer activate.</p>
                        <p class="information">Want it to never end? Keep this field empty</p>
                    </div>

                </div>

                <label for="input-length" class="label">Length active</label>

                <div class="length-container">
                    <input id="input-length" name="length" type="number" value="{{ old('length') }}" required>

                    <select name="lengthUnit">
                        <option value="hours">Hours</option>
                        <option value="days">Days</option>
                        <option value="weeks">Weeks</option>
                    </select>
                </div>

                <h2>Dynamic event</h2>

                <div class="containerRadio label">
                    <input type="checkbox" id="dynamic" name="dynamic" value="1" />
                    <label for="dynamic">Dynamic event</label>
                </div>

                <div id="dynamicEventBox">
                    <!-- HIER KOMT ROUTE -->
                    Route selector
                </div>

                <button type="submit">Create event</button>
            </form>
        </div>
    </div>
</body>

</html>