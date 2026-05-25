<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;
use App\Models\Category;
use App\Models\Effects;

class FunctionController extends Controller
{

    public function index()
    {
        $functions = Functions::all();
        $categories = Category::all();

        return view('Functions.index', compact('functions', 'categories'));
    }

    public function show($id)
    {
        $function = Functions::with('Effects')->findorFail($id);
        $effect = Effects::findOrFail($id);
        return view('Functions.show', [
            'function' => $function,
            'effect' => $effect
        ]);
    }
}
