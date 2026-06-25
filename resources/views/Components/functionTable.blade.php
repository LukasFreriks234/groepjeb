<div class="topNav">
    <button class="tabButton active" data-target="functionsTable">
        Functions
    </button>
    <button id="tabButtonEvent" class="tabButton" data-target="eventsTable">
        Events
    </button>
</div>

<div id="functionsTable" class="tabContent" style="display:block;">
    <div class="filters">
        <div>
            <label class="sr-only" for="myInput">Search functions</label>
            <input type="search" id="myInput" placeholder="Type a function name..."><br>

            @php
                $arrCategories = $categories->toArray();
                $arrCategorie = array_column($arrCategories, 'category');
                array_multisort($arrCategorie, SORT_ASC, $arrCategories);
                $i = 1;
            @endphp

            <fieldset>
                <legend>Filters:</legend>
            @foreach($arrCategories as $category)
                <input type="checkbox" id="category{{ $i }}" class="functionFilter" name="category{{ $i }}"
                    value="{{ $category['category'] }}">
                <label for="category{{ $i }}">{{ $category['category'] }}</label><br>

                @php
                    $i++;
                @endphp
            @endforeach
            </fieldset>
        </div>
    </div>

    @php
        $arrFunctions = $functions->toArray();
        $arrFunctionName = array_column($arrFunctions, 'name');
        $arrFunctionCategory = array_column($arrFunctions, 'category');
        array_multisort($arrFunctionCategory, SORT_ASC, $arrFunctionName, SORT_ASC, $arrFunctions);
    @endphp

    <p id="functionKeyboardDragInstructions" class="sr-only">
        Keyboard instructions: use Tab or Shift Tab to move through the function list. Press Enter or Space on a
        function to select it. Then move to a grid cell and press Enter or Space to place it.
    </p>

    <ul id="functionsList" tabindex="-1" aria-label="functions">
        @foreach($arrFunctions as $function)
            <li tabindex="0" id="function{{ $function['id'] }}" class="functionItem keyboardDraggableFunction"
                draggable="true" data-function-id="{{ $function['id'] }}" data-category="{{ $function['category'] }}"
                role="button"
                aria-label="Select function {{ $function['name'] }} in category {{ $function['category'] }} to place it in the grid. Effects: Safety {{ data_get($function, 'effects.Safety', 0) }}, Recreation {{ data_get($function, 'effects.Recreation', 0) }}, Environmental Quality {{ data_get($function, 'effects.Environmental Quality', 0) }}, Services {{ data_get($function, 'effects.Services', 0) }}, Mobility {{ data_get($function, 'effects.Mobility', 0) }}.">
                <div class="functionImage">
                    <img src="{{ asset($function['image']) }}" alt="{{ $function['name'] }}"
                        data-category="{{ $function['category'] }}">
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
            <input type="text" id="eventSearch" placeholder="Search events.." aria-label="Search events"><br>

            <input type="checkbox" id="typeOneOff" class="eventFilter" value="one-off">
            <label for="typeOneOff">One-off</label><br>

            <input type="checkbox" id="typeRecurring" class="eventFilter" value="recurring">
            <label for="typeRecurring">Recurring</label><br>

            <input type="checkbox" id="typeDynamic" class="eventFilter" value="dynamic">
            <label for="typeDynamic">Dynamic</label><br>
        </div>
    </div>

    @php
        $arrEvents = $events->sortBy('name');
    @endphp

    <p id="eventKeyboardDragInstructions" class="sr-only">
        Keyboard instructions: use Tab or Shift Tab to move through the event list. Press Enter or Space on an event to
        select it. Then move to a grid cell and press Enter or Space to place it.
    </p>

    <ul id="eventsList" tabindex="-1" aria-label="events" aria-describedby="eventKeyboardDragInstructions">
        @foreach($arrEvents as $event)
            @php
                $eventType = $event->recurring_id ? 'recurring' : 'one-off';
                $eventTypeLabel = $event->recurring_id ? 'Recurring' : 'One-off';
                $isDynamic = $event->dynamic ? '1' : '0';
                $isGlobal = $event->is_global ?? false;
            @endphp

            @if($isDynamic)
            <li 
                tabindex="0"
                id="event{{ $event->id }}" 
                class="dynamicEventItem"
                data-event-id="{{ $event->id }}"
                data-event-name="{{ $event->name }}"
                data-type="{{ $eventType }}"
                data-dynamic="{{ $isDynamic }}"
                data-image="{{ $event->image_url }}"
                data-frequency="{{ $event->recurring?->frequency }}"
                data-amount="{{ $event->recurring?->amount }}"
                data-weekly='@json($event->recurring?->weekly?->pluck("weekday"))'
                data-date-number='@json($event->recurring?->monthly?->pluck("day_of_month") ?? [])'
                data-ordinal-number='@json($event->recurring?->monthly?->pluck('ordinal_number') ?? [])'
                data-weekday='@json($event->recurring?->monthly?->pluck('weekday') ?? [])'
                data-start-time="{{ $event->time }}"
                role="button"
                aria-label="Select event {{ $event->name }} of type {{ $eventTypeLabel }}{{ $event->dynamic ? ' and dynamic' : '' }} to place it in the grid."
            >
                <div class="functionImage">
                    <img 
                        src="{{ asset($event->image_url) }}" 
                        alt="{{ $event->name }}"
                        draggable="false"
                    >
                </div>

                <div class="functionDescription">
                    <p class="functionName">{{ $event->name }}</p>

                    <p class="functionCategory">
                        Dynamic
                    </p>
                </div>

                
                <label class="switch">
                    <input type="checkbox" active="false" data-event-id="{{ $event->id }}" aria-label="Activate {{ $event->name }}">
                    <span class="slider round"></span>
                </label>

            </li>
            @else
            <li 
                tabindex="0"
                id="event{{ $event->id }}" 
                class="functionItem eventItem draggableEventItem"
                draggable="true"
                data-event-id="{{ $event->id }}"
                data-event-name="{{ $event->name }}"
                data-type="{{ $eventType }}"
                data-dynamic="{{ $isDynamic }}"
                data-global="{{ $isGlobal ? '1' : '0' }}"
                data-image="{{ $event->image_url }}"
                data-frequency="{{ $event->recurring?->frequency }}"
                data-amount="{{ $event->recurring?->amount }}"
                data-weekly='@json($event->recurring?->weekly?->pluck("weekday"))'
                data-date-number='@json($event->recurring?->monthly?->pluck("day_of_month") ?? [])'
                data-ordinal-number='@json($event->recurring?->monthly?->pluck('ordinal_number') ?? [])'
                data-weekday='@json($event->recurring?->monthly?->pluck('weekday') ?? [])'
                data-start-time="{{ $event->time }}"
                role="button"
                aria-label="Select event {{ $event->name }} of type {{ $eventTypeLabel }}{{ $event->dynamic ? ' and dynamic' : '' }}{{ $isGlobal ? ', currently active as global event' : '' }} to place it in the grid."
            >
                <div class="functionImage">
                    <img 
                        src="{{ asset($event->image_url) }}" 
                        alt="{{ $event->name }}"
                    >
                </div>

                <div class="functionDescription">
                    <p class="functionName">{{ $event->name }}</p>

                    <p class="functionCategory">
                        {{ $eventTypeLabel }}
                    </p>
                </div>

                <label class="switch">
                    <input type="checkbox" aria-label="Activate {{ $event->name }}">
                    <span class="slider round"></span>
                </label>
            </li>
            @endif
        @endforeach
    </ul>
</div>