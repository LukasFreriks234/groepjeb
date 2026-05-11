<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Functions;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index()
    {
        $functions = Functions::all();
        $categories = Category::all();

        return view('overview', compact('functions', 'categories'));
    }
}
