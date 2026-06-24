<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
<script src="{{ asset('js/dynamicEvent.js') }}" defer></script>
<script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
<script src="{{ asset('js/delete.js') }}" defer></script>
<script src="{{ asset('js/switchFunctionEvent.js') }}" defer></script>
<script src="{{ asset('js/mainroads.js') }}" defer></script>
<script src="{{ asset('js/simulationTimer.js') }}?v={{ filemtime(public_path('js/simulationTimer.js')) }}" defer></script>
<script src="{{ asset('js/dayNight.js') }}" defer></script>


<link href="{{ asset('css/functionTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/gridStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/effectTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/layout.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/functionEventsTableStyle.css') }}" type="text/css" rel="stylesheet"/>
<link href="{{ asset('css/eventTableStyle.css') }}" type="text/css" rel="stylesheet"/>

<title>Metropolis</title>

</head>
<body>
    <x-navbar/>

<div class="container">
    <div class="gridSection">
        <div class="global-events-zone" data-global-drop-zone>
            <div class="global-events-zone-header">
                <span class="global-events-zone-label">Global Events</span>
                <span class="global-events-zone-hint">Drop events here to apply to entire grid</span>
            </div>

            <div class="global-events-zone-items">
                @if(isset($globalEvents) && $globalEvents->count() > 0)
                    @foreach($globalEvents as $globalEvent)
                        <div class="global-event-chip" data-global-event-id="{{ $globalEvent->id }}">
                            <img
                                src="{{ asset($globalEvent->image_url) }}"
                                alt=""
                                class="global-event-chip-icon"
                            >

                            <span class="global-event-chip-name">{{ $globalEvent->name }}</span>

                            <button
                                class="global-event-remove"
                                type="button"
                                data-remove-global="{{ $globalEvent->id }}"
                                aria-label="Remove {{ $globalEvent->name }} from global events"
                            >&times;</button>

                            <div class="global-event-tooltip hidden">
                                <div class="global-event-tooltip-header">
                                    <img
                                        src="{{ asset($globalEvent->image_url) }}"
                                        alt=""
                                        class="global-event-tooltip-icon"
                                    >

                                    <div>
                                        <strong>{{ $globalEvent->name }}</strong>

                                        @if($globalEvent->name === 'Day')
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Active: 6:00 - 18:00
                                            </span>
                                        @elseif($globalEvent->name === 'Night')
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Active: 18:00 - 6:00
                                            </span>
                                        @elseif($globalEvent->length)
                                            <br>
                                            <span class="global-event-tooltip-duration">
                                                Duration: {{ $globalEvent->length }} {{ $globalEvent->length_unit }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="global-event-tooltip-effects">
                                    <strong>Effects:</strong>

                                    <ul>
                                        @foreach($globalEvent->effects as $effect)
                                            <li class="{{ $effect->effect > 0 ? 'positiveEffect' : ($effect->effect < 0 ? 'negativeEffect' : 'neutralEffect') }}">
                                                {{ $effect->category_name }}: {{ $effect->effect > 0 ? '+' : '' }}{{ $effect->effect }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="global-events-zone-empty">No global events active</span>
                @endif
            </div>
        </div>

        <x-grid
            :cells="$cells"
            :categories="$categories"
            :event-grid-cells="$eventGridCells"
        />

        <div class="road-controls" aria-label="Main road controls">
            <button id="toggle-mainroad-button" type="button">
                Show Main Road Overlay
            </button>

            <button id="createRouteButton" type="button">
                Create Route
            </button>
        </div>

        <div class="gridSavePanel">
            <form method="POST" action="{{ route('grid.save') }}" class="gridSaveForm">
                @csrf

                
                <input
                    id="savedGridName"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Grid layout"
                    maxlength="255"
                    required
                >

                <button type="submit">Save grid</button>
            </form>

            @if (session('status'))
                <p class="gridSaveMessage">{{ session('status') }}</p>
            @endif

            @error('name')
                <p class="gridSaveError">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="sidebar">
        <div class="effectsSection">
            <x-effectTable
                :categories="$categories"
                :effect-totals="$effectTotals"
                :quality-of-life="$qualityOfLife"
            />
        </div>

        <div class="functionsSection">
            <x-functionTable :functions="$functions" :categories="$categories" :events="$events" />
        </div>
    </div>
</div>

</body>
</html>
