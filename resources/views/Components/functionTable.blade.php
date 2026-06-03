<div id="functionsTable">
    <div class="filters">
        <div>
            <input type="text" id="myInput" placeholder="Search for functions.." aria-label="searchbar for functions"><br>

            <?php 
            $arrCategories = $categories->toArray();
            $arrCategorie = array_column($arrCategories, 'category');
            array_multisort($arrCategorie, SORT_ASC, $arrCategories);
            $i = 1;
            ?>

            @foreach($arrCategories as $category)
                <input 
                    type="checkbox" 
                    id="category{{ $i }}" 
                    class="functionFilter" 
                    name="category{{ $i }}" 
                    value="{{ $category['category'] }}"
                >
                <label for="category{{ $i }}">{{ $category['category'] }}</label><br>
                <?php $i++; ?>
            @endforeach
        </div>
    </div>

    <?php 
    $arrFunctions = $functions->toArray();
    $arrFunctionName = array_column($arrFunctions, 'name');
    $arrFunctionCategory = array_column($arrFunctions, 'category');
    array_multisort($arrFunctionCategory, SORT_ASC, $arrFunctionName, SORT_ASC, $arrFunctions);
    ?>

    <ul id="functionsList" tabindex="-1" aria-label="functions" >
        @foreach($arrFunctions as $function)
            <li tabindex="0"
                id="function{{ $function['id'] }}" 
                class="functionItem keyboardDraggableFunction"
                data-function-id="{{ $function['id'] }}"
                data-category="{{ $function['category'] }}"
                tabindex="0"
                role="button"
                aria-label="Select function {{ $function['name'] }} in category {{ $function['category'] }} to place it in the grid. Effects: Safety {{ data_get($function, 'effects.Safety', 0) }}, Recreation {{ data_get($function, 'effects.Recreation', 0) }}, Environmental Quality {{ data_get($function, 'effects.Environmental Quality', 0) }}, Services {{ data_get($function, 'effects.Services', 0) }}, Mobility {{ data_get($function, 'effects.Mobility', 0) }}."
            >
                <div class="functionImage">
                    <img
                        src="{{ asset($function['image']) }}" 
                        alt="{{ $function['name'] }}"
                        data-category="{{ $function['category'] }}"
                    >
                </div>

                <div class="functionDescription">
                    <p class="functionName">{{ $function['name'] }}</p>
                    <p class="sr-only">Category</p>
                    <p class="functionCategory" name="{{ $function['category'] }}">
                        {{ $function['category'] }}
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
</div>