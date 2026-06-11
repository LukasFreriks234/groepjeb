<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;
use App\Models\Event;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
<<<<<<< HEAD
use App\Models\EventgridCell;

=======
>>>>>>> parent of 991f29f (Merge remote-tracking branch 'origin/time-event' into submain)
=======
>>>>>>> parent of b994d93 (dynamic)
=======
=======
use App\Models\EventgridCell;

>>>>>>> parent of 1e23ca4 (Revert "Merge branch 'frontend-dynamic-event' into submain")
>>>>>>> parent of 194b036 (fix)
=======
use App\Models\EventgridCell;

>>>>>>> parent of 1e23ca4 (Revert "Merge branch 'frontend-dynamic-event' into submain")
=======
>>>>>>> parent of 019c764 (Merge branch 'frontend-dynamic-event' into submain)

class GridCellController extends Controller
{
    private function cellsWithRelations()
    {
        return GridCell::with(['cityFunction', 'events.effects']);
    }

    private function calculateTotals()
    {
        $cells = $this->cellsWithRelations()->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        return [
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
        ];
    }

    public function index()
    {
        $cells = $this->cellsWithRelations()
            ->orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        $functions = Functions::with('effects')->get();
        $categories = Category::all();

        $events = Event::with([
            'effects',
            'recurring.weekly',
            'recurring.monthly',
        ])->get();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        return view('welcome', compact(
            'cells',
            'functions',
            'events',
            'categories',
            'effectTotals',
            'qualityOfLife'
        ));
    }

    public function assignFunction(Request $request)
    {
        $cell = GridCell::find($request->cell_id);

        if (!$cell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        $cell->destination_type = $request->function_id;
        $cell->is_available = false;
        $cell->save();

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function moveFunction(Request $request)
    {
        $fromCell = GridCell::with('events')->find($request->from_cell_id);
        $toCell = GridCell::with('events')->find($request->to_cell_id);

        if (!$fromCell || !$toCell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        if ($fromCell->id == $toCell->id) {
            return response()->json([
                'success' => true,
                'message' => 'Same cell',
            ]);
        }

        $fromFunctionId = $fromCell->destination_type;
        $toFunctionId = $toCell->destination_type;

        $fromEventIds = $fromCell->events->pluck('id')->toArray();
        $toEventIds = $toCell->events->pluck('id')->toArray();

        $toCell->destination_type = $fromFunctionId;
        $toCell->is_available = ($fromFunctionId || count($fromEventIds) > 0) ? false : true;
        $toCell->save();

        $fromCell->destination_type = $toFunctionId;
        $fromCell->is_available = ($toFunctionId || count($toEventIds) > 0) ? false : true;
        $fromCell->save();

        $fromCell->events()->sync($toEventIds);
        $toCell->events()->sync($fromEventIds);

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function removeFunction(Request $request)
    {
        $cell = GridCell::find($request->id);

        if ($cell) {
            $cell->destination_type = null;
            $cell->events()->detach();
            $cell->is_available = true;
            $cell->save();
        }

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function assignEvent(Request $request)
    {
        $cell = GridCell::find($request->cell_id);
        $event = Event::with('effects')->find($request->event_id);

        if (!$cell || !$event) {
            return response()->json([
                'success' => false,
                'message' => 'Cell or event not found',
            ], 404);
        }

        $cell->events()->syncWithoutDetaching([
            $event->id => [
                'route_order' => $request->route_order ?? null,
            ],
        ]);

        $cell->is_available = false;
        $cell->save();

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function removeEvent(Request $request)
    {
        $cell = GridCell::with('events')->find($request->cell_id);

        if (!$cell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        if ($request->event_id) {
            $cell->events()->detach($request->event_id);
        } else {
            $cell->events()->detach();
        }

        $cell->load('events');

        if (!$cell->destination_type && $cell->events->isEmpty()) {
            $cell->is_available = true;
            $cell->save();
        }

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function neighborEffects(Request $request)
    {
        $cell = GridCell::find($request->cell_id);

        if (!$cell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        $cellsToCalculate = $this->cellsWithRelations()
            ->where(function ($query) use ($cell) {
                $query->where('x_coordinate', $cell->x_coordinate)
                    ->where('y_coordinate', $cell->y_coordinate);
            })
            ->orWhere(function ($query) use ($cell) {
                $query->where('x_coordinate', $cell->x_coordinate)
                    ->where('y_coordinate', $cell->y_coordinate - 1);
            })
            ->orWhere(function ($query) use ($cell) {
                $query->where('x_coordinate', $cell->x_coordinate)
                    ->where('y_coordinate', $cell->y_coordinate + 1);
            })
            ->orWhere(function ($query) use ($cell) {
                $query->where('x_coordinate', $cell->x_coordinate - 1)
                    ->where('y_coordinate', $cell->y_coordinate);
            })
            ->orWhere(function ($query) use ($cell) {
                $query->where('x_coordinate', $cell->x_coordinate + 1)
                    ->where('y_coordinate', $cell->y_coordinate);
            })
            ->get();

        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cellsToCalculate, $categories);
        $qualityOfLife = array_sum($effectTotals);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
        ]);
    }
}