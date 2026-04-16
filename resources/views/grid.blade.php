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
        <div class="metropolis-grid">
            @foreach($cells as $cell)
                <div class="grid-cell {{ $cell->is_available ? 'available' : 'occupied' }}" 
                     data-id="{{ $cell->id }}">
                    <!-- @if(!$cell->is_available)
                        <span class="destination-label">{{ $cell->image }}</span>
                    @endif -->
                </div>
            @endforeach
        </div>
    </main>
</div>
    
</body>
</html>