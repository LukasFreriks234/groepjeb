<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview - Events</title>

    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet" />
</head>

<body>
    <x-navbar />

    <div class="overviewContent">
        <nav class="overviewTabs" aria-label="Overview navigation">
            <a href="{{ route('functions.index') }}" class="overviewTab">
                Functions
            </a>

            <a href="{{ route('groups.index') }}" class="overviewTab">
                Groups
            </a>

            <a
                href="{{ route('events.index') }}"
                class="overviewTab active"
                aria-current="page"
            >
                Events
            </a>
        </nav>

        <div class="topbar">
            <h1>All events:</h1>

            <a href="{{ route('events.create') }}">
                <button class="createButton" type="button">
                    Create event
                </button>
            </a>
        </div>

        <ul id="functionsList">
            @forelse($events as $event)
                <li class="functionItem eventItem">
                    <div class="functionNameImage">
                        <div class="functionImage">
                            <img
                                src="{{ asset($event->image_url) }}"
                                alt="{{ $event->name }}"
                            >
                        </div>

                        <div>
                            <p class="functionName">
                                {{ $event->name }}
                            </p>

                            <p class="functionCategory">
                                {{ $event->recurring_id ? 'Recurring' : 'One-off' }}
                                @if($event->dynamic)
                                    / Dynamic
                                @endif
                            </p>
                        </div>
                    </div>
                </li>
            @empty
                <li class="functionItem">
                    <p>No events have been created yet.</p>
                </li>
            @endforelse
        </ul>
    </div>
</body>

</html>