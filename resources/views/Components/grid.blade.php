<div class="simulation-container">
    <main class="city-section">
        <h2>City area</h2>
        <div class="metropolis-grid" role="grid">
            @foreach($cells as $cell)
                <div class="grid-cell {{ $cell->is_available ? 'available' : 'occupied' }}" 
                     data-id="{{ $cell->id }}"
                     role="gridcell"
                     tabindex="0"
                     aria-label="Cel on position {{ $cell->x_coordinate }}, {{ $cell->y_coordinate }}. Status: {{ $cell->is_available ? 'available' : 'occupied' }}">
                    @if(!$cell->is_available && $cell->cityFunction)
                        <img src="{{ asset('storage/' . $cell->cityFunction->image) }}" 
                            alt="{{ $cell->cityFunction->name }}" 
                            class="grid-image">
                    @endif 
                </div>
            @endforeach
        </div>
    </main>
</div>