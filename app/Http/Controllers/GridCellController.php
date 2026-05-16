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
}