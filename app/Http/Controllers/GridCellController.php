<?php

namespace App\Http\Controllers;

use App\Models\GridDynamic;
use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Models\EventgridCell;
use DateInterval;
use DateTime;
use Barryvdh\DomPDF\Facade\Pdf;

class GridCellController extends Controller
{
    private function cellsWithRelations()
    {
return GridCell::with(['cityFunction', 'mainRoad']);
    }

    private function gridDynamicIdForCell(GridCell $cell)
    {
        $dynamicId = DB::table('grid_dynamics')
            ->where('grid_cell_id', $cell->id)
            ->value('id');

        if ($dynamicId) {
            return $dynamicId;
        }

$existing = DB::table('grid_dynamics')
            ->where('x_coordinate', $cell->x_coordinate)
            ->where('y_coordinate', $cell->y_coordinate)
            ->whereNull('grid_cell_id')
            ->first();

        if ($existing) {
            DB::table('grid_dynamics')
                ->where('id', $existing->id)
                ->update([
                    'grid_cell_id' => $cell->id,
                    'updated_at' => now(),
                ]);

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
            ->get([
                'event_id',
                'grid_dynamics_id',
                'route_order',
                'expires_at',
            ]);

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

        $globalEvents = Event::with('effects')
            ->where('is_global', true)
            ->get();

        $eventGridCells = EventgridCell::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        return view('welcome', compact(
            'cells',
            'functions',
            'events',
            'globalEvents',
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

        $durationInRealSeconds = max(
            1,
            $durationInSimulationMinutes / $simulationMinutesPerRealSecond
        );

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
            ->join(
                'grid_dynamics',
                'event_grid_cells.grid_dynamics_id',
                '=',
                'grid_dynamics.id'
            )
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

            $expiredCellIds = $expired->pluck('grid_cell_id')
                ->filter()
                ->unique();

            GridCell::whereIn('id', $expiredCellIds)
                ->get()
                ->each(function ($cell) {
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

    public function toggleGlobalEvent(Request $request)
    {
        $event = Event::find($request->event_id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        $event->is_global = !$event->is_global;
        $event->save();

        $cells = $this->cellsWithRelations()->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        $globalEvents = Event::with('effects')
            ->where('is_global', true)
            ->get();

        return response()->json([
            'success' => true,
            'is_global' => $event->is_global,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
            'globalEvents' => $globalEvents,
        ]);
    }

    public function checkDayNight(Request $request)
    {
        $simulationMinute = (int) ($request->simulation_minute ?? 0);
        $cycleLength = 24 * 60; // 1440 minutes in a full cycle

        // Day: 6:00 (360 min) to 18:00 (1080 min)
        $isDay = $simulationMinute >= 360 && $simulationMinute < 1080;

        $dayEvent = Event::where('name', 'Day')->first();
        $nightEvent = Event::where('name', 'Night')->first();

        $changed = false;

        if ($isDay) {
            if ($dayEvent && !$dayEvent->is_global) {
                $dayEvent->is_global = true;
                $dayEvent->save();
                $changed = true;
            }
            if ($nightEvent && $nightEvent->is_global) {
                $nightEvent->is_global = false;
                $nightEvent->save();
                $changed = true;
            }
        } else {
            if ($nightEvent && !$nightEvent->is_global) {
                $nightEvent->is_global = true;
                $nightEvent->save();
                $changed = true;
            }
            if ($dayEvent && $dayEvent->is_global) {
                $dayEvent->is_global = false;
                $dayEvent->save();
                $changed = true;
            }
        }

        if ($changed) {
            $cells = $this->cellsWithRelations()->get();
            $categories = Category::all();
            $effectTotals = Effects::calculateEffectTotals($cells, $categories);
            $qualityOfLife = array_sum($effectTotals);

            $globalEvents = Event::with('effects')
                ->where('is_global', true)
                ->get();

            return response()->json([
                'success' => true,
                'changed' => true,
                'is_day' => $isDay,
                'effectTotals' => $effectTotals,
                'qualityOfLife' => $qualityOfLife,
                'globalEvents' => $globalEvents,
            ]);
        }

        return response()->json([
            'success' => true,
            'changed' => false,
            'is_day' => $isDay,
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

        $effectTotals = Effects::calculateEffectTotals(
            $cellsToCalculate,
            $categories
        );

        $qualityOfLife = array_sum($effectTotals);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
        ]);
    }

    public function exportPdf()
    {
        $cells = $this->cellsWithRelations()
            ->orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        $this->attachEventsToCells($cells);

        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = array_sum($effectTotals);

        $pdf = Pdf::loadView('pdf.report', [
            'cells' => $cells,
            'categories' => $categories,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
        ]);

        return $pdf->download('simulatierapport.pdf');
    }

    public function checkRecurring($currentdate)
    {
        $recurring = DB::table('recurrings')->select(
            'frequency',
            'amount'
        )->first();

        $event = DB::table('events')->select(
            'start_date',
            'time'
        )->first();

        if ($recurring && $recurring->frequency === 'daily') {
            $interval = new DateInterval('P' . (int) $recurring->amount . 'D');
            $date = new DateTime($currentdate . ' ' . $event->time);
            $nextdate = $date->add($interval)->format('Y-m-d');
            // $this.dd($nextdate);
            return $nextdate;
        } else if ($recurring && $recurring->frequency === 'weekly') {
            $startdate = $event->start_date;
            $current = $currentdate->dayName;
        } else if ($recurring && $recurring->frequency === 'monthly') {
            // monthly
        } else if ($recurring && $recurring->frequency === 'yearly') {
            // yearly
        } else {
            return "error";
        }
    }


    // function dd()
    // {
    //     echo '<pre>';
    //     array_map(function ($x) {
    //         var_dump($x); }, func_get_args());
    //     die;
    // }

    public function toggle(Request $request)
    {
        if ($request->active) {

            $gridDynamic = GridDynamic::create([
                'x_coordinate' => 0,
                'y_coordinate' => 0,
                'is_available' => 1,
                'grid_cell_id' => 1,
            ]);

            EventgridCell::create([
                'event_id' => $request->event_id,
                'grid_dynamics_id' => $gridDynamic->id,
                'route_order' => 1,
            ]);

            $nextDate = $this->checkRecurring($request->date);

            // GridCell::where('grid_cell_id', $gridDynamic->id)->update(['is_available' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Toggled'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Deactivated / no action taken'
        ]);
    }

    function untoggle(Request $request)
    {

        EventgridCell::where('event_id', $request->event_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Untoggled'
        ]);
    }
}
