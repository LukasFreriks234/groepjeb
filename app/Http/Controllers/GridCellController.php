<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Models\EventgridCell;

class GridCellController extends Controller
{
    private function cellsWithRelations()
    {
        return GridCell::with(['cityFunction']);
    }

    private function gridDynamicIdForCell(GridCell $cell)
    {
        $dynamicId = DB::table('grid_dynamics')
            ->where('grid_cell_id', $cell->id)
            ->value('id');

        if ($dynamicId) {
            return $dynamicId;
        }

        // Fallback: misschien bestaat de rij al op coördinaten, maar zonder koppeling
        $existing = DB::table('grid_dynamics')
            ->where('x_coordinate', $cell->x_coordinate)
            ->where('y_coordinate', $cell->y_coordinate)
            ->whereNull('grid_cell_id')
            ->first();

        if ($existing) {
            DB::table('grid_dynamics')
                ->where('id', $existing->id)
                ->update(['grid_cell_id' => $cell->id, 'updated_at' => now()]);

            return $existing->id;
        }

        return DB::table('grid_dynamics')->insertGetId([
            'x_coordinate' => $cell->x_coordinate,
            'y_coordinate' => $cell->y_coordinate,
            'is_available' => $cell->is_available,
            'grid_cell_id' => $cell->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function attachEventsToCells($cells)
    {
        if ($cells->isEmpty()) {
            return $cells;
        }

        $cellIds = $cells->pluck('id')->values();

        $dynamicRows = DB::table('grid_dynamics')
            ->whereIn('grid_cell_id', $cellIds)
            ->get(['id', 'grid_cell_id']);

        if ($dynamicRows->isEmpty()) {
            $cells->each(function ($cell) {
                $cell->setRelation('events', collect());
            });

            return $cells;
        }

        $dynamicToCell = $dynamicRows->pluck('grid_cell_id', 'id');

        $eventRows = DB::table('event_grid_cells')
            ->whereIn('grid_dynamics_id', $dynamicRows->pluck('id'))
            ->get(['event_id', 'grid_dynamics_id', 'route_order', 'expires_at']);

        if ($eventRows->isEmpty()) {
            $cells->each(function ($cell) {
                $cell->setRelation('events', collect());
            });

            return $cells;
        }

        $events = Event::with('effects')
            ->whereIn('id', $eventRows->pluck('event_id')->unique())
            ->get()
            ->keyBy('id');

        $eventsByCellId = [];

        foreach ($eventRows as $eventRow) {
            $cellId = $dynamicToCell[$eventRow->grid_dynamics_id] ?? null;
            $event = $events->get($eventRow->event_id);

            if (!$cellId || !$event) {
                continue;
            }

            if (!array_key_exists($cellId, $eventsByCellId)) {
                $eventsByCellId[$cellId] = collect();
            }

            $eventsByCellId[$cellId]->push($event);
        }

        $cells->each(function ($cell) use ($eventsByCellId) {
            $events = $eventsByCellId[$cell->id] ?? collect();

            $cell->setRelation(
                'events',
                $events->unique('id')->values()
            );
        });

        return $cells;
    }

    private function cellHasEvents(GridCell $cell)
    {
        $dynamicId = $this->gridDynamicIdForCell($cell);

        return DB::table('event_grid_cells')
            ->where('grid_dynamics_id', $dynamicId)
            ->exists();
    }

    private function updateCellAvailability(GridCell $cell)
    {
        $cell->is_available = !$cell->destination_type && !$this->cellHasEvents($cell);
        $cell->save();
    }

    private function calculateTotals()
    {
        $cells = $this->cellsWithRelations()->get();
        $this->attachEventsToCells($cells);

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

        $this->attachEventsToCells($cells);

        $functions = Functions::with('effects')->get();
        $categories = Category::all();

        $events = Event::with([
            'effects',
            'recurring.weekly',
            'recurring.monthly',
        ])->get();

        $eventGridCells = EventgridCell::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        return view('welcome', compact(
            'cells',
            'functions',
            'events',
            'categories',
            'effectTotals',
            'qualityOfLife',
            'eventGridCells'
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
        $fromCell = GridCell::find($request->from_cell_id);
        $toCell = GridCell::find($request->to_cell_id);

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

        $toCell->destination_type = $fromFunctionId;
        $toCell->save();

        $fromCell->destination_type = $toFunctionId;
        $fromCell->save();

        $this->updateCellAvailability($fromCell);
        $this->updateCellAvailability($toCell);

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function removeFunction(Request $request)
    {
        $cell = GridCell::find($request->id ?? $request->cell_id);

        if ($cell) {
            $dynamicId = $this->gridDynamicIdForCell($cell);

            DB::table('event_grid_cells')
                ->where('grid_dynamics_id', $dynamicId)
                ->delete();

            $cell->destination_type = null;
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

        $dynamicId = $this->gridDynamicIdForCell($cell);

        $simulationMinutesPerRealSecond = 24;
        $eventLength = (int) ($event->length ?: 1);

        $durationInSimulationMinutes = match ($event->length_unit) {
            'hours' => $eventLength * 60,
            'days' => $eventLength * 60 * 24,
            'weeks' => $eventLength * 60 * 24 * 7,
            default => $eventLength * 60,
        };

        $durationInRealSeconds = max(1, $durationInSimulationMinutes / $simulationMinutesPerRealSecond);
        $expiresAt = now()->addSeconds($durationInRealSeconds);

        $existingRow = DB::table('event_grid_cells')
            ->where('event_id', $event->id)
            ->where('grid_dynamics_id', $dynamicId)
            ->first();

        $payload = [
            'route_order' => $request->route_order ?? 1,
            'expires_at' => $expiresAt,
            'updated_at' => now(),
        ];

        if ($existingRow) {
            DB::table('event_grid_cells')
                ->where('id', $existingRow->id)
                ->update($payload);
        } else {
            DB::table('event_grid_cells')->insert(array_merge($payload, [
                'event_id' => $event->id,
                'grid_dynamics_id' => $dynamicId,
                'created_at' => now(),
            ]));
        }

        $cell->is_available = false;
        $cell->save();

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function checkExpiredEvents(Request $request)
    {
        $expired = DB::table('event_grid_cells')
            ->join('grid_dynamics', 'event_grid_cells.grid_dynamics_id', '=', 'grid_dynamics.id')
            ->whereNotNull('event_grid_cells.expires_at')
            ->where('event_grid_cells.expires_at', '<=', now())
            ->select(
                'event_grid_cells.id',
                'event_grid_cells.event_id',
                'event_grid_cells.grid_dynamics_id',
                'grid_dynamics.grid_cell_id'
            )
            ->get();

        if ($expired->isNotEmpty()) {
            DB::table('event_grid_cells')
                ->whereIn('id', $expired->pluck('id'))
                ->delete();

            $expiredCellIds = $expired->pluck('grid_cell_id')->filter()->unique();

            GridCell::whereIn('id', $expiredCellIds)->get()->each(function ($cell) {
                $this->updateCellAvailability($cell);
            });
        }

        $totals = $this->calculateTotals();

        return response()->json([
            'success' => true,
            'expiredEvents' => $expired,
            'effectTotals' => $totals['effectTotals'],
            'qualityOfLife' => $totals['qualityOfLife'],
        ]);
    }

    public function removeEvent(Request $request)
    {
        $cell = GridCell::find($request->cell_id);

        if (!$cell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        $dynamicId = $this->gridDynamicIdForCell($cell);

        $query = DB::table('event_grid_cells')
            ->where('grid_dynamics_id', $dynamicId);

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        $query->delete();

        $this->updateCellAvailability($cell);

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

        $this->attachEventsToCells($cellsToCalculate);

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
