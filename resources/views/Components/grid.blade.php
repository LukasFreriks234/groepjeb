@props(['cells', 'categories', 'eventGridCells'])

@php
    $columns = $cells->max('x_coordinate') + 1;
    $rows = $cells->max('y_coordinate') + 1;
@endphp

<div class="simulationContainer">
    <main class="citySection">
        @if(auth()->check() && auth()->user()->role === 'policymaker')
            <a
                href="{{ route('grid.export-pdf') }}"
                class="navButton"
                aria-label="Export the current simulation to a PDF report"
            >
                Save as PDF
            </a>
        @endif

        <div id="gridInstructionAnnouncement"
        class="sr-only"
        role="status"
        aria-live="polite"
        aria-atomic="true"></div>

        <h2>City area</h2>

        <p id="gridKeyboardInstructions" class="sr-only">
            Keyboard instructions: use Tab or Shift Tab to move to the grid. Inside the grid, use the arrow keys to move between cells. Press Enter or Space on a filled cell to select the function. Press Enter or Space on another cell to move or swap it. Events can be placed on cells that already contain a matching function. Use the remove button to remove a function or event from a filled cell.
        </p>

        <div
            class="metropolisGrid"
            role="group"
            aria-label="The grid exists out of {{ $columns }} columns, {{ $rows }} rows"
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
                        $cellLabel .= 'Contains no function. ';
                    }

                    if ($hasEvents) {
                        $cellLabel .= 'Events: ' . $eventNames . '. ';
                    }

                    if (!$hasFunction && !$hasEvents) {
                        $cellLabel .= 'Empty cell.';
                    }
                @endphp

                <div
                    class="gridCell {{ $cell->is_available ? 'available' : 'occupied' }} {{ $cell->mainRoad ? 'main-road' : '' }}"
                    data-id="{{ $cell->id }}"
                    data-x="{{ $cell->x_coordinate }}"
                    data-y="{{ $cell->y_coordinate }}"
                    role="button"
                    tabindex="0"
                    aria-label="{{ $cellLabel }}"
                    aria-rowindex="{{ $readableRow }}"
                    aria-colindex="{{ $readableColumn }}"
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
                        <div class="gridEvents">
                            @foreach($cell->events as $event)
                                @foreach($arrEventGridCells as $gridCell)
                                    @if($gridCell->grid_dynamics_id == $cell->id && $gridCell->event_id == $event->id)
                                        @php
                                            $order = $gridCell->route_order;
                                        @endphp
                                    @endif
                                @endforeach

                                <img
                                    src="{{ asset($event->image_url) }}"
                                    alt="{{ $event->name }}"
                                    class="gridEventImage draggableGridEvent"
                                    draggable="true"
                                    tabindex="0"
                                    role="button"
                                    aria-label="Move event {{ $event->name }} from this grid cell"
                                    data-event-id="{{ $event->id }}"
                                    data-event-name="{{ $event->name }}"
                                    data-from-cell-id="{{ $cell->id }}"
                                    event-speed="{{ $event->speed }}"
                                    route-state="{{ $order ?? 0 }}"
                                    dynamic-event="{{ $event->dynamic }}"
                                >
                            @endforeach
                        </div>
                    @endif

                    @if($cell->mainRoad)
                        <div class="main-road-icon" aria-hidden="true">
                            <img
                                src="{{ asset('images/mainroad.png') }}"
                                alt=""
                            >
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