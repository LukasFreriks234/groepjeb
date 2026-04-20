<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GridCell;

class GridCellController extends Controller
{
    public function index() {
        $cells = GridCell::orderBy('y_coordinate')->orderBy('x_coordinate')->get();
        return view('/', compact('cells'));
    }
}
