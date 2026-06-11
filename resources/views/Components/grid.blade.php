@props(['cells', 'categories','eventGridCells'])

<div class="simulationContainer">
    <main class="citySection">
        <h2>City area</h2>

        <p id="gridKeyboardInstructions" class="sr-only">
            Keyboard instructions: use Tab or Shift Tab to move to the grid. Inside the grid, use the arrow keys to move between cells. Press Enter or Space on a filled cell to select the function. Press Enter or Space on another cell to move or swap it. Events can be placed on cells that already contain a matching function. Use the remove button to remove a function or event from a filled cell.
        </p>

        <div 
            class="metropolisGrid" 
            role="grid"
            aria-describedby="gridKeyboardInstructions"
        >

        @php
        $arrEventGridCells = $eventGridCells->sortBy('route_order');
        @endphp


            @foreach($cells as $cell)
                @php
                    $readableRow = $cell->y_coordinate + 1;
                    $readableColumn = $cell->x_coordinate + 1;
                    $hasFunction = $cell->cityFunction !== null;
                    $hasEvents = $cell->events && $cell->events->count() > 0;

                    $eventNames = $hasEvents
                        ? $cell->events->pluck('name')->join(', ')
                        : '';

                    $cellLabel = 'Grid cell row ' . $readableRow . ', column ' . $readableColumn . '. ';

                    if ($hasFunction) {
                        $cellLabel .= 'Contains ' . $cell->cityFunction->name . '. ';
                    } else {
                        $cellLabel .= 'Empty cell. ';
                    }

                    if ($hasEvents) {
                        $cellLabel .= 'Events: ' . $eventNames . '. ';
                    }

                    if ($hasFunction) {
                        $cellLabel .= 'Press Enter or Space to select this function to move it.';
                    } else {
                        $cellLabel .= 'Press Enter or Space to place a selected function here.';
                    }
                @endphp

                <div 
                    class="gridCell {{ $cell->is_available ? 'available' : 'occupied' }}"
                    data-id="{{ $cell->id }}"
                    data-x="{{ $cell->x_coordinate }}"
                    data-y="{{ $cell->y_coordinate }}"
                    role="gridcell"
                    tabindex="0"
                    aria-label="{{ $cellLabel }}"
                >
                    @if($hasFunction)
                        <img
                            src="{{ asset($cell->cityFunction->image) }}"
                            alt=""
                            aria-hidden="true"
                            tabindex="-1"
                            class="gridImage draggableGridFunction"
                            draggable="true"
                            data-function-id="{{ $cell->cityFunction->id }}"
                            data-function-name="{{ $cell->cityFunction->name }}"
                            data-from-cell-id="{{ $cell->id }}"
                            data-category="{{ $cell->cityFunction->category }}"
                        >
                    @endif
    
                    @if($hasEvents)
                        <div class="gridEvents" aria-hidden="true">
                            @foreach($cell->events as $event)
                                @foreach ($arrEventGridCells as $gridCell)
                                    @if($gridCell->grid_cell_id == $cell->id && $gridCell->event_id == $event->id)
                                    @php
                                        $order = $gridCell->route_order;
                                        @endphp
                                    @endif
                                @endforeach
                                <img
                                    src="{{ asset($event->image_url) }}"
                                    alt=""
                                    aria-hidden="true"
                                    tabindex="-1"
                                    class="gridEventImage draggableGridEvent"
                                    draggable="true"
                                    data-event-id="{{ $event->id }}"
                                    data-event-name="{{ $event->name }}"
                                    data-from-cell-id="{{ $cell->id }}"
                                    event-speed="{{ $event->speed }}"
                                    route-state="{{ $order }}"
                                >
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </main>
</div>

<div
    id="functionTooltip"
    class="functionTooltip hidden"
    role="tooltip"
>
    <div
        id="tooltipAnnouncement"
        class="sr-only"
        aria-live="assertive"
        aria-atomic="true"
    ></div>

    <ul id="tooltipEffectsList">
        @foreach($categories as $category)
            <li>
                {{ $category->category }}:
                <span data-tooltip-effect-category="{{ $category->category }}">
                    0
                </span>
            </li>
        @endforeach

        <li>
            Quality of Life:
            <span id="tooltipQualityOfLife">0</span>
        </li>
    </ul>
</div>