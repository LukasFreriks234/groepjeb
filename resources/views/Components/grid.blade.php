@props(['cells', 'categories'])

<div class="simulationContainer">
    <main class="citySection">
        <h2>City area</h2>

        <div class="metropolisGrid" role="grid">
            @foreach($cells as $cell)
                <div 
                    class="gridCell {{ $cell->is_available ? 'available' : 'occupied' }}"
                    data-id="{{ $cell->id }}"
                    data-x="{{ $cell->x_coordinate }}"
                    data-y="{{ $cell->y_coordinate }}"
                    role="gridcell"
                    tabindex="0"
                    aria-label="Cel row {{ $cell->y_coordinate }}, column {{ $cell->x_coordinate }}."
                >
                    @if(!$cell->is_available && $cell->cityFunction)
                        <img
                            src="{{ asset($cell->cityFunction->image) }}"
                            alt="{{ $cell->cityFunction->name }}"
                            class="gridImage draggableGridFunction"
                            draggable="true"
                            data-function-id="{{ $cell->cityFunction->id }}"
                            data-from-cell-id="{{ $cell->id }}"
                            data-category="{{ $cell->cityFunction->category }}"
                        >
                    @endif
                </div>
            @endforeach
        </div>
    </main>
</div>

<!-- TOOLTIP -->
<div id="functionTooltip" class="functionTooltip" tabindex="0" aria-label="Effect values" aria-live="polite" hidden>
    <ul id="tooltipEffectsList">
        @foreach($categories as $category)
            <li>
                {{ $category->category }}:
                <span data-tooltip-effect-category="{{ $category->category }}">0</span>
            </li>
        @endforeach

        <li>
            Quality of Life:
            <span id="tooltipQualityOfLife">0</span>
        </li>
    </ul>
</div>