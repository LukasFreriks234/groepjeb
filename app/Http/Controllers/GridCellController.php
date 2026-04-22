<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;
use App\Models\Functions;
use App\Models\Category;

class GridCellController extends Controller
{
    public function index() {
        $cells = GridCell::orderBy('y_coordinate')->orderBy('x_coordinate')->get();
        $functions = Functions::all();
        $categories = Category::all();

        return view('welcome', compact('cells', 'functions', 'categories'));
    }

    public function removeFunction(Request $request)
    {
        $cell = GridCell::find($request->id);

        if ($cell) {
            $cell->destination_type = null;
            $cell->is_available = true;
            $cell->save();
        }

        return response()->json(['success' => true]);
    }
}