<?php

namespace App\Http\Controllers;

use App\Models\GridCell;
use App\Models\SavedGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SavedGridController extends Controller
{
    public function index()
    {
        $savedGrids = SavedGrid::query()
            ->selectRaw(
                "name, created_at, COUNT(*) as item_count, SUM(CASE WHEN item_type = 'function' THEN 1 ELSE 0 END) as function_count, SUM(CASE WHEN item_type = 'event' THEN 1 ELSE 0 END) as event_count"
            )
            ->groupBy('name', 'created_at')
            ->orderByDesc('created_at')
            ->orderBy('name')
            ->get()
            ->map(function ($savedGrid) {
                $savedGrid->saved_at = Carbon::parse($savedGrid->created_at)->format('Y-m-d H:i');
                $savedGrid->display_name = $savedGrid->name ?? 'Unnamed grid';

                return $savedGrid;
            });

        return view('SavedGrids.index', compact('savedGrids'));
    }

    public function load(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'created_at' => ['required', 'date'],
        ]);

        $snapshotRows = SavedGrid::query()
            ->where('name', $validated['name'])
            ->where('created_at', Carbon::parse($validated['created_at']))
            ->orderBy('id')
            ->get();

        if ($snapshotRows->isEmpty()) {
            return back()->withErrors([
                'name' => 'Saved grid not found.',
            ]);
        }

        DB::transaction(function () use ($snapshotRows) {
            GridCell::query()->update([
                'destination_type' => null,
                'is_available' => true,
            ]);

            DB::table('event_grid_cells')->delete();

            foreach ($snapshotRows as $row) {
                if ($row->item_type === 'function' && $row->function_id) {
                    GridCell::query()
                        ->whereKey($row->grid_cell_id)
                        ->update([
                            'destination_type' => $row->function_id,
                            'is_available' => false,
                        ]);

                    continue;
                }

                if ($row->item_type === 'event' && $row->event_id) {
                    DB::table('event_grid_cells')->insert([
                        'event_id' => $row->event_id,
                        'grid_dynamics_id' => $row->grid_cell_id,
                        'route_order' => $row->route_order ?? 0,
                        'expires_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    GridCell::query()
                        ->whereKey($row->grid_cell_id)
                        ->update([
                            'is_available' => false,
                        ]);
                }
            }
        });

        return redirect('/grid')->with('status', sprintf('Loaded grid "%s".', $validated['name']));
    }
}