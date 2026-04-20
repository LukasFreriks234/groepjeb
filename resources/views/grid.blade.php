<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grid</title>
    <link rel="stylesheet" href="{{ asset('css/grid.css') }}">
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
    
</body>
</html>