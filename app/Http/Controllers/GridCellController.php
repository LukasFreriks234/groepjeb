<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class GridCellController extends Controller
{
    public function index()
    {
        $cells = GridCell::with('cityFunction')
            ->orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        $functions = Functions::with('effects')->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = Effects::calculateQualityOfLife($cells, $categories);

        return view('welcome', compact(
            'cells',
            'functions',
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

        $cells = GridCell::with('cityFunction')->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = Effects::calculateQualityOfLife($cells, $categories);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
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
        $toCell->is_available = false;
        $toCell->save();

        $fromCell->destination_type = $toFunctionId;
        $fromCell->is_available = $toFunctionId ? false : true;
        $fromCell->save();

        $cells = GridCell::with('cityFunction')->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = Effects::calculateQualityOfLife($cells, $categories);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
        ]);
    }

    public function removeFunction(Request $request)
    {
        $cell = GridCell::find($request->id);

        if ($cell) {
            $cell->destination_type = null;
            $cell->is_available = true;
            $cell->save();
        }

        $cells = GridCell::with('cityFunction')->get();
        $categories = Category::all();

        $effectTotals = Effects::calculateEffectTotals($cells, $categories);
        $qualityOfLife = Effects::calculateQualityOfLife($cells, $categories);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
            'qualityOfLife' => $qualityOfLife,
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

        $cellsToCalculate = GridCell::with('cityFunction')
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

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
        ]);
    }
}