<?php

use App\Models\EventgridCell;

public function saveRoute(Request $request)
{
    EventgridCell::create([
        'event_id' => $request->event_id,
        'grid_cell_id' => $request->grid_cell_id,
        'route_order' => $request->route_order,
    ]);

    return response()->json([
        'success' => true
    ]);
}