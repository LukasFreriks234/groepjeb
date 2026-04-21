<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
    <script src="{{ asset('js/gridDragDrop.js') }}" defer></script>
    <link href="{{ asset('css/styles.css')}}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/griddion.css')}}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/grid.css')}}" type="text/css" rel="stylesheet" />
    <title>Document</title>
</head>

<body>
    <div class="simulation-container">
        <main class="city-section">
            <h2>City area</h2>
            <div class="metropolis-grid" role="grid">
                @foreach($cells as $cell)
                    <div class="grid-cell {{ $cell->is_available ? 'available' : 'occupied' }}" data-id="{{ $cell->id }}"
                        role="gridcell" tabindex="0"
                        aria-label="Cel on position {{ $cell->x_coordinate }}, {{ $cell->y_coordinate }}. Status: {{ $cell->is_available ? 'avilable' : 'occupied' }}">
                        <!-- @if(!$cell->is_available && $cell->cityFunction)
                                <img src="{{ asset('storage/' . $cell->cityFunction->image_path) }}" 
                                    alt="{{ $cell->cityFunction->name }}" 
                                    class="grid-image">
                            @endif -->
                    </div>
                @endforeach
            </div>
        </main>
    </div>
    <div id="functions_table">
        <input type="text" id="myInput" placeholder="Search for names.."><br>
        @foreach($categories as $category)
            <input type="checkbox" id="category{{ $loop->iteration }}" class="function_filter"
                name="category{{ $loop->iteration }}" value="{{ $category->category }}">
            <label for="category{{ $loop->iteration }}">{{ $category->category }}</label><br>
        @endforeach


        <ul id="functions_list">
            @foreach($functions as $function)
                <li id="function-{{ $loop->iteration }}" class="function-item" draggable="true">
                    <div class="function_image">
                        <img src="{{ $function->image }}">
                    </div>
                    <div>
                        <p class="function_name">{{ $function->name }}</p>
                        <p class="function_category" name="{{ $function->category->category }}">{{ $function->category->category }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</body>

</html>