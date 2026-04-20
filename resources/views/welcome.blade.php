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
        <div class="simulation-container">
    <main class="city-section">
        <h2>City area</h2>
        <div class="metropolis-grid" role="grid">
            @foreach($cells as $cell)
                <div class="grid-cell {{ $cell->is_available ? 'available' : 'occupied' }}" 
                     data-id="{{ $cell->id }}"
                     role="gridcell"
                     tabindex="0"
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

        <input type="checkbox" id="category1" class="function_filter" name="category1" value="Germany">
        <label for="category1"> Germany</label><br>
        <input type="checkbox" id="category2" class="function_filter" name="category2" value="Sweden">
        <label for="category2"> Sweden</label><br>
        <input type="checkbox" id="category3" class="function_filter" name="category3" value="UK">
        <label for="category3"> UK</label><br>


            <ul id="functions_list">
                <li id="function-1" class="function-item" draggable="true">
                    <div class="function_image">
                        <img src="{{ url('/images/photo-masthead-375-BoK_p8LG.webp') }}">
                    </div>
                    <div>
                        <p class="function_name">Alfreds Futterkiste</p>
                        <p class="function_category">Germany</p>
                    </div>
                </li>

                <li id="function-2" class="function-item" draggable="true">
                    <div class="function_image">
                        <img src="{{ url('/images/UtsiktverJrvsfrnStenegrd_CMSTemplate.jpg') }}">
                    </div>
                    <div>
                        <p class="function_name">Berglunds snabbkop</p>
                        <p class="function_category">Sweden</p>
                    </div>
                </li>

                <li id="function-3" class="function-item" draggable="true">
                    <div class="function_image">
                        <img src="{{ url('/images/LY-AR6.webp') }}">
                    </div>
                    <div>
                        <p class="function_name">Island Trading</p>
                        <p class="function_category">UK</p>
                    </div>
                </li>

                <li id="function-4" class="function-item" draggable="true">
                    <div class="function_image">
                        <img src="{{ url('/images/Essen-Südviertel_Luft.jpg') }}">
                    </div>
                    <div>
                        <p class="function_name">Koniglich Essen</p>
                        <p class="function_category">Germany</p>
                    </div>
                </li>
            </ul>
    </div>
</body>
</html>
