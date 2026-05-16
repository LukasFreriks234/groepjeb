@props(['cells', 'categories'])

<div class="simulationContainer">
    <main class="citySection">
        <h2>City area</h2>

        <div class="metropolisGrid" role="grid">
            @foreach($cells as $cell)
                <div 
                    class="gridCell {{ $cell->is_available ? 'available' : 'occupied' }}"
                    data-id="{{ $cell->id }}"
                    role="gridcell"
                    tabindex="0"
                    aria-label="Cel on position {{ $cell->x_coordinate }}, {{ $cell->y_coordinate }}. Status: {{ $cell->is_available ? 'available' : 'occupied' }}"
                >
                    @if(!$cell->is_available && $cell->cityFunction)
                        <img 
                            src="{{ asset($cell->cityFunction->image) }}"
                            alt="{{ $cell->cityFunction->name }}"
                            class="gridImage"
                            data-category="{{ $cell->cityFunction->category }}"
                        >
                    @endif
                </div>
            @endforeach
        </div>
    </main>
</div>

<!-- TOOLTIP -->
<div id="functionTooltip" class="functionTooltip hidden">
    <ul id="tooltipEffectsList">
        @foreach($categories as $category)
            <li>
                {{ $category->category }}:
                <span data-tooltip-effect-category="{{ $category->category }}">0</span>
            </li>
        @endforeach
    </ul>
</div>