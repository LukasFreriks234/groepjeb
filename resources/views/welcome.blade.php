<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
    <script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
    <link href="{{ asset('css/styles.css')}}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/griddion.css')}}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/grid.css')}}" type="text/css" rel="stylesheet"/>
    <title>Document</title>
</head>
<body>

<grid :cells="$cells"/>
<x-functionTable/>

</body>
</html>
