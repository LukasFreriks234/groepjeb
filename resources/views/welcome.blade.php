<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
    <script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
    <script src="{{ asset('js/delete.js') }}" defer></script>
    <link href="{{ asset('css/functionTableStyle.css')}}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/gridStyle.css')}}" type="text/css" rel="stylesheet"/>
    <title>Metropolis</title>
</head>
<body>

<div class="container">
<x-grid :cells="$cells"/>
<x-functionTable :functions="$functions" :categories="$categories"/>
</div>
</body>
</html>
