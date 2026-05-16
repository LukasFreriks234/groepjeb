<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class GridCellController extends Controller
{
    public function index() {
        $cells = GridCell::with('cityFunction')
            ->orderBy('y_coordinate')
            ->orderBy('x_coordinate')
            ->get();

        $functions = Functions::all();
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
        // Zoek de cell waar je met je muis overheen gaat
        $cell = GridCell::find($request->cell_id);

        if (!$cell) {
            return response()->json([
                'success' => false,
                'message' => 'Cell not found',
            ], 404);
        }

        // Zoek alleen de cellen boven, onder, links en rechts
        $neighbors = GridCell::with('cityFunction')
            ->where(function ($query) use ($cell) {
                // boven
                $query->where('x_coordinate', $cell->x_coordinate)
                      ->where('y_coordinate', $cell->y_coordinate - 1);
            })
            ->orWhere(function ($query) use ($cell) {
                // onder
                $query->where('x_coordinate', $cell->x_coordinate)
                      ->where('y_coordinate', $cell->y_coordinate + 1);
            })
            ->orWhere(function ($query) use ($cell) {
                // links
                $query->where('x_coordinate', $cell->x_coordinate - 1)
                      ->where('y_coordinate', $cell->y_coordinate);
            })
            ->orWhere(function ($query) use ($cell) {
                // rechts
                $query->where('x_coordinate', $cell->x_coordinate + 1)
                      ->where('y_coordinate', $cell->y_coordinate);
            })
            ->get();

        $categories = Category::all();

        // Tel de effects op van alleen deze buren
        $effectTotals = Effects::calculateEffectTotals($neighbors, $categories);

        return response()->json([
            'success' => true,
            'effectTotals' => $effectTotals,
        ]);
    }
}