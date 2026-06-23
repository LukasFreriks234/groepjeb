<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\MainRoad;
use App\Models\Event;

class MainRoadController extends Controller
{
     public function toggleMainRoad(GridCell $cell)
    {
        if (!$cell->isBorder()) {
            return response()->json([
                'success' => false
            ], 403);
        }

        if ($cell->mainRoad) {

            $cell->mainRoad()->delete();

            return response()->json([
                'success' => true,
                'mainRoad' => false
            ]);

        } else {

            MainRoad::create([
                'grid_cell_id' => $cell->id
            ]);

            return response()->json([
                'success' => true,
                'mainRoad' => true
            ]);
        }
    }

    public function calculateRoute(Request $request)
    {
        $start = GridCell::findOrFail($request->start_cell_id);
        $end = GridCell::findOrFail($request->end_cell_id);

        $route = $this->buildRoute($start, $end);

        return response()->json([
            'route' => $route
        ]);
    }

    private function buildRoute($start, $end)
    {
        $route = [];

        $x = $start->x_coordinate;
        $y = $start->y_coordinate;

        $tx = $end->x_coordinate;
        $ty = $end->y_coordinate;

        $route[] = [$x, $y];

        while ($x != $tx) {
            $x += ($tx > $x) ? 1 : -1;
            $route[] = [$x, $y];
        }

        while ($y != $ty) {
            $y += ($ty > $y) ? 1 : -1;
            $route[] = [$x, $y];
        }

        return $route;
    }

}
