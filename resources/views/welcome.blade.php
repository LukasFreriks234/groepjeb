<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
    <script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
    <script src="{{ asset('js/delete.js') }}" defer></script>
    <script src="{{ asset('js/switchFunctionEvent.js') }}" defer></script>
    <script src="{{ asset('js/dynamicEvent.js') }}" defer></script>
    <script src="{{ asset('js/simulationTimer.js') }}?v={{ filemtime(public_path('js/simulationTimer.js')) }}" defer></script>

    <link href="{{ asset('css/functionTableStyle.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/gridStyle.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/effectTableStyle.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/layout.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/functionEventsTableStyle.css') }}" type="text/css" rel="stylesheet"/>

    <title>Metropolis</title>
</head>
<body>
    <x-navbar />

    <div class="container">
        <div class="gridSection">
            <x-grid :cells="$cells" :categories="$categories" :eventGridCells="$eventGridCells"/>
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