<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>

    <link rel="stylesheet" href="{{ asset('css/navbarStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">

    <script src="{{ asset('js/createEvent.js') }}" defer></script>
</head>

<body>

    <x-navbar />

    <h1>Create Event</h1>

    @if ($errors->any())
        <div style="color:red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="container">
        <div class="form-section">
            <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                @csrf

                <x-formInput name="name" label="Name" :value="old('name')" />

                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*" alt="Upload an image for the new function">

                <h2>Effect while active</h2>

                <x-formInput name="Safety" label="Safety" type="number" :value="old('Safety', 0)" min="-10" max="10" />

                <x-formInput name="Recreation" label="Recreation" type="number" :value="old('Recreation', 0)" min="-10" max="10" />

                <x-formInput name="Environmental_Quality" label="Environmental Quality" type="number"
                    :value="old('Environmental_Quality', 0)" min="-10" max="10" />

                <x-formInput name="Services" label="Services" type="number" :value="old('Services', 0)" min="-10" max="10" />

                <x-formInput name="Mobility" label="Mobility" type="number" :value="old('Mobility', 0)" min="-10" max="10" />

                <h2>Event settings</h2>

                <div class="containerRadio">
                    <input type="radio" id="oneOff" name="typeEvent" value="oneOff" />
                    <label for="oneOff">One-off</label>
                </div>

                <div class="containerRadio">
                    <input type="radio" id="recurring" name="typeEvent" value="recurring" />
                    <label for="recurring">Recurring</label>
                </div>

                <div id="recurringFields">
                    <div>
                        <label>Frequency</label>
                        <select name="recurrencePattern">
                            <option value="">---</option>
                            <option value="daily">daily</option>
                            <option value="weekly">weekly</option>
                            <option value="monthly">monthly</option>
                            <option value="custom">custom</option>
                        </select>
                    </div>

                    <label>Active period</label>
                    <div class="input-with-unit">
                        <input type="number" name="activePeriod" />
                        <span>days</span>
                    </div>
                </div>

                <h2>Dynamic event</h2>

                <div class="containerRadio">
                    <input type="checkbox" id="dynamic" name="dynamic" value="1" />
                    <label for="dynamic">Dynamic event</label>
                </div>

                <div id="dynamicEventBox">
                    Route selector
                </div>

                <button type="submit">Create event</button>
            </form>
        </div>
    </div>
</body>

</html>