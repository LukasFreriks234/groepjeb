<div class="topNav">
    <button class="tabButton active" data-target="functionsTable">
        Functions
    </button>
    <button class="tabButton" data-target="eventsTable">
        Events
    </button>
</div>

<div id="functionsTable" class="tabContent" style="display:block;">
    <div class="filters">
        <div>
            <input 
                type="text" 
                id="myInput" 
                placeholder="Search for functions.." 
                aria-label="Search functions"
            ><br>

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

    <p id="keyboardDragInstructions" class="sr-only">
        Keyboard instructions: use Tab or Shift Tab to move through the function list. Press Enter or Space on a function to select it. Then move to a grid cell and press Enter or Space to place it.
    </p>

    <ul 
        id="functionsList" 
        tabindex="-1" 
        aria-label="functions"
        aria-describedby="keyboardDragInstructions"
    >
        @foreach($arrFunctions as $function)
            <li 
                tabindex="0"
                id="function{{ $function['id'] }}" 
                class="functionItem keyboardDraggableFunction"
                data-function-id="{{ $function['id'] }}"
                data-category="{{ $function['category'] }}"
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

<div id="eventsTable" class="tabContent" style="display:none;">
    <div class="filters">
        <div>
            <input type="text" id="eventSearch" placeholder="Search events.."><br>

            <input type="checkbox" id="typeOneOff" class="eventFilter" value="one-off">
            <label for="typeOneOff">One-off</label><br>

            <input type="checkbox" id="typeRecurring" class="eventFilter" value="recurring">
            <label for="typeRecurring">Recurring</label><br>
        </div>
</div>


    <?php 
        $arrEvents = $events->toArray();
        $arrEventName = array_column($arrEvents, 'name');
        array_multisort($arrEventName, SORT_ASC, $arrEvents);
    ?>
    
    <ul id="eventsList">
        @foreach($arrEvents as $event)
            <li 
                id="event{{ $event['id'] }}" 
                class="functionItem"
                data-event-id="{{ $event['id'] }}"
                data-type="{{ $event['type'] }}"
            >
                <div class="functionImage">
                    <img 
                        src="{{ asset($event['image_url']) }}" 
                        alt="{{ $event['name'] }}"
                    >
                </div>

                <div>
                    <p class="functionName">{{ $event['name'] }}</p>
                    <p class="functionCategory">{{ $event['type'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>