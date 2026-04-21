<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\functions;
use Illuminate\Http\Request;
use App\Models\GridCell;

class GridCellController extends Controller
{
    public function index() {
        $cells = GridCell::orderBy('y_coordinate')->orderBy('x_coordinate')->get();
        $functions = functions::all();
        $categories = categories::all();
        return view('welcome', compact('cells', 'functions', 'categories'));
    }
}
